<?php
// app/Http/Controllers/Api/ApiAuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SantriAccount;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    /**
     * Login Wali via Mobile (Sanctum token)
     *
     * Request:
     * - username
     * - password
     *
     * Response:
     * - token
     * - user (role, id_santri)
     * - santri (data lengkap)
     */
    public function login(Request $request)
    {
        $request->validate([
            'id_santri' => 'required|string',
            'password'  => 'required|string',
        ]);

        // -- Cari akun di santri_accounts --
        $account = SantriAccount::where('username', $request->id_santri)->first();

        if (!$account || !Hash::check($request->password, $account->password)) {
            throw ValidationException::withMessages([
                'id_santri' => ['ID Santri atau password salah.'],
            ]);
        }

        // -- Hapus token lama --
        $account->tokens()->delete();

        // -- Buat token baru --
        $token = $account->createToken('mobile-app')->plainTextToken;

        // -- Update last_login --
        $account->update(['last_login' => now()]);

        // -- Response data --
        $responseData = [
            'success' => true,
            'message' => 'Login berhasil',
            'token'   => $token,
            'user'    => [
                'name'      => $account->santri->nama_lengkap ?? '-',
                'role'      => $account->role,
                'role_id'   => $account->id_santri,
            ],
        ];

        // -- Sertakan data santri --
        $santri = Santri::with(['kelasSantri.kelas.kelompok', 'kelasPrimary.kelas'])
            ->where('id_santri', $account->id_santri)
            ->select([
                'id_santri',
                'nis',
                'nama_lengkap',
                'jenis_kelamin',
                'status',
                'alamat_santri',
                'daerah_asal',
                'nama_orang_tua',
                'nomor_hp_ortu',
                'foto'
            ])
            ->first();

        if ($santri) {
            $kelasList = $this->buildKelasListGrouped($santri);

            $kelasName = 'Belum Ada Kelas';
            if ($santri->kelasPrimary && $santri->kelasPrimary->kelas) {
                $kelasName = $santri->kelasPrimary->kelas->nama_kelas;
            } elseif ($santri->kelasSantri->isNotEmpty() && $santri->kelasSantri->first()->kelas) {
                $kelasName = $santri->kelasSantri->first()->kelas->nama_kelas;
            }

            $responseData['santri'] = [
                'id_santri'      => $santri->id_santri,
                'nis'            => $santri->nis,
                'nama_lengkap'   => $santri->nama_lengkap,
                'jenis_kelamin'  => $santri->jenis_kelamin,
                'status'         => $santri->status,
                'alamat_santri'  => $santri->alamat_santri,
                'daerah_asal'    => $santri->daerah_asal,
                'nama_orang_tua' => $santri->nama_orang_tua,
                'nomor_hp_ortu'  => $santri->nomor_hp_ortu,
                'foto'           => $santri->foto,
                'foto_url'       => $santri->foto_url,
                'kelas'          => $kelasName,
                'kelas_list'     => $kelasList,
            ];
        } else {
            $responseData['santri'] = null;
        }

        return response()->json($responseData, 200);
    }

    /**
     * Logout - Hapus token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ], 200);
    }

    /**
     * Get Profile Santri yang sedang login
     */
    public function profile(Request $request)
    {
        $account = $request->user();

        $santri = Santri::with(['kelasSantri.kelas.kelompok', 'kelasPrimary.kelas'])
            ->where('id_santri', $account->id_santri)
            ->select([
                'id_santri',
                'nis',
                'nama_lengkap',
                'jenis_kelamin',
                'status',
                'alamat_santri',
                'daerah_asal',
                'nama_orang_tua',
                'nomor_hp_ortu',
                'foto',
                'created_at'
            ])
            ->first();

        if (!$santri) {
            return response()->json([
                'success' => false,
                'message' => 'Data santri tidak ditemukan.',
            ], 404);
        }

        $kelasList = $this->buildKelasListGrouped($santri);

        $kelasName = 'Belum Ada Kelas';
        if ($santri->kelasPrimary && $santri->kelasPrimary->kelas) {
            $kelasName = $santri->kelasPrimary->kelas->nama_kelas;
        } elseif ($santri->kelasSantri->isNotEmpty() && $santri->kelasSantri->first()->kelas) {
            $kelasName = $santri->kelasSantri->first()->kelas->nama_kelas;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id_santri'       => $santri->id_santri,
                'nis'             => $santri->nis,
                'nama_lengkap'    => $santri->nama_lengkap,
                'jenis_kelamin'   => $santri->jenis_kelamin,
                'status'          => $santri->status,
                'alamat_santri'   => $santri->alamat_santri,
                'daerah_asal'     => $santri->daerah_asal,
                'nama_orang_tua'  => $santri->nama_orang_tua,
                'nomor_hp_ortu'   => $santri->nomor_hp_ortu,
                'foto_url'        => $santri->foto_url,
                'bergabung_sejak' => $santri->created_at->format('d F Y'),
                'kelas'           => $kelasName,
                'kelas_list'      => $kelasList,
            ]
        ], 200);
    }

    /**
     * Build kelas list grouped by kelompok
     * 
     * @param \App\Models\Santri $santri
     * @return array
     */
    private function buildKelasListGrouped($santri)
    {
        $kelasList = [];

        if ($santri->kelasSantri->isEmpty()) {
            return $kelasList;
        }

        // Group kelas by kelompok
        $grouped = $santri->kelasSantri->groupBy(function ($santriKelas) {
            return $santriKelas->kelas?->kelompok?->id_kelompok ?? 'unknown';
        });

        foreach ($grouped as $kelompokId => $santriKelasItems) {
            // Skip if kelompok not found
            if ($kelompokId === 'unknown') {
                continue;
            }

            $firstItem = $santriKelasItems->first();
            $kelompok = $firstItem->kelas?->kelompok;

            if (!$kelompok) {
                continue;
            }

            $kelasList[] = [
                'kelompok_id' => $kelompok->id_kelompok,
                'kelompok_name' => $kelompok->nama_kelompok,
                'kelas' => $santriKelasItems->map(function ($santriKelas) {
                    $kelas = $santriKelas->kelas;
                    return [
                        'id_kelas' => $kelas->id,
                        'kode_kelas' => $kelas->kode_kelas,
                        'nama_kelas' => $kelas->nama_kelas,
                        'is_primary' => $santriKelas->is_primary,
                    ];
                })->sortByDesc('is_primary')->values()->toArray(),
            ];
        }

        return $kelasList;
    }
}