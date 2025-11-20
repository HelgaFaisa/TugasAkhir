<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Santri;
use App\Models\Wali;
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
        // Ambil akun user dengan role 'santri'
        $users = User::where('role', 'santri')->get();
        // Ambil data santri yang belum memiliki akun
        $santris_tanpa_akun = Santri::whereDoesntHave('user')->get();

        return view('admin.users.santri_accounts', compact('users', 'santris_tanpa_akun'));
    }

    /**
     * Tampilkan daftar akun Wali Santri.
     */
    public function waliAccounts()
    {
        // Ambil akun user dengan role 'wali'
        $users = User::where('role', 'wali')->get();
        
        // Asumsi: Wali tidak punya tabel biodata terpisah untuk langkah 3 ini,
        // jadi kita ambil dari data Santri.
        // Jika Wali memiliki tabel biodata Walis, kita bisa tambahkan logika Wali::whereDoesntHave('user')
        $walis = Wali::all();

        return view('admin.users.wali_accounts', compact('users', 'walis'));
    }

    /**
     * Tampilkan form untuk membuat akun baru (digunakan untuk santri dan wali).
     */
    public function createAccount(string $role)
    {
        if (!in_array($role, ['santri', 'wali'])) {
            abort(404);
        }

        $list_data = [];
        if ($role === 'santri') {
            // Ambil santri yang BELUM punya akun
            $list_data = Santri::whereDoesntHave('user')->get();
        } elseif ($role === 'wali') {
            // Ambil semua data wali (kita asumsikan Wali adalah individu terpisah yang didata admin)
            $list_data = Wali::all();
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

        // Validasi
        $validated = $request->validate([
            'role_id' => [
                'required', 
                Rule::unique('users', 'role_id')->where(function ($query) use ($role) {
                    return $query->where('role', $role);
                })
            ],
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'role_id.unique' => 'Akun untuk data ini sudah ada.',
            'role_id.required' => 'Wajib memilih data Santri/Wali yang akan dibuatkan akun.',
            'username.unique' => 'Username ini sudah digunakan.',
        ]);

        // Dapatkan nama berdasarkan role_id
        if ($role === 'santri') {
            $data_induk = Santri::where('id_santri', $request->role_id)->firstOrFail();
            $name = $data_induk->nama_lengkap;
        } elseif ($role === 'wali') {
            $data_induk = Wali::where('id_wali', $request->role_id)->firstOrFail();
            $name = $data_induk->nama_wali;
        }

        // Simpan User
        User::create([
            'name' => $name,
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'role_id' => $validated['role_id'],
        ]);

        return redirect()->route('admin.users.'.$role.'_accounts')->with('success', 'Akun '.$role.' berhasil dibuat.');
    }
    
    // Tambahkan method edit/update/destroy untuk akun di langkah berikutnya
}