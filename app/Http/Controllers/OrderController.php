<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('customer')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('order_number', 'like', "%$s%")
                  ->orWhere('customer_name', 'like', "%$s%")
                  ->orWhere('customer_phone', 'like', "%$s%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $services  = Service::where('is_active', true)->orderBy('sort_order')->get()->groupBy('category');
        $staffList = Staff::where('is_active', true)->orderBy('name')->get();
        return view('orders.create', compact('services', 'staffList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'       => 'required|string|max:100',
            'customer_phone'      => 'required|string|max:20',
            'customer_id'         => 'nullable|exists:customers,id',
            'category'            => 'required|in:kiloan,satuan,ongkir',
            'service_type'        => 'nullable|string',
            'weight'              => 'nullable|numeric|min:0',
            'satuan_quantities'   => 'nullable|array',
            'satuan_quantities.*' => 'nullable|integer|min:0|max:99',
            'ongkir_id'           => 'nullable|exists:services,id',
            'perfume'             => 'nullable|string',
            'speed'               => 'required|in:reguler,kilat,ekspres',
            'discount'            => 'nullable|numeric|min:0',
            'payment_status'      => 'required|in:lunas,belum_lunas',
            'payment_method'      => 'nullable|in:cash,transfer,qris,wallet',
            'notes'               => 'nullable|string|max:500',
            'referral_code'       => 'nullable|string',
            'ironing_staff_id'    => 'nullable|exists:staff,id',
        ]);

        $createdOrder = null;
        DB::transaction(function () use ($validated, $request, &$createdOrder) {
            // Find or create customer
            $customer = null;
            if (!empty($validated['customer_id'])) {
                $customer = Customer::find($validated['customer_id']);
            } else {
                $customer = Customer::firstOrCreate(
                    ['phone' => $validated['customer_phone']],
                    [
                        'name'          => $validated['customer_name'],
                        'referral_code' => Customer::generateReferralCode($validated['customer_name']),
                        'referred_by'   => $validated['referral_code'] ?? null,
                    ]
                );
            }

            // Calculate pricing
            $basePrice = $this->calcBasePrice($validated);
            $surcharge = $basePrice * Order::$speedRates[$validated['speed']];
            $discount  = (float)($validated['discount'] ?? 0);
            $total     = max(0, $basePrice + $surcharge - $discount);

            // Ironing fee untuk staff setrika
            $ironingFee = null;
            if (!empty($validated['ironing_staff_id'])) {
                $ironingStaff = Staff::find($validated['ironing_staff_id']);
                if ($ironingStaff && ($validated['weight'] ?? 0) > 0) {
                    $ironingFee = (float) $ironingStaff->fee_per_kg * ($validated['weight'] ?? 0);
                }
            }

            // Estimated done date
            $estimatedDone = match ($validated['speed']) {
                'kilat'   => today()->addDay(),
                'ekspres' => today(),
                default   => today()->addDays(3),
            };

            $order = Order::create([
                'order_number'    => Order::generateOrderNumber(),
                'customer_id'     => $customer?->id,
                'customer_name'   => $validated['customer_name'],
                'customer_phone'  => $validated['customer_phone'],
                'category'        => $validated['category'],
                'service_type'    => $validated['service_type'] ?? null,
                'weight'          => $validated['weight'] ?? null,
                'perfume'         => $validated['perfume'] ?? null,
                'speed'           => $validated['speed'],
                'estimated_done'  => $estimatedDone,
                'subtotal'        => $basePrice,
                'speed_surcharge' => $surcharge,
                'discount'        => $discount,
                'total'           => $total,
                'payment_status'  => $validated['payment_status'],
                'payment_method'  => $validated['payment_method'] ?? null,
                'paid_at'         => $validated['payment_status'] === 'lunas' ? now() : null,
                'status'           => 'antri',
                'notes'            => $validated['notes'] ?? null,
                'created_by'       => 'Staff',
                'ironing_staff_id' => $validated['ironing_staff_id'] ?? null,
                'ironing_fee'      => $ironingFee,
            ]);
            $createdOrder = $order;

            // Update customer stats
            if ($customer) {
                $customer->increment('total_orders');
                if ($validated['category'] === 'kiloan' && !empty($validated['weight'])) {
                    $customer->increment('total_weight', $validated['weight']);
                }
                $this->updateMemberLevel($customer);

                // Referral commission (10%)
                if ($customer->referred_by && $validated['payment_status'] === 'lunas') {
                    $referrer = Customer::where('referral_code', $customer->referred_by)->first();
                    if ($referrer) {
                        $rate       = (float) Setting::get('referral_commission', 10) / 100;
                        $commission = $total * $rate;
                        WalletTransaction::create([
                            'customer_id'    => $referrer->id,
                            'type'           => 'credit',
                            'amount'         => $commission,
                            'balance_before' => $referrer->wallet_balance,
                            'balance_after'  => $referrer->wallet_balance + $commission,
                            'description'    => "Komisi referral dari {$customer->name}",
                            'order_id'       => $order->id,
                        ]);
                        $referrer->increment('wallet_balance', $commission);
                        // Only give commission once per customer
                        $customer->update(['referred_by' => null]);
                    }
                }
            }

            // Deduct wallet balance jika bayar via wallet
            if (($validated['payment_method'] ?? '') === 'wallet'
                && $validated['payment_status'] === 'lunas'
                && $customer) {
                $customer->refresh();
                if ($customer->wallet_balance < $total) {
                    throw new \Exception('Saldo wallet tidak mencukupi.');
                }
                WalletTransaction::create([
                    'customer_id'    => $customer->id,
                    'type'           => 'debit',
                    'amount'         => $total,
                    'balance_before' => $customer->wallet_balance,
                    'balance_after'  => $customer->wallet_balance - $total,
                    'description'    => "Pembayaran pesanan {$order->order_number}",
                    'order_id'       => $order->id,
                ]);
                $customer->decrement('wallet_balance', $total);
            }
        });

        return redirect()->route('orders.show', $createdOrder)
                         ->with('new_order', true);
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items']);
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:antri,dicuci,dijemur,disetrika,siap_ambil,selesai,dibatalkan']);
        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan diperbarui!');
    }

    public function markPaid(Request $request, Order $order)
    {
        $request->validate(['payment_method' => 'required|in:cash,transfer,qris,wallet']);
        $order->update([
            'payment_status' => 'lunas',
            'payment_method' => $request->payment_method,
            'paid_at'        => now(),
        ]);

        return back()->with('success', 'Pembayaran berhasil dicatat!');
    }

    private function calcBasePrice(array $data): float
    {
        if ($data['category'] === 'kiloan') {
            $prices = [
                'cuci_setrika' => 7000,
                'cuci_kering'  => 5000,
                'setrika_saja' => 4000,
            ];
            $pricePerKg = $prices[$data['service_type']] ?? 7000;
            return ($data['weight'] ?? 0) * $pricePerKg;
        }

        if ($data['category'] === 'satuan' && !empty($data['satuan_quantities'])) {
            $total = 0;
            foreach ($data['satuan_quantities'] as $itemId => $qty) {
                $qty = (int) $qty;
                if ($qty <= 0) continue;
                $svc = Service::find($itemId);
                if ($svc) $total += $svc->price * $qty;
            }
            return $total;
        }

        if ($data['category'] === 'ongkir' && !empty($data['ongkir_id'])) {
            $svc = Service::find($data['ongkir_id']);
            return $svc ? (float) $svc->price : 0;
        }

        return 0;
    }

    private function updateMemberLevel(Customer $customer): void
    {
        $level = match (true) {
            $customer->total_orders >= 30 => 'Gold',
            $customer->total_orders >= 10 => 'Silver',
            default                        => 'Bronze',
        };
        $customer->update(['member_level' => $level]);
    }

    public function searchCustomer(Request $request)
    {
        $q = $request->get('q', '');
        $customers = Customer::where('name', 'like', "%$q%")
                             ->orWhere('phone', 'like', "%$q%")
                             ->orWhere('referral_code', 'like', "%$q%")
                             ->take(10)->get(['id', 'name', 'phone', 'referral_code', 'member_level', 'wallet_balance']);

        return response()->json($customers);
    }
}
