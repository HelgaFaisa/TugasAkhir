<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SantriAuthController extends Controller
{
    public function login()
    {
        if (Auth::guard('santri')->check()) {
            return redirect()->route('santri.dashboard');
        }

        return view('santri.auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $request->session()->forget(['login_attempts']);

        if (Auth::guard('santri')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Gunakan DB::table langsung — hindari masalah model cast/mutator
            $account = Auth::guard('santri')->user();

            DB::table('santri_accounts')
                ->where('id', $account->id)
                ->update(['last_login' => now()]);

            $nama = $account->santri
                ? $account->santri->nama_lengkap
                : $account->username;

            return redirect()->route('santri.dashboard')
                ->with('success', 'Selamat datang, ' . $nama . '!');
        }

        $attempts = $request->session()->get('login_attempts', 0) + 1;
        $request->session()->put('login_attempts', $attempts);

        if ($attempts >= 3) {
            $request->session()->flush();
            $request->session()->regenerate();

            return redirect()->back()->withErrors([
                'username' => 'Terlalu banyak percobaan. Session direset, silakan coba lagi.',
            ])->withInput($request->except('password'));
        }

        throw ValidationException::withMessages([
            'username' => 'Login gagal (Percobaan ke-' . $attempts . '/3). Username atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('santri')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('santri.login')
            ->with('success', 'Berhasil logout.');
    }
}