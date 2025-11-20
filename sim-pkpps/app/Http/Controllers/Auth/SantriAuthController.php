<?php
// app/Http/Controllers/Auth/SantriAuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SantriAuthController extends Controller
{
    /**
     * Tampilkan halaman login santri/wali
     */
    public function login()
    {
        return view('santri.auth.login');
    }

    /**
     * Proses login santri/wali dengan auto-clear session on failed
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // ✅ TAMBAHAN 1: Clear old session data
        $request->session()->forget(['login_attempts', 'last_attempt_time']);

        // Coba login dengan guard default
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Cek apakah user adalah santri atau wali
            if ($user->role === 'santri' || $user->role === 'wali') {
                // ✅ TAMBAHAN 2: Regenerate & clear
                $request->session()->regenerate();
                $request->session()->forget(['login_attempts', 'last_attempt_time']);
                
                return redirect()->intended(route('santri.dashboard'))
                    ->with('success', 'Selamat datang, ' . $user->name . '!');
            }
            
            // ✅ TAMBAHAN 3: Role tidak sesuai - clear session
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerate();
            
            return redirect()->back()->withErrors([
                'username' => 'Akun Anda tidak memiliki akses ke halaman ini. Gunakan login Admin jika Anda admin.'
            ])->withInput($request->except('password'));
        }

        // ✅ TAMBAHAN 4: Track & auto-flush
        $attempts = $request->session()->get('login_attempts', 0) + 1;
        $request->session()->put('login_attempts', $attempts);
        $request->session()->put('last_attempt_time', now());

        if ($attempts >= 3) {
            $request->session()->flush();
            $request->session()->regenerate();
            
            return redirect()->back()->withErrors([
                'username' => 'Terlalu banyak percobaan login gagal. Session telah direset. Silakan coba lagi.'
            ])->withInput($request->except('password'));
        }

        throw ValidationException::withMessages([
            'username' => "Login gagal (Percobaan ke-{$attempts}/3). Username/Password salah atau akun tidak terdaftar.",
        ]);
    }

    /**
     * Logout santri/wali
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('santri.login')
            ->with('success', 'Anda berhasil logout.');
    }
}