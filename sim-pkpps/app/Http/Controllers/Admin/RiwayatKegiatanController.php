<?php
// app/Http/Controllers/admin/RiwayatKegiatanController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKegiatan;
use App\Models\Kegiatan;
use App\Models\KategoriKegiatan;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatKegiatanController extends Controller
{
    /**
     * Halaman utama riwayat kegiatan & absensi
     */
    public function index(Request $request)
    {
        $query = AbsensiKegiatan::with(['santri', 'kegiatan.kategori']);

        // Filter Santri
        if ($request->filled('id_santri')) {
            $query->where('id_santri', $request->id_santri);
        }

        // Filter Kategori
        if ($request->filled('kategori_id')) {
            $query->whereHas('kegiatan', function($q) use ($request) {
                $q->where('kategori_id', $request->kategori_id);
            });
        }

        // Filter Kegiatan
        if ($request->filled('kegiatan_id')) {
            $query->where('kegiatan_id', $request->kegiatan_id);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        // Filter Bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', date('m', strtotime($request->bulan)))
                  ->whereYear('tanggal', date('Y', strtotime($request->bulan)));
        }

        $riwayats = $query->orderBy('tanggal', 'desc')
            ->orderBy('waktu_absen', 'desc')
            ->paginate(20)
            ->appends(request()->query());

        // Data untuk filter
        $santris = Santri::where('status', 'Aktif')
            ->select('id_santri', 'nama_lengkap')
            ->orderBy('nama_lengkap')
            ->get();

        $kategoris = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();
        
        $kegiatans = Kegiatan::select('kegiatan_id', 'nama_kegiatan')
            ->orderBy('nama_kegiatan')
            ->get();

        // Statistik Global
        $statsQuery = AbsensiKegiatan::query();
        
        // Apply same filters to stats
        if ($request->filled('id_santri')) {
            $statsQuery->where('id_santri', $request->id_santri);
        }
        if ($request->filled('kategori_id')) {
            $statsQuery->whereHas('kegiatan', function($q) use ($request) {
                $q->where('kategori_id', $request->kategori_id);
            });
        }
        if ($request->filled('kegiatan_id')) {
            $statsQuery->where('kegiatan_id', $request->kegiatan_id);
        }
        if ($request->filled('tanggal_dari')) {
            $statsQuery->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $statsQuery->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('bulan')) {
            $statsQuery->whereMonth('tanggal', date('m', strtotime($request->bulan)))
                      ->whereYear('tanggal', date('Y', strtotime($request->bulan)));
        }

        $stats = $statsQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('admin.kegiatan.riwayat.index', compact(
            'riwayats', 
            'santris', 
            'kategoris', 
            'kegiatans', 
            'stats'
        ));
    }

    /**
     * Riwayat kehadiran per santri (detail)
     */
    public function detailSantri($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        
        // Statistik per santri
        $stats = AbsensiKegiatan::where('id_santri', $id_santri)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Total kehadiran per kategori
        $statsByKategori = AbsensiKegiatan::where('id_santri', $id_santri)
            ->join('kegiatans', 'absensi_kegiatans.kegiatan_id', '=', 'kegiatans.kegiatan_id')
            ->join('kategori_kegiatans', 'kegiatans.kategori_id', '=', 'kategori_kegiatans.kategori_id')
            ->select(
                'kategori_kegiatans.nama_kategori',
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('kategori_kegiatans.nama_kategori')
            ->get();

        // Riwayat 30 hari terakhir
        $riwayat30Hari = AbsensiKegiatan::where('id_santri', $id_santri)
            ->whereDate('tanggal', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(tanggal) as tanggal'),
                DB::raw('SUM(CASE WHEN status = "Hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Riwayat lengkap
        $riwayats = AbsensiKegiatan::with('kegiatan.kategori')
            ->where('id_santri', $id_santri)
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        return view('admin.kegiatan.riwayat.detail-santri', compact(
            'santri',
            'stats',
            'statsByKategori',
            'riwayat30Hari',
            'riwayats'
        ));
    }

    /**
     * Show detail riwayat
     */
    public function show(AbsensiKegiatan $riwayat)
    {
        $riwayat->load(['santri', 'kegiatan.kategori']);
        return view('admin.kegiatan.riwayat.show', compact('riwayat'));
    }

    /**
     * Edit riwayat absensi
     */
    public function edit(AbsensiKegiatan $riwayat)
    {
        $riwayat->load(['santri', 'kegiatan']);
        return view('admin.kegiatan.riwayat.edit', compact('riwayat'));
    }

    /**
     * Update riwayat absensi
     */
    public function update(Request $request, AbsensiKegiatan $riwayat)
    {
        $validated = $request->validate([
            'status' => 'required|in:Hadir,Izin,Sakit,Alpa',
            'waktu_absen' => 'nullable|date_format:H:i',
        ]);

        $riwayat->update($validated);

        return redirect()->route('admin.riwayat-kegiatan.index')
            ->with('success', 'Riwayat absensi berhasil diperbarui.');
    }

    /**
     * Hapus riwayat absensi
     */
    public function destroy(AbsensiKegiatan $riwayat)
    {
        $nama = $riwayat->santri->nama_lengkap;
        $riwayat->delete();

        return redirect()->route('admin.riwayat-kegiatan.index')
            ->with('success', "Riwayat absensi $nama berhasil dihapus.");
    }

    /**
     * Export/Cetak laporan (opsional - bisa dikembangkan)
     */
    public function exportPdf(Request $request)
    {
        // Implementasi export PDF jika diperlukan
        return response()->json(['message' => 'Fitur export sedang dikembangkan']);
    }
}