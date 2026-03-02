<?php
// app/Http/Controllers/Auth/AdminForgotPasswordController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminForgotPasswordController extends Controller
{
    // ══════════════════ STEP 1 : FORM EMAIL ══════════════════

    /**
     * Tampilkan form input email
     */
    public function showEmailForm()
    {
        return view('admin.auth.forgot_password');
    }

    /**
     * Kirim OTP ke email super admin
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        // Cek apakah email terdaftar sebagai super_admin
        $user = User::where('email', $request->email)
            ->where('role', 'super_admin')
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan atau bukan akun Super Admin.',
            ])->withInput();
        }

        // Hapus OTP lama untuk email ini
        PasswordResetOtp::where('email', $request->email)->delete();

        // Generate OTP 6 digit
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan ke database dengan expired 10 menit
        PasswordResetOtp::create([
            'email'      => $request->email,
            'otp'        => $otp,
            'expired_at' => now()->addMinutes(10),
        ]);

        // Kirim email OTP
        Mail::to($request->email)->send(new OtpMail($otp, $user->name));

        // Redirect ke form verifikasi OTP
        return redirect()
            ->route('admin.forgot.verify_form', ['email' => $request->email])
            ->with('success', 'Kode OTP telah dikirim ke email Anda. Berlaku 10 menit.');
    }

    // ══════════════════ STEP 2 : VERIFIKASI OTP ══════════════════

    /**
     * Tampilkan form input OTP
     */
    public function showVerifyForm(Request $request)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('admin.forgot.email_form');
        }

        return view('admin.auth.verify_otp', compact('email'));
    }

    /**
     * Proses verifikasi OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size'     => 'Kode OTP harus 6 digit.',
        ]);

        $record = PasswordResetOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('is_verified', false)
            ->first();

        if (!$record) {
            return back()->withErrors([
                'otp' => 'Kode OTP tidak valid.',
            ])->withInput();
        }

        if ($record->isExpired()) {
            $record->delete();
            return back()->withErrors([
                'otp' => 'Kode OTP sudah expired. Silakan kirim ulang.',
            ])->withInput();
        }

        // Tandai OTP sebagai terverifikasi
        $record->update(['is_verified' => true]);

        // Redirect ke form reset password
        return redirect()
            ->route('admin.forgot.reset_form', ['email' => $request->email])
            ->with('success', 'Kode OTP valid. Silakan buat password baru.');
    }

    /**
     * Kirim ulang OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'super_admin')
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan.',
            ]);
        }

        // Hapus OTP lama
        PasswordResetOtp::where('email', $request->email)->delete();

        // Generate OTP baru
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordResetOtp::create([
            'email'      => $request->email,
            'otp'        => $otp,
            'expired_at' => now()->addMinutes(10),
        ]);

        Mail::to($request->email)->send(new OtpMail($otp, $user->name));

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }

    // ══════════════════ STEP 3 : RESET PASSWORD ══════════════════

    /**
     * Tampilkan form reset password (hanya jika OTP sudah diverifikasi)
     */
    public function showResetForm(Request $request)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('admin.forgot.email_form');
        }

        // Pastikan OTP sudah diverifikasi
        $verified = PasswordResetOtp::where('email', $email)
            ->where('is_verified', true)
            ->exists();

        if (!$verified) {
            return redirect()->route('admin.forgot.email_form')
                ->withErrors(['email' => 'Silakan verifikasi OTP terlebih dahulu.']);
        }

        return view('admin.auth.reset_password', compact('email'));
    }

    /**
     * Proses reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',      // minimal 1 huruf besar
                'regex:/[a-z]/',      // minimal 1 huruf kecil
                'regex:/[0-9]/',      // minimal 1 angka
                'regex:/[^A-Za-z0-9]/', // minimal 1 simbol
            ],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.regex'     => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
        ]);

        // Cek ulang apakah OTP sudah terverifikasi
        $verified = PasswordResetOtp::where('email', $request->email)
            ->where('is_verified', true)
            ->exists();

        if (!$verified) {
            return redirect()->route('admin.forgot.email_form')
                ->withErrors(['email' => 'Sesi tidak valid. Silakan ulangi proses.']);
        }

        // Update password user
        $user = User::where('email', $request->email)
            ->where('role', 'super_admin')
            ->first();

        if (!$user) {
            return redirect()->route('admin.forgot.email_form')
                ->withErrors(['email' => 'Akun tidak ditemukan.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus semua record OTP untuk email ini
        PasswordResetOtp::where('email', $request->email)->delete();

        return redirect()->route('admin.login')
            ->with('success', 'Password berhasil diubah! Silakan login dengan password baru.');
    }
}
