<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('orders')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%")
                  ->orWhere('referral_code', 'like', "%$s%");
            });
        }

        if ($request->filled('level')) {
            $query->where('member_level', $request->level);
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $customer->load('orders');
        $walletHistory = WalletTransaction::where('customer_id', $customer->id)
                                          ->latest()->take(10)->get();
        $referrals = Customer::where('referred_by', $customer->referral_code)->get();

        return view('customers.show', compact('customer', 'walletHistory', 'referrals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'required|string|max:20|unique:customers,phone',
            'email'   => 'nullable|email',
            'address' => 'nullable|string|max:255',
        ]);

        $validated['referral_code'] = Customer::generateReferralCode($validated['name']);

        Customer::create($validated);

        return back()->with('success', 'Pelanggan berhasil ditambahkan!');
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'email'   => 'nullable|email',
            'address' => 'nullable|string|max:255',
        ]);

        $customer->update($validated);

        return back()->with('success', 'Data pelanggan diperbarui!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Pelanggan dihapus.');
    }

    /**
     * Tambah saldo wallet pelanggan (dilakukan oleh admin setelah konfirmasi transfer).
     */
    public function topup(Request $request, Customer $customer)
    {
        $request->validate([
            'amount'      => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
        ], [
            'amount.required' => 'Nominal topup wajib diisi.',
            'amount.min'      => 'Minimal topup Rp 1.000.',
        ]);

        $amount      = (float) $request->amount;
        $description = trim($request->description) ?: 'Topup saldo oleh admin';

        WalletTransaction::create([
            'customer_id'    => $customer->id,
            'type'           => 'credit',
            'amount'         => $amount,
            'balance_before' => $customer->wallet_balance,
            'balance_after'  => $customer->wallet_balance + $amount,
            'description'    => $description,
            'order_id'       => null,
        ]);

        $customer->increment('wallet_balance', $amount);

        return back()->with(
            'topup_success',
            'Saldo Rp ' . number_format($amount, 0, ',', '.') . ' berhasil ditambahkan ke ' . $customer->name . '.'
        );
    }

    /**
     * Pencarian pelanggan via AJAX.
     * Mendukung: nama, nomor HP (penuh atau 4 digit terakhir), kode referral.
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $customers = Customer::where(function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%")
                  ->orWhere('referral_code', 'like', "%{$q}%");

            // Jika input hanya angka (misal: 4 digit terakhir HP), cari juga yang berakhiran angka tersebut
            if (ctype_digit($q)) {
                $query->orWhere('phone', 'like', "%{$q}");
            }
        })
        ->take(10)
        ->get(['id', 'name', 'phone', 'referral_code', 'member_level', 'wallet_balance']);

        return response()->json($customers);
    }
}
