<?php
// app/Http/Controllers/Api/ApiKepulanganController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kepulangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApiKepulanganController extends Controller
{
    /**
     * Get list kepulangan santri (untuk wali santri)
     * GET /api/v1/kepulangan
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            // Ambil id_santri dari akun yang login
            $idSantri = $user->id_santri;
            
            if (!$idSantri) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan.',
                ], 404);
            }

            // Build query dengan pagination
            $page = $request->input('page', 1);
            $perPage = 15;
            
            $query = Kepulangan::with('santri')
                ->where('id_santri', $idSantri);

            // Filter berdasarkan status (optional)
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter berdasarkan tahun (optional)
            if ($request->filled('tahun')) {
                $query->whereYear('tanggal_pulang', $request->tahun);
            }

            // Order by terbaru
            $query->orderBy('created_at', 'desc');

            // Get data dengan pagination
            $kepulangan = $query->paginate($perPage, ['*'], 'page', $page);

            // Get info kuota santri
            $kuotaInfo = Kepulangan::getSisaKuotaSantri($idSantri);
            $settings = Kepulangan::getSettings();

            // Format response
            $data = [
                'success' => true,
                'message' => 'Data kepulangan berhasil diambil.',
                'data' => [
                    'kepulangan' => collect($kepulangan->items())->map(function($item) {
                        return [
                            'id_kepulangan' => $item->id_kepulangan,
                            'tanggal_izin' => $item->tanggal_izin->format('Y-m-d'),
                            'tanggal_izin_formatted' => $item->tanggal_izin->format('d M Y'),
                            'tanggal_pulang' => $item->tanggal_pulang->format('Y-m-d'),
                            'tanggal_pulang_formatted' => $item->tanggal_pulang->format('d M Y'),
                            'tanggal_kembali' => $item->tanggal_kembali->format('Y-m-d'),
                            'tanggal_kembali_formatted' => $item->tanggal_kembali->format('d M Y'),
                            'durasi_izin' => $item->durasi_izin,
                            'alasan' => $item->alasan,
                            'status' => $item->status,
                            'catatan' => $item->catatan,
                            'approved_at' => $item->approved_at ? $item->approved_at->format('Y-m-d H:i:s') : null,
                            'approved_at_formatted' => $item->approved_at ? $item->approved_at->format('d M Y H:i') : null,
                            'is_aktif' => $item->is_aktif,
                            'is_terlambat' => $item->is_terlambat,
                        ];
                    }),
                    'kuota' => [
                        'kuota_maksimal' => $kuotaInfo['kuota_maksimal'],
                        'total_terpakai' => $kuotaInfo['total_terpakai'],
                        'sisa_kuota' => $kuotaInfo['sisa_kuota'],
                        'persentase' => $kuotaInfo['persentase'],
                        'status' => $kuotaInfo['status'], // aman, hampir_habis, melebihi
                        'badge_color' => $kuotaInfo['badge_color'], // success, warning, danger
                        'periode_mulai' => $settings->periode_mulai,
                        'periode_akhir' => $settings->periode_akhir,
                    ],
                    'pagination' => [
                        'current_page' => $kepulangan->currentPage(),
                        'last_page' => $kepulangan->lastPage(),
                        'per_page' => $kepulangan->perPage(),
                        'total' => $kepulangan->total(),
                        'from' => $kepulangan->firstItem(),
                        'to' => $kepulangan->lastItem(),
                    ],
                ],
            ];

            return response()->json($data, 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get detail kepulangan
     * GET /api/v1/kepulangan/{id_kepulangan}
     */
    public function show($idKepulangan)
    {
        try {
            $user = Auth::user();

            $idSantri = $user->id_santri;

            // Get kepulangan dengan validasi kepemilikan
            $kepulangan = Kepulangan::with('santri')
                ->where('id_kepulangan', $idKepulangan)
                ->where('id_santri', $idSantri) // Pastikan milik santri yang login
                ->first();

            if (!$kepulangan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data kepulangan tidak ditemukan.',
                ], 404);
            }

            // Get info kuota
            $kuotaInfo = Kepulangan::getSisaKuotaSantri($idSantri);
            $settings = Kepulangan::getSettings();

            $data = [
                'success' => true,
                'message' => 'Detail kepulangan berhasil diambil.',
                'data' => [
                    'kepulangan' => [
                        'id_kepulangan' => $kepulangan->id_kepulangan,
                        'tanggal_izin' => $kepulangan->tanggal_izin->format('Y-m-d'),
                        'tanggal_izin_formatted' => $kepulangan->tanggal_izin->format('d M Y'),
                        'tanggal_pulang' => $kepulangan->tanggal_pulang->format('Y-m-d'),
                        'tanggal_pulang_formatted' => $kepulangan->tanggal_pulang->format('d M Y'),
                        'tanggal_kembali' => $kepulangan->tanggal_kembali->format('Y-m-d'),
                        'tanggal_kembali_formatted' => $kepulangan->tanggal_kembali->format('d M Y'),
                        'durasi_izin' => $kepulangan->durasi_izin,
                        'alasan' => $kepulangan->alasan,
                        'status' => $kepulangan->status,
                        'catatan' => $kepulangan->catatan,
                        'approved_at' => $kepulangan->approved_at ? $kepulangan->approved_at->format('Y-m-d H:i:s') : null,
                        'approved_at_formatted' => $kepulangan->approved_at ? $kepulangan->approved_at->format('d M Y H:i') : null,
                        'is_aktif' => $kepulangan->is_aktif,
                        'is_terlambat' => $kepulangan->is_terlambat,
                        'santri' => [
                            'nama_lengkap' => $kepulangan->santri->nama_lengkap,
                            'nis' => $kepulangan->santri->nis,
                        ],
                    ],
                    'kuota' => [
                        'kuota_maksimal' => $kuotaInfo['kuota_maksimal'],
                        'total_terpakai' => $kuotaInfo['total_terpakai'],
                        'sisa_kuota' => $kuotaInfo['sisa_kuota'],
                        'persentase' => $kuotaInfo['persentase'],
                        'status' => $kuotaInfo['status'],
                        'badge_color' => $kuotaInfo['badge_color'],
                        'periode_mulai' => $settings->periode_mulai,
                        'periode_akhir' => $settings->periode_akhir,
                    ],
                ],
            ];

            return response()->json($data, 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get info kuota santri
     * GET /api/v1/kepulangan/kuota
     */
    public function kuota(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!in_array($user->role, ['santri', 'wali'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak.',
                ], 403);
            }

            $idSantri = $user->id_santri;
            
            if (!$idSantri) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan.',
                ], 404);
            }

            $kuotaInfo = Kepulangan::getSisaKuotaSantri($idSantri);
            $settings = Kepulangan::getSettings();

            // Get detail izin dalam periode aktif
            $detailIzin = Kepulangan::where('id_santri', $idSantri)
                ->whereIn('status', ['Disetujui', 'Selesai'])
                ->whereBetween('tanggal_pulang', [$settings->periode_mulai, $settings->periode_akhir])
                ->orderBy('tanggal_pulang', 'desc')
                ->get()
                ->map(function($item) {
                    return [
                        'id_kepulangan' => $item->id_kepulangan,
                        'tanggal_pulang' => $item->tanggal_pulang->format('Y-m-d'),
                        'tanggal_pulang_formatted' => $item->tanggal_pulang->format('d M Y'),
                        'tanggal_kembali' => $item->tanggal_kembali->format('Y-m-d'),
                        'tanggal_kembali_formatted' => $item->tanggal_kembali->format('d M Y'),
                        'durasi_izin' => $item->durasi_izin,
                        'status' => $item->status,
                    ];
                });

            $data = [
                'success' => true,
                'message' => 'Info kuota berhasil diambil.',
                'data' => [
                    'kuota_maksimal' => $kuotaInfo['kuota_maksimal'],
                    'total_terpakai' => $kuotaInfo['total_terpakai'],
                    'sisa_kuota' => $kuotaInfo['sisa_kuota'],
                    'persentase' => $kuotaInfo['persentase'],
                    'status' => $kuotaInfo['status'],
                    'badge_color' => $kuotaInfo['badge_color'],
                    'periode_mulai' => $settings->periode_mulai,
                    'periode_akhir' => $settings->periode_akhir,
                    'detail_izin' => $detailIzin,
                ],
            ];

            return response()->json($data, 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Notifikasi status kepulangan santri saat ini
     * GET /api/v1/kepulangan/notifikasi
     */
    public function notifikasiKepulangan(Request $request)
    {
        try {
            $user = Auth::user();

            if (!in_array($user->role, ['santri', 'wali'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak.',
                ], 403);
            }

            $idSantri = $user->id_santri;
            $today = Carbon::today();

            // Cari kepulangan yang sedang aktif (tanggal hari ini ada di antara tanggal_pulang dan tanggal_kembali)
            $kepulangan = Kepulangan::where('id_santri', $idSantri)
                ->where('status', 'Disetujui')
                ->where('tanggal_pulang', '<=', $today)
                ->where('tanggal_kembali', '>=', $today)
                ->orderBy('tanggal_kembali', 'desc')
                ->first();

            if (!$kepulangan) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'sedang_pulang' => false,
                        'tanggal_kembali' => null,
                        'sisa_hari' => 0,
                        'status' => null,
                    ],
                ]);
            }

            $tanggalKembali = Carbon::parse($kepulangan->tanggal_kembali);
            $sisaHari = $today->diffInDays($tanggalKembali, false); // negatif jika sudah lewat
            $statusKepulangan = $sisaHari < 0 ? 'terlambat' : 'aktif';

            return response()->json([
                'success' => true,
                'data' => [
                    'sedang_pulang' => true,
                    'tanggal_kembali' => $tanggalKembali->format('Y-m-d'),
                    'tanggal_kembali_formatted' => $tanggalKembali->locale('id')->isoFormat('D MMMM Y'),
                    'sisa_hari' => (int) $sisaHari,
                    'status' => $statusKepulangan,
                    'alasan' => $kepulangan->alasan,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
