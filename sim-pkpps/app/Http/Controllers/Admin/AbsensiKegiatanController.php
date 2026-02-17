<?php
// app/Http/Controllers/admin/AbsensiKegiatanController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKegiatan;
use App\Models\Kegiatan;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiKegiatanController extends Controller
{
    /**
     * Daftar kegiatan untuk absensi
     */
    public function index(Request $request)
    {
        // Query dengan eager loading untuk optimasi
        $query = Kegiatan::with(['kategori', 'kelasKegiatan'])
            ->select('id', 'kegiatan_id', 'kategori_id', 'nama_kegiatan', 'hari', 'waktu_mulai', 'waktu_selesai');

        // Filter Hari
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        // Filter Kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter Kelas
        if ($request->filled('id_kelas')) {
            $query->whereHas('kelasKegiatan', function($q) use ($request) {
                $q->where('kelas.id', $request->id_kelas);
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                  ->orWhere('kegiatan_id', 'like', "%{$search}%");
            });
        }

        // Pagination dengan 15 item per page
        $kegiatans = $query->orderBy('hari')->orderBy('waktu_mulai')->paginate(15)->appends(request()->query());
        
        // Data untuk filter
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
        $kategoris = \App\Models\KategoriKegiatan::select('kategori_id', 'nama_kategori')->get();
        $kelasList = \App\Models\Kelas::with('kelompok')->orderBy('urutan')->get();

        return view('admin.kegiatan.absensi.index', compact('kegiatans', 'hariList', 'kategoris', 'kelasList'));
    }

    /**
     * Form input absensi
     */
    public function inputAbsensi($kegiatan_id)
    {
        // Get kegiatan dengan relasi kategori dan kelas
        $kegiatan = Kegiatan::with(['kategori', 'kelasKegiatan'])
            ->where('kegiatan_id', $kegiatan_id)
            ->firstOrFail();
            
        $tanggal = request('tanggal', now()->format('Y-m-d'));
        
        // Get santri sesuai kelas kegiatan
        if ($kegiatan->isForAllClasses()) {
            // Kegiatan umum: ambil SEMUA santri aktif
            $santris = Santri::where('status', 'Aktif')
                ->with('kelasSantri.kelas')
                ->orderBy('nama_lengkap')
                ->get();
        } else {
            // Kegiatan khusus: ambil santri yang kelasnya match
            $kelasIds = $kegiatan->kelasKegiatan->pluck('id')->toArray();
            
            // Coba ambil santri dari sistem kelas baru
            $santris = Santri::where('status', 'Aktif')
                ->whereHas('kelasSantri', function($query) use ($kelasIds) {
                    $query->whereIn('id_kelas', $kelasIds);
                })
                ->with('kelasSantri.kelas')
                ->orderBy('nama_lengkap')
                ->get();
            
            // Fallback: Jika tidak ada santri (belum migrasi), gunakan old column kelas
            if ($santris->isEmpty()) {
                $kelasNames = $kegiatan->kelasKegiatan->pluck('nama_kelas')->toArray();
                $santris = Santri::where('status', 'Aktif')
                    ->whereIn('kelas', $kelasNames)
                    ->with('kelasSantri.kelas')
                    ->orderBy('nama_lengkap')
                    ->get();
            }
        }

        // Ambil data absensi yang sudah ada
        $absensiData = AbsensiKegiatan::where('kegiatan_id', $kegiatan_id)
            ->whereDate('tanggal', $tanggal)
            ->pluck('status', 'id_santri')
            ->toArray();

        // Info kelas kegiatan untuk view
        $kegiatanInfo = [
            'is_umum' => $kegiatan->isForAllClasses(),
            'kelas_list' => $kegiatan->kelasKegiatan->pluck('nama_kelas')->implode(', '),
            'jumlah_kelas' => $kegiatan->kelasKegiatan->count(),
        ];

        return view('admin.kegiatan.absensi.input', compact('kegiatan', 'santris', 'absensiData', 'tanggal', 'kegiatanInfo'));
    }

    /**
     * Simpan absensi manual
     */
    public function simpanAbsensi(Request $request)
    {
        $validated = $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,kegiatan_id',
            'tanggal' => 'required|date',
            'absensi' => 'required|array',
            'absensi.*' => 'required|in:Hadir,Izin,Sakit,Alpa',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->absensi as $id_santri => $status) {
                AbsensiKegiatan::updateOrCreate(
                    [
                        'kegiatan_id' => $request->kegiatan_id,
                        'id_santri' => $id_santri,
                        'tanggal' => $request->tanggal,
                    ],
                    [
                        'status' => $status,
                        'metode_absen' => 'Manual',
                        'waktu_absen' => now()->format('H:i:s'),
                    ]
                );
            }

            DB::commit();
            return redirect()->route('admin.absensi-kegiatan.index')
                ->with('success', 'Absensi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    /**
     * Rekap absensi kegiatan
     */
    public function rekapAbsensi(Request $request, $kegiatan_id)
    {
        $kegiatan = Kegiatan::with(['kategori', 'kelasKegiatan'])->where('kegiatan_id', $kegiatan_id)->firstOrFail();
        
        $query = AbsensiKegiatan::with('santri')
            ->where('kegiatan_id', $kegiatan_id);

        // Filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', date('m', strtotime($request->bulan)))
                  ->whereYear('tanggal', date('Y', strtotime($request->bulan)));
        }

        $absensis = $query->orderBy('tanggal', 'desc')
            ->orderBy('waktu_absen', 'desc')
            ->paginate(20);

        // Statistik
        $stats = AbsensiKegiatan::where('kegiatan_id', $kegiatan_id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('admin.kegiatan.absensi.rekap', compact('kegiatan', 'absensis', 'stats'));
    }

    /**
     * Scan RFID (API untuk JavaScript)
     */
    public function scanRfid(Request $request)
    {
        $validated = $request->validate([
            'rfid_uid' => 'required|string',
            'kegiatan_id' => 'required|exists:kegiatans,kegiatan_id',
            'tanggal' => 'required|date',
        ]);

        // Cari santri berdasarkan RFID
        $santri = Santri::where('rfid_uid', $request->rfid_uid)
            ->where('status', 'Aktif')
            ->first();

        if (!$santri) {
            return response()->json([
                'success' => false,
                'message' => 'RFID tidak terdaftar atau santri tidak aktif.'
            ], 404);
        }

        // Cek apakah sudah absen hari ini
        $existing = AbsensiKegiatan::where('kegiatan_id', $request->kegiatan_id)
            ->where('id_santri', $santri->id_santri)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => $santri->nama_lengkap . ' sudah melakukan absensi (' . $existing->status . ').'
            ], 400);
        }

        // Simpan absensi
        $absensi = AbsensiKegiatan::create([
            'kegiatan_id' => $request->kegiatan_id,
            'id_santri' => $santri->id_santri,
            'tanggal' => $request->tanggal,
            'status' => 'Hadir',
            'metode_absen' => 'RFID',
            'waktu_absen' => now()->format('H:i:s'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil untuk ' . $santri->nama_lengkap,
            'data' => [
                'nama' => $santri->nama_lengkap,
                'id_santri' => $santri->id_santri,
                'kelas' => $santri->kelas,
                'waktu' => now()->format('H:i:s'),
            ]
        ]);
    }
}