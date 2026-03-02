<?php
// app/Http/Controllers/Auth/AdminAuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;

class AdminAuthController extends Controller
{
    /**
     * Tampilkan halaman login admin
     */
    public function login()
    {
        return view('admin.auth.login');
    }

    /**
     * Proses login admin dengan auto-clear session on failed
     */
    public function authenticate(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // -- Coba login dengan username --
        if (Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {

            $user = Auth::user();
            $adminRoles = ['super_admin', 'akademik', 'pamong'];

            // -- Pastikan hanya role admin yang bisa login via form admin --
            if (!in_array($user->role, $adminRoles)) {
                Auth::logout();
                $request->session()->invalidate();
                throw ValidationException::withMessages([
                    'username' => 'Akun ini bukan akun admin.',
                ]);
            }

            // -- Regenerate session untuk keamanan --
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        // Track failed attempts
        $attempts = $request->session()->get('login_attempts', 0) + 1;
        $request->session()->put('login_attempts', $attempts);

        // Auto-flush setelah 3x gagal
        if ($attempts >= 3) {
            $request->session()->flush();
            $request->session()->regenerate();
            
            return redirect()->back()->withErrors([
                'username' => 'Terlalu banyak percobaan login gagal. Session telah direset. Silakan coba lagi.'
            ])->withInput($request->except('password'));
        }

        throw ValidationException::withMessages([
            'username' => "Login gagal (Percobaan ke-{$attempts}/3). Username/Password salah atau bukan akun Admin.",
        ]);
    }

    /**
     * Logout admin
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')
            ->with('success', 'Anda berhasil logout.');
    }
    
    /**
     * Tampilkan halaman register admin
     */
    public function register()
    {
        return view('admin.auth.register');
    }

    /**
     * Proses register admin baru
     */
    public function storeRegister(Request $request)
    {
        // Validasi hanya Email, Password, dan Konfirmasi Password
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.unique' => 'Email ini sudah terdaftar sebagai Admin.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.'
        ]);

        // Gunakan email sebagai username dan berikan nama default
        $user = User::create([
            'name' => 'Administrator', 
            'email' => $request->email,
            'username' => $request->email,
            'role' => 'super_admin',
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Akun admin berhasil dibuat!');
    }
}