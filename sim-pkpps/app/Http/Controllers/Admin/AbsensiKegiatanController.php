<?php
// app/Http/Controllers/admin/AbsensiKegiatanController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKegiatan;
use App\Models\Kegiatan;
use App\Models\Kelas;
use App\Models\Kepulangan;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiKegiatanController extends Controller
{
    /**
     * Daftar kegiatan untuk absensi — diarahkan ke Dashboard Kegiatan (tidak redundan)
     */
    public function index(Request $request)
    {
        return redirect()->route('admin.kegiatan.jadwal');
    }

    /**
     * Form input absensi
     */
    public function inputAbsensi($kegiatan_id)
    {
        // Get kegiatan dengan relasi kategori dan kelas
        $kegiatan = Kegiatan::with(['kategori', 'kelasKegiatan.kelompok'])
            ->where('kegiatan_id', $kegiatan_id)
            ->firstOrFail();
            
        $tanggal = request('tanggal', now()->format('Y-m-d'));
        
        // Build santri grouped by kegiatan kelas
        $santriGrouped = collect();
        
        if ($kegiatan->isForAllClasses()) {
            // Kegiatan umum: ambil SEMUA santri aktif, group by primary kelas
            $allSantris = Santri::where('status', 'Aktif')
                ->with(['kelasSantri.kelas', 'kelasPrimary.kelas'])
                ->orderBy('nama_lengkap')
                ->get();
            
            $santriGrouped = $allSantris->groupBy(function($s) {
                $primary = $s->kelasPrimary;
                return $primary && $primary->kelas ? $primary->kelas->nama_kelas : 'Tanpa Kelas';
            })->sortKeys();
        } else {
            // Kegiatan khusus: group by kegiatan kelas
            foreach ($kegiatan->kelasKegiatan as $kelas) {
                $santriInKelas = Santri::where('status', 'Aktif')
                    ->whereHas('kelasSantri', function($q) use ($kelas) {
                        $q->where('id_kelas', $kelas->id);
                    })
                    ->with(['kelasSantri.kelas', 'kelasPrimary.kelas'])
                    ->orderBy('nama_lengkap')
                    ->get();
                
                if ($santriInKelas->count() > 0) {
                    $santriGrouped[$kelas->nama_kelas] = $santriInKelas;
                }
            }
        }
        
        // Flatten for total count
        $santris = $santriGrouped->flatten()->unique('id_santri');

        // Ambil data absensi yang sudah ada
        $absensiData = AbsensiKegiatan::where('kegiatan_id', $kegiatan_id)
            ->whereDate('tanggal', $tanggal)
            ->pluck('status', 'id_santri')
            ->toArray();

        // Cek santri yang sedang pulang
        $santriSedangPulang = Kepulangan::where('status', 'Disetujui')
            ->where('tanggal_pulang', '<=', $tanggal)
            ->where('tanggal_kembali', '>=', $tanggal)
            ->pluck('id_santri')
            ->toArray();

        // Info kelas kegiatan untuk view
        $kegiatanInfo = [
            'is_umum' => $kegiatan->isForAllClasses(),
            'kelas_list' => $kegiatan->kelasKegiatan->pluck('nama_kelas')->implode(', '),
            'jumlah_kelas' => $kegiatan->kelasKegiatan->count(),
        ];

        return view('admin.kegiatan.absensi.input', compact('kegiatan', 'santris', 'santriGrouped', 'absensiData', 'tanggal', 'kegiatanInfo', 'santriSedangPulang'));
    }

    /**
     * Simpan absensi manual (hanya santri yang dikirim form)
     */
    public function simpanAbsensi(Request $request)
    {
        $validated = $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,kegiatan_id',
            'tanggal' => 'required|date',
            'absensi' => 'nullable|array',
            'absensi.*' => 'nullable|in:Hadir,Izin,Sakit,Alpa,Terlambat,Pulang',
        ]);

        // Cek santri yang sedang pulang
        $santriSedangPulang = Kepulangan::where('status', 'Disetujui')
            ->where('tanggal_pulang', '<=', $request->tanggal)
            ->where('tanggal_kembali', '>=', $request->tanggal)
            ->pluck('id_santri')
            ->toArray();

        $absensiInput = $request->absensi ?? [];

        DB::beginTransaction();
        try {
            $saved = 0;
            foreach ($absensiInput as $id_santri => $status) {
                // Skip jika kosong (santri dilewati)
                if (empty($status)) {
                    continue;
                }

                // Paksa status Pulang untuk santri yang sedang pulang
                $finalStatus = in_array($id_santri, $santriSedangPulang) ? 'Pulang' : $status;

                AbsensiKegiatan::updateOrCreate(
                    [
                        'kegiatan_id' => $request->kegiatan_id,
                        'id_santri' => $id_santri,
                        'tanggal' => $request->tanggal,
                    ],
                    [
                        'status' => $finalStatus,
                        'metode_absen' => 'Manual',
                        'waktu_absen' => now()->format('H:i:s'),
                    ]
                );
                $saved++;
            }

            DB::commit();
            return redirect()->route('admin.kegiatan.index')
                ->with('success', "Absensi berhasil disimpan ({$saved} santri).");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    /**
     * Edit single absensi record
     */
    public function editAbsensi($id)
    {
        $absensi = AbsensiKegiatan::with(['santri', 'kegiatan.kategori'])->findOrFail($id);
        return view('admin.kegiatan.absensi.edit', compact('absensi'));
    }

    /**
     * Update single absensi record
     */
    public function updateAbsensi(Request $request, $id)
    {
        $absensi = AbsensiKegiatan::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Hadir,Izin,Sakit,Alpa,Terlambat,Pulang',
        ]);

        $absensi->update([
            'status' => $validated['status'],
            'waktu_absen' => now()->format('H:i:s'),
        ]);

        return redirect()->route('admin.absensi-kegiatan.rekap', $absensi->kegiatan_id)
            ->with('success', 'Status absensi ' . $absensi->santri->nama_lengkap . ' berhasil diperbarui.');
    }

    /**
     * Hapus single absensi record
     */
    public function hapusAbsensi($id)
    {
        $absensi = AbsensiKegiatan::findOrFail($id);
        $kegiatanId = $absensi->kegiatan_id;
        $nama = $absensi->santri->nama_lengkap;
        $absensi->delete();

        return redirect()->route('admin.absensi-kegiatan.rekap', $kegiatanId)
            ->with('success', 'Data absensi ' . $nama . ' berhasil dihapus.');
    }

    /**
     * Rekap absensi kegiatan
     */
    public function rekapAbsensi(Request $request, $kegiatan_id)
    {
        $kegiatan = Kegiatan::with(['kategori', 'kelasKegiatan'])->where('kegiatan_id', $kegiatan_id)->firstOrFail();
        
        $query = AbsensiKegiatan::with(['santri.kelasSantri.kelas'])
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

        // Filter kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('santri.kelasSantri', function($q) use ($request) {
                $q->where('id_kelas', $request->kelas_id);
            });
        }

        $absensis = $query->orderBy('tanggal', 'desc')
            ->orderBy('waktu_absen', 'desc')
            ->get();

        // Build kelas list for filter dropdown
        if ($kegiatan->isForAllClasses()) {
            $kelasFilterList = Kelas::active()->ordered()->get();
        } else {
            $kelasFilterList = $kegiatan->kelasKegiatan;
        }

        // Grup per kelas berdasarkan kegiatan kelas
        if ($kegiatan->isForAllClasses()) {
            $absensiPerKelas = $absensis->groupBy(function ($item) {
                return $item->santri->kelas_name ?? 'Belum Ada Kelas';
            })->sortKeys();
        } else {
            $absensiPerKelas = collect();
            foreach ($kegiatan->kelasKegiatan as $kelas) {
                $kelasAbsensis = $absensis->filter(function ($item) use ($kelas) {
                    return $item->santri->kelasSantri->contains('id_kelas', $kelas->id);
                });
                if ($kelasAbsensis->count() > 0) {
                    $absensiPerKelas[$kelas->nama_kelas] = $kelasAbsensis;
                }
            }
        }

        // Statistik
        $statsQuery = AbsensiKegiatan::where('kegiatan_id', $kegiatan_id);
        if ($request->filled('tanggal')) {
            $statsQuery->whereDate('tanggal', $request->tanggal);
        }
        if ($request->filled('bulan')) {
            $statsQuery->whereMonth('tanggal', date('m', strtotime($request->bulan)))
                       ->whereYear('tanggal', date('Y', strtotime($request->bulan)));
        }
        if ($request->filled('kelas_id')) {
            $statsQuery->whereHas('santri.kelasSantri', function($q) use ($request) {
                $q->where('id_kelas', $request->kelas_id);
            });
        }
        $stats = $statsQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('admin.kegiatan.absensi.rekap', compact('kegiatan', 'absensis', 'absensiPerKelas', 'stats', 'kelasFilterList'));
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