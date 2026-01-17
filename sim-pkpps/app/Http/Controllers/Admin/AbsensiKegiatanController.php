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
        $query = Kegiatan::with('kategori');

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        $kegiatans = $query->orderBy('hari')->orderBy('waktu_mulai')->paginate(10);
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];

        return view('admin.kegiatan.absensi.index', compact('kegiatans', 'hariList'));
    }

    /**
     * Form input absensi
     */
    public function inputAbsensi($kegiatan_id)
    {
        $kegiatan = Kegiatan::with('kategori')->where('kegiatan_id', $kegiatan_id)->firstOrFail();
        $tanggal = request('tanggal', now()->format('Y-m-d'));
        
        // Ambil semua santri aktif
        $santris = Santri::where('status', 'Aktif')
            ->select('id', 'id_santri', 'nama_lengkap', 'kelas', 'rfid_uid')
            ->orderBy('nama_lengkap')
            ->get();

        // Ambil data absensi yang sudah ada
        $absensiData = AbsensiKegiatan::where('kegiatan_id', $kegiatan_id)
            ->whereDate('tanggal', $tanggal)
            ->pluck('status', 'id_santri')
            ->toArray();

        return view('admin.kegiatan.absensi.input', compact('kegiatan', 'santris', 'absensiData', 'tanggal'));
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
        $kegiatan = Kegiatan::with('kategori')->where('kegiatan_id', $kegiatan_id)->firstOrFail();
        
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