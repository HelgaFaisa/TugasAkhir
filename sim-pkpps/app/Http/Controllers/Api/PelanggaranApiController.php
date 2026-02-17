<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KlasifikasiPelanggaran;
use App\Models\KategoriPelanggaran;
use App\Models\PembinaanSanksi;
use App\Models\RiwayatPelanggaran;
use Illuminate\Http\Request;

class PelanggaranApiController extends Controller
{
    /**
     * GET KLASIFIKASI PELANGGARAN (Public - Untuk Semua)
     */
    public function getKlasifikasi()
    {
        try {
            $data = KlasifikasiPelanggaran::aktif()
                ->byUrutan()
                ->get(['id_klasifikasi', 'nama_klasifikasi', 'deskripsi', 'urutan']);

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET KATEGORI PELANGGARAN (Public - Untuk Semua)
     * Bisa difilter berdasarkan klasifikasi
     */
    public function getKategoriPelanggaran(Request $request)
    {
        try {
            $query = KategoriPelanggaran::with('klasifikasi:id_klasifikasi,nama_klasifikasi')
                ->aktif()
                ->orderBy('id_klasifikasi')
                ->orderBy('nama_pelanggaran');

            // Filter by klasifikasi (optional)
            if ($request->filled('id_klasifikasi')) {
                $query->where('id_klasifikasi', $request->id_klasifikasi);
            }

            $data = $query->get([
                'id_kategori',
                'id_klasifikasi',
                'nama_pelanggaran',
                'poin',
                'kafaroh',
            ]);

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET PEMBINAAN & SANKSI (Public - Untuk Semua)
     */
    public function getPembinaanSanksi()
    {
        try {
            $data = PembinaanSanksi::aktif()
                ->byUrutan()
                ->get(['id_pembinaan', 'judul', 'konten', 'urutan']);

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET RIWAYAT PELANGGARAN SANTRI (Private - Hanya yang Published)
     * HANYA menampilkan pelanggaran yang is_published_to_parent = true
     */
    public function getRiwayatPelanggaran(Request $request)
    {
        try {
            // Ambil id_santri dari user yang login
            $user = $request->user();
            $idSantri = $user->role_id; // role_id menyimpan id_santri

            // Query dengan pagination
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            $query = RiwayatPelanggaran::with([
                    'kategori:id_kategori,nama_pelanggaran,poin,kafaroh,id_klasifikasi',
                    'kategori.klasifikasi:id_klasifikasi,nama_klasifikasi'
                ])
                ->where('id_santri', $idSantri)
                ->where('is_published_to_parent', true) // HANYA yang sudah dipublish
                ->orderBy('tanggal', 'desc')
                ->orderBy('created_at', 'desc');

            // Filter by status kafaroh (optional)
            if ($request->filled('status_kafaroh')) {
                if ($request->status_kafaroh == 'selesai') {
                    $query->where('is_kafaroh_selesai', true);
                } else {
                    $query->where('is_kafaroh_selesai', false);
                }
            }

            // Filter by tanggal (optional)
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
            }

            $data = $query->paginate($perPage, [
                'id_riwayat',
                'id_kategori',
                'tanggal',
                'poin',
                'poin_asli',
                'keterangan',
                'is_kafaroh_selesai',
                'tanggal_kafaroh_selesai',
                'catatan_kafaroh',
            ]);

            return response()->json([
                'success' => true,
                'data' => $data->items(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET STATISTIK PELANGGARAN SANTRI
     */
    public function getStatistik(Request $request)
    {
        try {
            $user = $request->user();
            $idSantri = $user->role_id;

            // Hanya hitung yang sudah dipublish
            $totalPelanggaran = RiwayatPelanggaran::where('id_santri', $idSantri)
                ->where('is_published_to_parent', true)
                ->count();

            $totalPoin = RiwayatPelanggaran::where('id_santri', $idSantri)
                ->where('is_published_to_parent', true)
                ->sum('poin');

            $totalKafarohSelesai = RiwayatPelanggaran::where('id_santri', $idSantri)
                ->where('is_published_to_parent', true)
                ->where('is_kafaroh_selesai', true)
                ->count();

            $totalKafarohBelum = RiwayatPelanggaran::where('id_santri', $idSantri)
                ->where('is_published_to_parent', true)
                ->where('is_kafaroh_selesai', false)
                ->count();

            // Pelanggaran bulan ini
            $pelanggaranBulanIni = RiwayatPelanggaran::where('id_santri', $idSantri)
                ->where('is_published_to_parent', true)
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_pelanggaran' => $totalPelanggaran,
                    'total_poin' => $totalPoin,
                    'total_kafaroh_selesai' => $totalKafarohSelesai,
                    'total_kafaroh_belum' => $totalKafarohBelum,
                    'pelanggaran_bulan_ini' => $pelanggaranBulanIni,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET DETAIL RIWAYAT PELANGGARAN
     */
    public function getDetailRiwayat(Request $request, $idRiwayat)
    {
        try {
            $user = $request->user();
            $idSantri = $user->role_id;

            $riwayat = RiwayatPelanggaran::with([
                    'kategori:id_kategori,nama_pelanggaran,poin,kafaroh,id_klasifikasi',
                    'kategori.klasifikasi:id_klasifikasi,nama_klasifikasi',
                    'adminKafaroh:id,name',
                ])
                ->where('id_riwayat', $idRiwayat)
                ->where('id_santri', $idSantri)
                ->where('is_published_to_parent', true) // HANYA yang sudah dipublish
                ->first([
                    'id_riwayat',
                    'id_kategori',
                    'tanggal',
                    'poin',
                    'poin_asli',
                    'keterangan',
                    'is_kafaroh_selesai',
                    'tanggal_kafaroh_selesai',
                    'admin_kafaroh_id',
                    'catatan_kafaroh',
                    'tanggal_published',
                ]);

            if (!$riwayat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan atau belum dipublikasikan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $riwayat,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}