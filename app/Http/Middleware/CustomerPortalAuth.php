<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerPortalAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('portal_customer_id')) {
            return redirect()->route('portal.login')
                             ->with('info', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
