<?php
// app/Http/Controllers/Api/ApiPengajuanKepulanganController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanKepulangan;
use App\Models\Kepulangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApiPengajuanKepulanganController extends Controller
{
    /**
     * POST: Submit pengajuan kepulangan baru
     * Endpoint: /api/v1/kepulangan/pengajuan
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Validasi role
            if (!in_array($user->role, ['santri', 'wali'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak.',
                ], 403);
            }

            $idSantri = $user->role_id;

            // Validasi input
            $validated = $request->validate([
                'tanggal_pulang' => 'required|date|after_or_equal:today',
                'tanggal_kembali' => 'required|date|after:tanggal_pulang',
                'alasan' => 'required|string|max:500',
            ], [
                'tanggal_pulang.required' => 'Tanggal pulang wajib diisi.',
                'tanggal_pulang.after_or_equal' => 'Tanggal pulang minimal hari ini.',
                'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
                'tanggal_kembali.after' => 'Tanggal kembali harus setelah tanggal pulang.',
                'alasan.required' => 'Alasan kepulangan wajib diisi.',
                'alasan.max' => 'Alasan maksimal 500 karakter.',
            ]);

            // Hitung durasi izin
            $tanggalPulang = Carbon::parse($validated['tanggal_pulang']);
            $tanggalKembali = Carbon::parse($validated['tanggal_kembali']);
            $durasiIzin = $tanggalPulang->diffInDays($tanggalKembali) + 1;

            // Create pengajuan
            $pengajuan = PengajuanKepulangan::create([
                'id_santri' => $idSantri,
                'tanggal_pulang' => $validated['tanggal_pulang'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'durasi_izin' => $durasiIzin,
                'alasan' => $validated['alasan'],
                'status' => 'Menunggu',
            ]);

            // Get info kuota untuk notifikasi
            $kuotaInfo = Kepulangan::getSisaKuotaSantri($idSantri);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dikirim. Menunggu persetujuan admin.',
                'data' => [
                    'id_pengajuan' => $pengajuan->id_pengajuan,
                    'tanggal_pulang' => $pengajuan->tanggal_pulang->format('Y-m-d'),
                    'tanggal_kembali' => $pengajuan->tanggal_kembali->format('Y-m-d'),
                    'durasi_izin' => $pengajuan->durasi_izin,
                    'status' => $pengajuan->status,
                    'kuota_info' => $kuotaInfo,
                ],
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET: List pengajuan kepulangan santri
     * Endpoint: /api/v1/kepulangan/pengajuan
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!in_array($user->role, ['santri', 'wali'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak.',
                ], 403);
            }

            $idSantri = $user->role_id;

            // Build query
            $page = $request->input('page', 1);
            $perPage = 15;

            $query = PengajuanKepulangan::where('id_santri', $idSantri);

            // Filter status (optional)
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Order by terbaru
            $query->orderBy('created_at', 'desc');

            // Paginate
            $pengajuan = $query->paginate($perPage, ['*'], 'page', $page);

            // Format response
            $data = [
                'success' => true,
                'message' => 'Data pengajuan berhasil diambil.',
                'data' => [
                    'pengajuan' => $pengajuan->map(function($item) {
                        return [
                            'id_pengajuan' => $item->id_pengajuan,
                            'tanggal_pulang' => $item->tanggal_pulang->format('Y-m-d'),
                            'tanggal_pulang_formatted' => $item->tanggal_pulang->format('d M Y'),
                            'tanggal_kembali' => $item->tanggal_kembali->format('Y-m-d'),
                            'tanggal_kembali_formatted' => $item->tanggal_kembali->format('d M Y'),
                            'durasi_izin' => $item->durasi_izin,
                            'alasan' => $item->alasan,
                            'status' => $item->status,
                            'catatan_review' => $item->catatan_review,
                            'reviewed_at' => $item->reviewed_at ? $item->reviewed_at->format('Y-m-d H:i:s') : null,
                            'reviewed_at_formatted' => $item->reviewed_at ? $item->reviewed_at->format('d M Y H:i') : null,
                            'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                            'created_at_formatted' => $item->created_at->format('d M Y H:i'),
                        ];
                    }),
                    'pagination' => [
                        'current_page' => $pengajuan->currentPage(),
                        'last_page' => $pengajuan->lastPage(),
                        'per_page' => $pengajuan->perPage(),
                        'total' => $pengajuan->total(),
                        'from' => $pengajuan->firstItem(),
                        'to' => $pengajuan->lastItem(),
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
     * POST: Preview durasi & validasi kuota (sebelum submit)
     * Endpoint: /api/v1/kepulangan/pengajuan/preview
     */
    public function preview(Request $request)
    {
        try {
            $user = Auth::user();
            $idSantri = $user->role_id;

            $validated = $request->validate([
                'tanggal_pulang' => 'required|date',
                'tanggal_kembali' => 'required|date|after:tanggal_pulang',
            ]);

            // Hitung durasi
            $tanggalPulang = Carbon::parse($validated['tanggal_pulang']);
            $tanggalKembali = Carbon::parse($validated['tanggal_kembali']);
            $durasiIzin = $tanggalPulang->diffInDays($tanggalKembali) + 1;

            // Get kuota info
            $kuotaInfo = Kepulangan::getSisaKuotaSantri($idSantri);
            $totalSetelahIzin = $kuotaInfo['total_terpakai'] + $durasiIzin;
            $sisaSetelahIzin = $kuotaInfo['kuota_maksimal'] - $totalSetelahIzin;
            $overLimit = $totalSetelahIzin > $kuotaInfo['kuota_maksimal'];

            $warningMessage = '';
            if ($overLimit) {
                $kelebihan = $totalSetelahIzin - $kuotaInfo['kuota_maksimal'];
                $warningMessage = "Izin ini akan melebihi batas {$kuotaInfo['kuota_maksimal']} hari per tahun. Kelebihan: {$kelebihan} hari.";
            } elseif ($totalSetelahIzin >= $kuotaInfo['kuota_maksimal'] * 0.8) {
                $warningMessage = "Kuota hampir habis! Sisa kuota setelah izin ini hanya " . max(0, $sisaSetelahIzin) . " hari.";
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'durasi_izin' => $durasiIzin,
                    'total_setelah_izin' => $totalSetelahIzin,
                    'sisa_setelah_izin' => max(0, $sisaSetelahIzin),
                    'over_limit' => $overLimit,
                    'warning_message' => $warningMessage,
                    'kuota_maksimal' => $kuotaInfo['kuota_maksimal'],
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}