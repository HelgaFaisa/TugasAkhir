<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar akun Santri.
     */
    public function santriAccounts()
    {
        $users = User::where('role', 'santri')->with('santri')->get();
        $santris_tanpa_akun = Santri::whereDoesntHave('user', function($query) {
            $query->where('role', 'santri');
        })->get();

        return view('admin.users.santri_accounts', compact('users', 'santris_tanpa_akun'));
    }

    /**
     * Tampilkan daftar akun Wali Santri.
     */
    public function waliAccounts()
    {
        $users = User::where('role', 'wali')->with('santri')->get();
        
        $santris_tanpa_wali = Santri::whereDoesntHave('waliUser')->get();

        return view('admin.users.wali_accounts', compact('users', 'santris_tanpa_wali'));
    }

    /**
     * Tampilkan form untuk membuat akun baru.
     */
    public function createAccount(string $role)
    {
        if (!in_array($role, ['santri', 'wali'])) {
            abort(404);
        }

        if ($role === 'santri') {
            $list_data = Santri::whereDoesntHave('user', function($query) {
                $query->where('role', 'santri');
            })->get();
        } else {
            // Wali: ambil santri yang belum punya akun wali
            $list_data = Santri::whereDoesntHave('waliUser')->get();
        }
        
        return view('admin.users.create_account', compact('role', 'list_data'));
    }

    /**
     * Simpan akun baru.
     */
    public function storeAccount(Request $request, string $role)
    {
        if (!in_array($role, ['santri', 'wali'])) {
            abort(404);
        }

        // Validasi berbeda untuk santri dan wali
        $rules = [
            'role_id' => [
                'required',
                Rule::exists('santris', 'id_santri'),
                function ($attribute, $value, $fail) use ($role) {
                    $exists = User::where('role', $role)
                        ->where('role_id', $value)
                        ->exists();
                    if ($exists) {
                        $fail("Santri ini sudah memiliki akun {$role}.");
                    }
                },
            ],
            'username' => 'required|string|max:255|unique:users,username',
        ];

        // Untuk wali: password tidak perlu min karena otomatis dari NIS
        // Untuk santri: password minimal 8 karakter
        if ($role === 'wali') {
            $rules['password'] = 'required|string|confirmed';
        } else {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $messages = [
            'role_id.required' => 'Wajib memilih santri.',
            'role_id.exists' => 'Data santri tidak ditemukan.',
            'username.unique' => 'Username sudah digunakan.',
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];

        $validated = $request->validate($rules, $messages);

        // Ambil data santri
        $santri = Santri::where('id_santri', $validated['role_id'])->firstOrFail();
        
        // Untuk wali: name = nama orang tua (jika ada) atau nama santri
        // Untuk santri: name = nama santri
        $name = ($role === 'wali') 
            ? ($santri->nama_orang_tua ?? $santri->nama_lengkap)
            : $santri->nama_lengkap;

        // Simpan User
        User::create([
            'name' => $name,
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'role_id' => $validated['role_id'],
        ]);

        $successMsg = $role === 'wali' 
            ? "Akun wali untuk santri {$santri->nama_lengkap} berhasil dibuat. Login: Username={$validated['username']}, Password=NIS"
            : "Akun santri {$santri->nama_lengkap} berhasil dibuat.";

        return redirect()->route('admin.users.'.$role.'_accounts')
            ->with('success', $successMsg);
    }

    /**
     * Hapus akun santri/wali.
     */
    public function destroyAccount(string $role, string $userId)
    {
        if (!in_array($role, ['santri', 'wali'])) {
            abort(404);
        }

        // Cari user berdasarkan ID
        $user = User::findOrFail($userId);

        // Pastikan user yang akan dihapus adalah role yang sesuai
        if ($user->role !== $role) {
            return redirect()->back()->with('error', 'Akun tidak valid.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.'.$role.'_accounts')
            ->with('success', "Akun {$role} {$userName} berhasil dihapus.");
    }

    /**
     * Reset password akun santri/wali ke default (NIS).
     */
    public function resetPassword(string $role, string $userId)
    {
        if (!in_array($role, ['santri', 'wali'])) {
            abort(404);
        }

        // Cari user berdasarkan ID
        $user = User::findOrFail($userId);

        // Pastikan user adalah role yang sesuai
        if ($user->role !== $role) {
            return redirect()->back()->with('error', 'Akun tidak valid.');
        }

        // Ambil santri terkait
        $santri = Santri::where('id_santri', $user->role_id)->first();
        
        if (!$santri || !$santri->nis) {
            return redirect()->back()->with('error', 'NIS santri tidak ditemukan. Tidak dapat mereset password.');
        }

        // Reset password ke NIS
        $user->password = Hash::make($santri->nis);
        $user->save();

        return redirect()->route('admin.users.'.$role.'_accounts')
            ->with('success', "Password akun {$user->name} berhasil direset ke NIS: {$santri->nis}");
    }
}