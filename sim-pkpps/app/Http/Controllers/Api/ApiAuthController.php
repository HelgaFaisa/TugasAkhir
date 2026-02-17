<?php
// app/Http/Controllers/Api/ApiAuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    /**
     * Login Santri/Wali via Mobile
     * 
     * Request: 
     * - id_santri (username)
     * - password
     * 
     * Response:
     * - token
     * - user (name, role, role_id)
     * - santri (data lengkap santri jika role=santri)
     */
    public function login(Request $request)
    {
        $request->validate([
            'id_santri' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cari user berdasarkan username (id_santri)
        $user = User::where('username', $request->id_santri)->first();

        // Validasi user dan password
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'id_santri' => ['ID Santri atau password salah.'],
            ]);
        }

        // Cek apakah user adalah santri atau wali
        if (!in_array($user->role, ['santri', 'wali'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini tidak memiliki akses ke aplikasi mobile.',
            ], 403);
        }

        // Hapus token lama (optional, untuk keamanan)
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('mobile-app')->plainTextToken;

        // Prepare response data
        $responseData = [
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'role' => $user->role,
                'role_id' => $user->role_id,
            ],
        ];

        // Jika santri atau wali, sertakan data santri
        // Untuk wali, role_id menyimpan id_santri yang diwali (anaknya)
        if (in_array($user->role, ['santri', 'wali'])) {
            $santri = Santri::with(['kelasSantri.kelas.kelompok', 'kelasPrimary.kelas'])
                ->where('id_santri', $user->role_id)
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
                // Build kelas_list grouped by kelompok
                $kelasList = $this->buildKelasListGrouped($santri);

                // Get primary kelas name for backward compatibility
                $kelasName = 'Belum Ada Kelas';
                if ($santri->kelasPrimary && $santri->kelasPrimary->kelas) {
                    $kelasName = $santri->kelasPrimary->kelas->nama_kelas;
                } elseif ($santri->kelasSantri->isNotEmpty() && $santri->kelasSantri->first()->kelas) {
                    $kelasName = $santri->kelasSantri->first()->kelas->nama_kelas;
                }

                $responseData['santri'] = [
                    'id_santri' => $santri->id_santri,
                    'nis' => $santri->nis,
                    'nama_lengkap' => $santri->nama_lengkap,
                    'jenis_kelamin' => $santri->jenis_kelamin,
                    'status' => $santri->status,
                    'alamat_santri' => $santri->alamat_santri,
                    'daerah_asal' => $santri->daerah_asal,
                    'nama_orang_tua' => $santri->nama_orang_tua,
                    'nomor_hp_ortu' => $santri->nomor_hp_ortu,
                    'foto' => $santri->foto,
                    'foto_url' => $santri->foto_url,
                    'kelas' => $kelasName, // Backward compatibility
                    'kelas_list' => $kelasList, // NEW: Multiple kelas grouped
                ];
            } else {
                $responseData['santri'] = null;
            }
        }

        return response()->json($responseData, 200);
    }

    /**
     * Logout - Hapus token
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ], 200);
    }

    /**
     * Get Profile Santri yang sedang login
     * Untuk role santri: tampilkan data diri sendiri
     * Untuk role wali: tampilkan data santri yang diwali (anaknya)
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        // Hanya santri dan wali yang bisa akses profil
        if (!in_array($user->role, ['santri', 'wali'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya santri/wali yang bisa mengakses profil.',
            ], 403);
        }

        // Untuk santri dan wali, role_id menyimpan id_santri
        $santri = Santri::with(['kelasSantri.kelas.kelompok', 'kelasPrimary.kelas'])
            ->where('id_santri', $user->role_id)
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

        // Build kelas_list grouped by kelompok
        $kelasList = $this->buildKelasListGrouped($santri);

        // Get primary kelas name for backward compatibility
        $kelasName = 'Belum Ada Kelas';
        if ($santri->kelasPrimary && $santri->kelasPrimary->kelas) {
            $kelasName = $santri->kelasPrimary->kelas->nama_kelas;
        } elseif ($santri->kelasSantri->isNotEmpty() && $santri->kelasSantri->first()->kelas) {
            $kelasName = $santri->kelasSantri->first()->kelas->nama_kelas;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id_santri' => $santri->id_santri,
                'nis' => $santri->nis,
                'nama_lengkap' => $santri->nama_lengkap,
                'jenis_kelamin' => $santri->jenis_kelamin,
                'status' => $santri->status,
                'alamat_santri' => $santri->alamat_santri,
                'daerah_asal' => $santri->daerah_asal,
                'nama_orang_tua' => $santri->nama_orang_tua,
                'nomor_hp_ortu' => $santri->nomor_hp_ortu,
                'foto_url' => $santri->foto_url, // Accessor dari Model Santri
                'bergabung_sejak' => $santri->created_at->format('d F Y'),
                'kelas' => $kelasName, // Backward compatibility
                'kelas_list' => $kelasList, // NEW: Multiple kelas grouped
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