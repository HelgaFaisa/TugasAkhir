<?php
// app/Http/Controllers/Santri/RiwayatKegiatanSantriController.php
namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKegiatan;
use App\Models\Kegiatan;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiwayatKegiatanSantriController extends Controller
{
    /**
     * Halaman utama: Jadwal Harian + Riwayat Absensi
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'santri') {
            abort(403, 'Akses ditolak.');
        }
        
        $santri = Santri::where('id_santri', $user->role_id)
            ->select('id_santri', 'nama_lengkap', 'kelas')
            ->firstOrFail();
        
        $idSantri = $santri->id_santri;
        $today = Carbon::today();
        $hariIni = Carbon::now()->locale('id')->dayName; // Senin, Selasa, etc.
        
        // ✅ JADWAL KEGIATAN HARI INI (Tetap)
        $jadwalHariIni = Kegiatan::with('kategori')
            ->where('hari', ucfirst($hariIni))
            ->select('kegiatan_id', 'kategori_id', 'nama_kegiatan', 'waktu_mulai', 'waktu_selesai', 'materi')
            ->orderBy('waktu_mulai')
            ->get();
        
        // ✅ CEK STATUS ABSENSI HARI INI
        $absensiHariIni = AbsensiKegiatan::where('id_santri', $idSantri)
            ->whereDate('tanggal', $today)
            ->pluck('status', 'kegiatan_id')
            ->toArray();
        
        // ✅ RIWAYAT ABSENSI (dengan filter)
        $query = AbsensiKegiatan::with('kegiatan.kategori')
            ->where('id_santri', $idSantri);
        
        // Filter Bulan
        if ($request->filled('bulan')) {
            $bulan = Carbon::parse($request->bulan);
            $query->whereMonth('tanggal', $bulan->month)
                  ->whereYear('tanggal', $bulan->year);
        }
        
        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $riwayats = $query->orderBy('tanggal', 'desc')
            ->orderBy('waktu_absen', 'desc')
            ->paginate(15)
            ->appends(request()->query());
        
        // ✅ STATISTIK KEHADIRAN (30 HARI TERAKHIR)
        $stats30Hari = AbsensiKegiatan::where('id_santri', $idSantri)
            ->whereDate('tanggal', '>=', Carbon::now()->subDays(30))
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        
        $totalKegiatan30Hari = array_sum($stats30Hari);
        $persentaseKehadiran = $totalKegiatan30Hari > 0 
            ? round(($stats30Hari['Hadir'] ?? 0) / $totalKegiatan30Hari * 100, 1) 
            : 0;
        
        // ✅ DATA GRAFIK: Kehadiran per Minggu (4 Minggu Terakhir)
        $dataGrafikMingguan = [];
        for ($i = 3; $i >= 0; $i--) {
            $startWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $hadir = AbsensiKegiatan::where('id_santri', $idSantri)
                ->whereBetween('tanggal', [$startWeek, $endWeek])
                ->where('status', 'Hadir')
                ->count();
            
            $total = AbsensiKegiatan::where('id_santri', $idSantri)
                ->whereBetween('tanggal', [$startWeek, $endWeek])
                ->count();
            
            $dataGrafikMingguan[] = [
                'minggu' => 'Minggu ' . (4 - $i),
                'hadir' => $hadir,
                'total' => $total,
                'persentase' => $total > 0 ? round($hadir / $total * 100, 1) : 0,
            ];
        }
        
        // ✅ STATISTIK PER KATEGORI KEGIATAN
        $statsByKategori = AbsensiKegiatan::where('id_santri', $idSantri)
            ->join('kegiatans', 'absensi_kegiatans.kegiatan_id', '=', 'kegiatans.kegiatan_id')
            ->join('kategori_kegiatans', 'kegiatans.kategori_id', '=', 'kategori_kegiatans.kategori_id')
            ->select(
                'kategori_kegiatans.nama_kategori',
                DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('kategori_kegiatans.nama_kategori')
            ->get();
        
        return view('santri.kegiatan.index', compact(
            'santri',
            'jadwalHariIni',
            'absensiHariIni',
            'riwayats',
            'stats30Hari',
            'totalKegiatan30Hari',
            'persentaseKehadiran',
            'dataGrafikMingguan',
            'statsByKategori',
            'hariIni'
        ));
    }
    
    /**
     * Detail Riwayat Absensi per Kegiatan
     */
    public function show($kegiatan_id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'santri') {
            abort(403, 'Akses ditolak.');
        }
        
        $santri = Santri::where('id_santri', $user->role_id)
            ->select('id_santri', 'nama_lengkap')
            ->firstOrFail();
        
        $kegiatan = Kegiatan::with('kategori')
            ->where('kegiatan_id', $kegiatan_id)
            ->firstOrFail();
        
        // Riwayat absensi untuk kegiatan ini
        $riwayats = AbsensiKegiatan::where('id_santri', $santri->id_santri)
            ->where('kegiatan_id', $kegiatan_id)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);
        
        // Statistik kehadiran untuk kegiatan ini
        $stats = AbsensiKegiatan::where('id_santri', $santri->id_santri)
            ->where('kegiatan_id', $kegiatan_id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        
        $totalAbsensi = array_sum($stats);
        $persentaseHadir = $totalAbsensi > 0 
            ? round(($stats['Hadir'] ?? 0) / $totalAbsensi * 100, 1) 
            : 0;
        
        return view('santri.kegiatan.show', compact(
            'santri',
            'kegiatan',
            'riwayats',
            'stats',
            'totalAbsensi',
            'persentaseHadir'
        ));
    }
}