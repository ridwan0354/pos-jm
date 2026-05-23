<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Http\Request;

class CustomerPortalController extends Controller
{
    private function shopMeta(): array
    {
        return [
            'shopName' => Setting::get('shop_name', 'LinenFlow POS'),
            'shopLogo' => Setting::get('shop_logo', ''),
        ];
    }

    public function showLogin()
    {
        if (session('portal_customer_id')) {
            return redirect()->route('portal.dashboard');
        }
        return view('portal.login', $this->shopMeta());
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ], [
            'phone.required' => 'Nomor HP wajib diisi.',
        ]);

        $raw      = preg_replace('/[^0-9]/', '', $request->phone);
        $customer = Customer::where('phone', $request->phone)
                            ->orWhere('phone', 'like', "%{$raw}")
                            ->first();

        if (! $customer) {
            return back()
                ->withErrors(['phone' => 'Nomor HP tidak terdaftar. Hubungi kasir untuk mendaftar.'])
                ->withInput();
        }

        session(['portal_customer_id' => $customer->id]);
        return redirect()->route('portal.dashboard');
    }

    public function logout()
    {
        session()->forget('portal_customer_id');
        return redirect()->route('portal.login')->with('success', 'Berhasil keluar.');
    }

    public function dashboard()
    {
        $customer = Customer::with([
            'orders'             => fn ($q) => $q->latest(),
            'walletTransactions' => fn ($q) => $q->latest()->take(20),
        ])->findOrFail(session('portal_customer_id'));

        $activeOrders  = $customer->orders->whereNotIn('status', ['selesai', 'dibatalkan'])->values();
        $historyOrders = $customer->orders->whereIn('status', ['selesai', 'dibatalkan'])->values();
        $adminPhone    = Setting::get('admin_phone', '');

        return view('portal.dashboard', array_merge($this->shopMeta(), compact(
            'customer', 'activeOrders', 'historyOrders', 'adminPhone'
        )));
    }

    public function requestTopup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000|max:10000000',
        ], [
            'amount.required' => 'Nominal topup wajib diisi.',
            'amount.min'      => 'Minimal topup Rp 10.000.',
            'amount.max'      => 'Maksimal topup Rp 10.000.000.',
        ]);

        $customer   = Customer::findOrFail(session('portal_customer_id'));
        $adminPhone = Setting::get('admin_phone', '');

        if (! $adminPhone) {
            return back()->withErrors(['amount' => 'Nomor admin belum dikonfigurasi. Hubungi kasir.']);
        }

        $amount  = number_format($request->amount, 0, ',', '.');
        $message = urlencode(
            "Halo Admin 👋\n\n" .
            "Saya ingin melakukan *topup saldo* laundry:\n" .
            "👤 Nama    : {$customer->name}\n" .
            "📱 HP      : {$customer->phone}\n" .
            "💰 Nominal : Rp {$amount}\n\n" .
            "Mohon konfirmasinya. Terima kasih! 🙏"
        );

        return redirect("https://wa.me/{$adminPhone}?text={$message}");
    }
}
