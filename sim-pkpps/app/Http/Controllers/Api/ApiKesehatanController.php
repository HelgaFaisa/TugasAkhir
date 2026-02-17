<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KesehatanSantri;
use App\Models\Santri;
use Illuminate\Http\Request;

class ApiKesehatanController extends Controller
{
    /**
     * Get riwayat kesehatan santri yang login
     */
    public function index(Request $request)
    {
        try {
            // Ambil id_santri dari user yang login (wali)
            $idSantri = $request->user()->role_id;
            
            // Cek santri exist
            $santri = Santri::where('id_santri', $idSantri)->first();
            
            if (!$santri) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data santri tidak ditemukan',
                ], 404);
            }
            
            // Query riwayat kesehatan
            $query = KesehatanSantri::where('id_santri', $idSantri)
                ->select([
                    'id',
                    'id_kesehatan',
                    'id_santri',
                    'tanggal_masuk',
                    'tanggal_keluar',
                    'keluhan',
                    'catatan',
                    'status',
                    'created_at'
                ])
                ->orderBy('tanggal_masuk', 'desc');
            
            // Filter status (optional)
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            // Pagination
            $kesehatan = $query->paginate(20);
            
            // Format data
            $data = $kesehatan->map(function($item) {
                return [
                    'id' => $item->id,
                    'id_kesehatan' => $item->id_kesehatan,
                    'tanggal_masuk' => $item->tanggal_masuk->format('Y-m-d'),
                    'tanggal_masuk_formatted' => $item->tanggal_masuk->format('d M Y'),
                    'tanggal_keluar' => $item->tanggal_keluar ? $item->tanggal_keluar->format('Y-m-d') : null,
                    'tanggal_keluar_formatted' => $item->tanggal_keluar ? $item->tanggal_keluar->format('d M Y') : null,
                    'keluhan' => $item->keluhan,
                    'catatan' => $item->catatan,
                    'status' => $item->status,
                    'lama_dirawat' => $item->lama_dirawat . ' hari',
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => [
                    'current_page' => $kesehatan->currentPage(),
                    'last_page' => $kesehatan->lastPage(),
                    'total' => $kesehatan->total(),
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat kesehatan: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Get detail kesehatan
     */
    public function show(Request $request, $idKesehatan)
    {
        try {
            $idSantri = $request->user()->role_id;
            
            // Cari data kesehatan
            $kesehatan = KesehatanSantri::where('id_kesehatan', $idKesehatan)
                ->where('id_santri', $idSantri) // Pastikan milik santri yang login
                ->first();
            
            if (!$kesehatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data kesehatan tidak ditemukan',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id_kesehatan' => $kesehatan->id_kesehatan,
                    'tanggal_masuk' => $kesehatan->tanggal_masuk->format('Y-m-d'),
                    'tanggal_masuk_formatted' => $kesehatan->tanggal_masuk->format('d F Y'),
                    'tanggal_keluar' => $kesehatan->tanggal_keluar ? $kesehatan->tanggal_keluar->format('Y-m-d') : null,
                    'tanggal_keluar_formatted' => $kesehatan->tanggal_keluar ? $kesehatan->tanggal_keluar->format('d F Y') : null,
                    'keluhan' => $kesehatan->keluhan,
                    'catatan' => $kesehatan->catatan,
                    'status' => $kesehatan->status,
                    'lama_dirawat' => $kesehatan->lama_dirawat,
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail kesehatan: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Get statistik kesehatan santri
     */
    public function statistik(Request $request)
    {
        try {
            $idSantri = $request->user()->role_id;
            
            // Hitung total per status
            $totalDirawat = KesehatanSantri::where('id_santri', $idSantri)
                ->where('status', 'dirawat')
                ->count();
            
            $totalSembuh = KesehatanSantri::where('id_santri', $idSantri)
                ->where('status', 'sembuh')
                ->count();
            
            $totalIzin = KesehatanSantri::where('id_santri', $idSantri)
                ->where('status', 'izin')
                ->count();
            
            $totalRiwayat = KesehatanSantri::where('id_santri', $idSantri)
                ->count();
            
            // Riwayat terbaru yang sedang dirawat
            $sedangDirawat = KesehatanSantri::where('id_santri', $idSantri)
                ->where('status', 'dirawat')
                ->orderBy('tanggal_masuk', 'desc')
                ->first();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_riwayat' => $totalRiwayat,
                    'total_dirawat' => $totalDirawat,
                    'total_sembuh' => $totalSembuh,
                    'total_izin' => $totalIzin,
                    'sedang_dirawat' => $sedangDirawat ? [
                        'id_kesehatan' => $sedangDirawat->id_kesehatan,
                        'tanggal_masuk' => $sedangDirawat->tanggal_masuk->format('d M Y'),
                        'keluhan' => $sedangDirawat->keluhan,
                        'lama_dirawat' => $sedangDirawat->lama_dirawat . ' hari',
                    ] : null,
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage(),
            ], 500);
        }
    }
}