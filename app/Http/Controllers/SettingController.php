<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'shop_name'           => Setting::get('shop_name', 'LinenFlow POS'),
            'admin_phone'         => Setting::get('admin_phone', ''),
            'shop_logo'           => Setting::get('shop_logo', ''),
            'referral_commission' => Setting::get('referral_commission', '10'),
        ];
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'shop_name'           => 'required|string|max:100',
            'admin_phone'         => 'required|string|max:20',
            'shop_logo'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'referral_commission' => 'required|numeric|min:0|max:100',
        ], [
            'shop_name.required'           => 'Nama toko wajib diisi.',
            'admin_phone.required'         => 'Nomor WA admin wajib diisi.',
            'shop_logo.image'              => 'File harus berupa gambar.',
            'shop_logo.max'                => 'Ukuran gambar maksimal 2MB.',
            'referral_commission.required' => 'Komisi referral wajib diisi.',
            'referral_commission.numeric'  => 'Komisi referral harus berupa angka.',
            'referral_commission.max'      => 'Komisi referral maksimal 100%.',
        ]);

        Setting::set('shop_name', $request->shop_name);

        // Normalisasi nomor HP ke format 628xxx
        $phone = preg_replace('/[^0-9]/', '', $request->admin_phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        Setting::set('admin_phone', $phone);
        Setting::set('referral_commission', $request->referral_commission);

        if ($request->hasFile('shop_logo')) {
            $old = Setting::get('shop_logo');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('shop_logo')->store('logos', 'public');
            Setting::set('shop_logo', $path);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }

    public function showVerifyForm()
    {
        return view('admin.verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if ($request->password === 'luzha1420') {
            session(['admin_verified' => true]);
            $intended = session()->pull('url.intended', route('dashboard'));
            return redirect($intended)->with('success', 'Verifikasi berhasil!');
        }

        return back()->withErrors(['password' => 'Password salah!']);
    }
}
