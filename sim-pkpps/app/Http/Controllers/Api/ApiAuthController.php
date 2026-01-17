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

        // Jika santri, sertakan data santri
        if ($user->role === 'santri') {
            $santri = Santri::where('id_santri', $user->role_id)
                ->select([
                    'id_santri',
                    'nis',
                    'nama_lengkap',
                    'jenis_kelamin',
                    'kelas',
                    'status',
                    'alamat_santri',
                    'daerah_asal',
                    'nama_orang_tua',
                    'nomor_hp_ortu',
                    'foto'
                ])
                ->first();

            $responseData['santri'] = $santri;
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
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'santri') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya santri yang bisa mengakses profil.',
            ], 403);
        }

        $santri = Santri::where('id_santri', $user->role_id)
            ->select([
                'id_santri',
                'nis',
                'nama_lengkap',
                'jenis_kelamin',
                'kelas',
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

        return response()->json([
            'success' => true,
            'data' => [
                'id_santri' => $santri->id_santri,
                'nis' => $santri->nis,
                'nama_lengkap' => $santri->nama_lengkap,
                'jenis_kelamin' => $santri->jenis_kelamin,
                'kelas' => $santri->kelas,
                'status' => $santri->status,
                'alamat_santri' => $santri->alamat_santri,
                'daerah_asal' => $santri->daerah_asal,
                'nama_orang_tua' => $santri->nama_orang_tua,
                'nomor_hp_ortu' => $santri->nomor_hp_ortu,
                'foto_url' => $santri->foto_url, // Accessor dari Model Santri
                'bergabung_sejak' => $santri->created_at->format('d F Y'),
            ]
        ], 200);
    }
}