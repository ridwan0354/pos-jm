<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtectAdminPages
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('admin_verified')) {
            // Simpan URL asal agar bisa di-redirect kembali setelah sukses verifikasi
            session()->put('url.intended', $request->fullUrl());
            return redirect()->route('admin.verify');
        }

        return $next($request);
    }
}
