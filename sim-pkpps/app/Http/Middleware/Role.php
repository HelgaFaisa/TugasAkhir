<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        // 1. Cek apakah pengguna sudah login
        if (!Auth::check()) {
            // Clear session jika belum login tapi masih ada session
            $request->session()->flush();
            $request->session()->regenerate();
            
            return redirect('/admin/login');
        }

        // Ambil role pengguna saat ini
        $currentRole = Auth::user()->role;

        // Pisahkan daftar role yang diizinkan
        $allowedRoles = explode(',', $roles);

        // 2. Cek apakah role pengguna termasuk dalam daftar yang diizinkan
        if (!in_array($currentRole, $allowedRoles)) {
            // ✅ TAMBAHAN: Redirect ke dashboard yang sesuai, jangan abort
            if ($currentRole === 'admin') {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
            
            if ($currentRole === 'santri' || $currentRole === 'wali') {
                return redirect()->route('santri.dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
            
            // Jika role tidak dikenali, logout paksa
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerate();
            
            return redirect('/admin/login')
                ->with('error', 'Role tidak valid. Silakan login kembali.');
        }

        return $next($request);
    }
}