<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Santri;
use App\Models\SantriAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // ══════════════════ AKUN SANTRI (WEB) ══════════════════

    /**
     * Daftar akun santri
     */
    public function santriAccounts()
    {
        $users = SantriAccount::where('role', 'santri')->with('santri')->get();

        $santris_tanpa_akun = Santri::whereDoesntHave('santriAccount', function ($q) {
            $q->where('role', 'santri');
        })->get();

        return view('admin.users.santri_accounts', compact('users', 'santris_tanpa_akun'));
    }

    /**
     * Buat akun santri untuk satu santri langsung (1 klik)
     */
    public function buatAkunSantri(Request $request, string $idSantri)
    {
        $santri = Santri::where('id_santri', $idSantri)->firstOrFail();

        if (!$santri->nis) {
            return redirect()->back()
                ->with('error', 'Santri ' . $santri->nama_lengkap . ' belum memiliki NIS.');
        }

        $sudahAda = SantriAccount::where('role', 'santri')
            ->where('id_santri', $idSantri)->exists();

        if ($sudahAda) {
            return redirect()->back()
                ->with('error', 'Santri ' . $santri->nama_lengkap . ' sudah memiliki akun.');
        }

        SantriAccount::create([
            'id_santri' => $santri->id_santri,
            'username'  => $santri->nama_lengkap,
            'password'  => Hash::make($santri->nis),
            'role'      => 'santri',
        ]);

        return redirect()->back()
            ->with('success', 'Akun santri ' . $santri->nama_lengkap . ' berhasil dibuat. Username: ' . $santri->nama_lengkap . ' | Password: ' . $santri->nis);
    }

    /**
     * Buat akun santri untuk semua santri yang belum punya akun (1 klik massal)
     */
    public function buatSemuaAkunSantri(Request $request)
    {
        $santriList = Santri::whereDoesntHave('santriAccount', function ($q) {
            $q->where('role', 'santri');
        })->whereNotNull('nis')->get();

        if ($santriList->isEmpty()) {
            return redirect()->back()
                ->with('info', 'Semua santri sudah memiliki akun.');
        }

        $berhasil = 0;

        foreach ($santriList as $santri) {
            SantriAccount::create([
                'id_santri' => $santri->id_santri,
                'username'  => $santri->nama_lengkap,
                'password'  => Hash::make($santri->nis),
                'role'      => 'santri',
            ]);
            $berhasil++;
        }

        return redirect()->back()
            ->with('success', $berhasil . ' akun santri berhasil dibuat sekaligus.');
    }

    /**
     * Hapus akun santri
     */
    public function destroySantriAccount(string $id)
    {
        $account = SantriAccount::where('role', 'santri')->findOrFail($id);
        $nama = $account->santri ? $account->santri->nama_lengkap : $account->username;
        $account->delete();

        return redirect()->back()
            ->with('success', 'Akun santri ' . $nama . ' berhasil dihapus.');
    }

    // ══════════════════ AKUN WALI (MOBILE) ══════════════════

    /**
     * Daftar akun wali
     */
    public function waliAccounts()
    {
        $users = SantriAccount::where('role', 'wali')->with('santri')->get();

        $santris_tanpa_wali = Santri::whereDoesntHave('santriAccount', function ($q) {
            $q->where('role', 'wali');
        })->get();

        return view('admin.users.wali_accounts', compact('users', 'santris_tanpa_wali'));
    }

    /**
     * Resolve username untuk akun wali.
     *
     * Aturan:
     * - Default  : nama_orang_tua  (sama seperti sebelumnya, username = nama ortu)
     * - Fallback : "nama_orang_tua - nama_santri"
     *              → hanya dipakai jika nama_orang_tua sudah dipakai
     *                akun wali lain (cek DB + array in-memory untuk proses massal).
     *
     * @param  Santri  $santri
     * @param  array   $usernameYangSudahDipakai  username yang sudah dibuat dalam iterasi massal saat ini
     */
    private function resolveUsernameWali(Santri $santri, array $usernameYangSudahDipakai = []): string
    {
        $usernameDefault = $santri->nama_orang_tua;

        // Cek di database: apakah nama ortu ini sudah jadi username wali lain?
        $sudahDiDbOlehLain = SantriAccount::where('role', 'wali')
            ->where('username', $usernameDefault)
            ->where('id_santri', '!=', $santri->id_santri)
            ->exists();

        // Cek di array in-memory (untuk proses massal dalam 1 request)
        $sudahDiMemoriOlehLain = in_array($usernameDefault, $usernameYangSudahDipakai);

        if ($sudahDiDbOlehLain || $sudahDiMemoriOlehLain) {
            // Fallback: tambahkan nama santri agar unik
            return $usernameDefault . ' - ' . $santri->nama_lengkap;
        }

        // Normal: cukup nama orang tua saja
        return $usernameDefault;
    }

    /**
     * Buat akun wali untuk satu santri langsung (1 klik)
     */
    public function buatAkunWali(Request $request, string $idSantri)
    {
        $santri = Santri::where('id_santri', $idSantri)->firstOrFail();

        if (!$santri->nis) {
            return redirect()->back()
                ->with('error', 'Santri ' . $santri->nama_lengkap . ' belum memiliki NIS.');
        }

        if (!$santri->nama_orang_tua) {
            return redirect()->back()
                ->with('error', 'Santri ' . $santri->nama_lengkap . ' belum memiliki data nama orang tua.');
        }

        $sudahAda = SantriAccount::where('role', 'wali')
            ->where('id_santri', $idSantri)->exists();

        if ($sudahAda) {
            return redirect()->back()
                ->with('error', 'Wali santri ' . $santri->nama_lengkap . ' sudah memiliki akun.');
        }

        $username = $this->resolveUsernameWali($santri);

        SantriAccount::create([
            'id_santri' => $santri->id_santri,
            'username'  => $username,
            'password'  => Hash::make($santri->nis),
            'role'      => 'wali',
        ]);

        return redirect()->back()
            ->with('success', 'Akun wali untuk ' . $santri->nama_lengkap . ' berhasil dibuat. Username: ' . $username . ' | Password: ' . $santri->nis);
    }

    /**
     * Buat akun wali untuk semua santri yang belum punya akun wali (1 klik massal)
     */
    public function buatSemuaAkunWali(Request $request)
    {
        $santriList = Santri::whereDoesntHave('santriAccount', function ($q) {
            $q->where('role', 'wali');
        })->whereNotNull('nis')->whereNotNull('nama_orang_tua')->get();

        if ($santriList->isEmpty()) {
            return redirect()->back()
                ->with('info', 'Semua santri sudah memiliki akun wali.');
        }

        $berhasil = 0;
        $gagal    = 0;

        // Lacak username yg dibuat dalam iterasi ini agar
        // santri berikut dg nama ortu sama langsung dapat fallback
        $usernameYangSudahDipakai = [];

        foreach ($santriList as $santri) {
            if (!$santri->nama_orang_tua) {
                $gagal++;
                continue;
            }

            $username = $this->resolveUsernameWali($santri, $usernameYangSudahDipakai);

            SantriAccount::create([
                'id_santri' => $santri->id_santri,
                'username'  => $username,
                'password'  => Hash::make($santri->nis),
                'role'      => 'wali',
            ]);

            $usernameYangSudahDipakai[] = $username;
            $berhasil++;
        }

        $pesan = $berhasil . ' akun wali berhasil dibuat.';
        if ($gagal > 0) {
            $pesan .= ' ' . $gagal . ' dilewati karena data orang tua tidak lengkap.';
        }

        return redirect()->back()->with('success', $pesan);
    }

    /**
     * Hapus akun wali
     */
    public function destroyWaliAccount(string $id)
    {
        $account = SantriAccount::where('role', 'wali')->findOrFail($id);
        $nama = $account->santri ? $account->santri->nama_lengkap : $account->username;
        $account->delete();

        return redirect()->back()
            ->with('success', 'Akun wali ' . $nama . ' berhasil dihapus.');
    }

    // ══════════════════ AKUN ADMIN ══════════════════

    /**
     * Daftar akun admin
     */
    public function adminAccounts()
    {
        $admins = User::whereIn('role', ['super_admin', 'akademik', 'pamong'])
            ->orderByRaw("FIELD(role, 'super_admin', 'akademik', 'pamong')")
            ->orderBy('name')
            ->get();

        return view('admin.users.admin_accounts', compact('admins'));
    }

    /**
     * Form buat akun admin baru
     */
    public function createAdminAccount()
    {
        return view('admin.users.admin_form', [
            'admin'  => null,
            'action' => route('admin.users.admin_store'),
            'method' => 'POST',
        ]);
    }

    /**
     * Simpan akun admin baru
     */
    public function storeAdminAccount(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:akademik,pamong',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah digunakan.',
            'role.required'     => 'Role wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'username' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        return redirect()->route('admin.users.admin_accounts')
            ->with('success', 'Akun ' . $validated['role'] . ' untuk ' . $validated['name'] . ' berhasil dibuat.');
    }

    /**
     * Form edit akun admin
     */
    public function editAdminAccount(string $userId)
    {
        $admin = User::whereIn('role', ['akademik', 'pamong'])->findOrFail($userId);

        return view('admin.users.admin_form', [
            'admin'  => $admin,
            'action' => route('admin.users.admin_update', $userId),
            'method' => 'PUT',
        ]);
    }

    /**
     * Update akun admin
     */
    public function updateAdminAccount(Request $request, string $userId)
    {
        $admin = User::whereIn('role', ['akademik', 'pamong'])->findOrFail($userId);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $userId,
            'role'     => 'required|in:akademik,pamong',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $admin->name     = $validated['name'];
        $admin->email    = $validated['email'];
        $admin->username = $validated['email'];
        $admin->role     = $validated['role'];

        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->route('admin.users.admin_accounts')
            ->with('success', 'Akun ' . $admin->name . ' berhasil diperbarui.');
    }

    /**
     * Hapus akun admin
     */
    public function destroyAdminAccount(string $userId)
    {
        $admin = User::whereIn('role', ['akademik', 'pamong'])->findOrFail($userId);
        $nama  = $admin->name;
        $admin->delete();

        return redirect()->route('admin.users.admin_accounts')
            ->with('success', 'Akun ' . $nama . ' berhasil dihapus.');
    }
}