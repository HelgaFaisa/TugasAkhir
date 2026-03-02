<?php
// app/Http/Middleware/CheckSantriAuth.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSantriAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('santri')->check()) {
            // Jika request AJAX/API, return JSON
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('santri.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $account = Auth::guard('santri')->user();

        // PERBAIKAN: pastikan akun masih valid dan punya id_santri
        if (!$account || !$account->id_santri) {
            Auth::guard('santri')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('santri.login')
                ->with('error', 'Akun tidak valid. Silakan login ulang.');
        }

        return $next($request);
    }
}