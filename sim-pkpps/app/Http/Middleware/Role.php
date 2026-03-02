<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     * Menerima daftar role yang diizinkan sebagai parameter middleware.
     * Contoh: role:super_admin,akademik,pamong
     *
     * Laravel memecah parameter setelah ':' menjadi argumen terpisah per koma,
     * sehingga kita harus gunakan variadic (...$roles) bukan string tunggal.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // -- Cek apakah pengguna sudah login --
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // -- Cek apakah role pengguna termasuk dalam daftar yang diizinkan --
        if (!in_array(Auth::user()->role, $roles)) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}   