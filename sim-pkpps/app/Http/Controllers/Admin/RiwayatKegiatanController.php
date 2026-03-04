<?php
// app/Http/Controllers/admin/RiwayatKegiatanController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKegiatan;
use App\Models\Kegiatan;
use App\Models\KategoriKegiatan;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\KelompokKelas;
use App\Models\SantriKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatKegiatanController extends Controller
{
    /**
     * Halaman utama riwayat kegiatan & absensi
     */
    public function index(Request $request)
    {
        // Query untuk mendapatkan kegiatan dengan statistik absensi
        $query = Kegiatan::with(['kategori', 'kelasKegiatan.kelompok'])
            ->withCount(['absensis as total_absensi'])
            ->withCount(['absensis as hadir' => function($q) {
                $q->where('status', 'Hadir');
            }])
            ->withCount(['absensis as izin' => function($q) {
                $q->where('status', 'Izin');
            }])
            ->withCount(['absensis as sakit' => function($q) {
                $q->where('status', 'Sakit');
            }])
            ->withCount(['absensis as alpa' => function($q) {
                $q->where('status', 'Alpa');
            }]);

        // Filter Kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter Tanggal untuk absensi
        if ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai') || $request->filled('bulan')) {
            $query->whereHas('absensis', function($q) use ($request) {
                if ($request->filled('tanggal_dari')) {
                    $q->whereDate('tanggal', '>=', $request->tanggal_dari);
                }
                if ($request->filled('tanggal_sampai')) {
                    $q->whereDate('tanggal', '<=', $request->tanggal_sampai);
                }
                if ($request->filled('bulan')) {
                    $q->whereMonth('tanggal', date('m', strtotime($request->bulan)))
                      ->whereYear('tanggal', date('Y', strtotime($request->bulan)));
                }
            });
        }

        $kegiatans = $query->orderBy('nama_kegiatan')
            ->paginate(15)
            ->appends(request()->query());

        // Data untuk filter
        $kategoris = KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();

        return view('admin.kegiatan.riwayat.index', compact('kegiatans', 'kategoris'));
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

        // Kehadiran per kelas santri
        $statsByKelasSantri = $santri->kelasSantri()
            ->with('kelas.kelompok')
            ->get()
            ->map(function($sk) use ($id_santri) {
                $kehadiran = AbsensiKegiatan::where('id_santri', $id_santri)
                    ->whereHas('kegiatan', function($q) use ($sk) {
                        $q->whereHas('kelasKegiatan', function($q2) use ($sk) {
                            $q2->where('id_kelas', $sk->id_kelas);
                        });
                    })
                    ->selectRaw('
                        COUNT(*) as total,
                        SUM(CASE WHEN status = "Hadir" THEN 1 ELSE 0 END) as hadir
                    ')
                    ->first();
                
                return [
                    'kelas' => $sk->kelas->nama_kelas,
                    'kelompok' => $sk->kelas->kelompok->nama_kelompok,
                    'total' => $kehadiran->total ?? 0,
                    'hadir' => $kehadiran->hadir ?? 0,
                    'persen' => ($kehadiran->total ?? 0) > 0 ? round((($kehadiran->hadir ?? 0) / $kehadiran->total) * 100, 1) : 0,
                ];
            });

        return view('admin.kegiatan.riwayat.detail-santri', compact(
            'santri',
            'stats',
            'statsByKategori',
            'riwayat30Hari',
            'riwayats',
            'statsByKelasSantri'
        ));
    }

    /**
     * Show detail riwayat per kegiatan
     */
    public function show($id, Request $request)
    {
        $kegiatan = Kegiatan::with(['kategori', 'kelasKegiatan.kelompok'])
            ->findOrFail($id);

        // Query riwayat absensi untuk kegiatan ini
        $query = AbsensiKegiatan::with(['santri.kelasSantri.kelas.kelompok'])
            ->where('kegiatan_id', $kegiatan->kegiatan_id);

        // Filter Santri
        if ($request->filled('id_santri')) {
            $query->where('id_santri', $request->id_santri);
        }

        // Filter Kelas
        if ($request->filled('id_kelas')) {
            $query->whereHas('santri.kelasSantri', function($q) use ($request) {
                $q->where('id_kelas', $request->id_kelas);
            });
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

        $kelasList = Kelas::active()->ordered()->with('kelompok')->get();

        // Statistik untuk kegiatan ini (sesuai filter)
        $statsQuery = AbsensiKegiatan::where('kegiatan_id', $kegiatan->kegiatan_id);
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
        if ($request->filled('id_kelas')) {
            $statsQuery->whereHas('santri.kelasSantri', function($q) use ($request) {
                $q->where('id_kelas', $request->id_kelas);
            });
        }
        $stats = $statsQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Hitung total SEMUA santri aktif
        $totalSantriEligible = Santri::where('status', 'Aktif')->count();
        $totalRecorded = array_sum($stats);
        $hadirCount = ($stats['Hadir'] ?? 0) + ($stats['Terlambat'] ?? 0);
        $persenHadir = $totalSantriEligible > 0 ? round($hadirCount / $totalSantriEligible * 100, 1) : 0;

        return view('admin.kegiatan.riwayat.show', compact(
            'kegiatan',
            'riwayats',
            'santris',
            'kelasList',
            'stats',
            'totalSantriEligible',
            'totalRecorded',
            'persenHadir'
        ));
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