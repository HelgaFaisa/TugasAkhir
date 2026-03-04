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
        
        // Build santri grouped by kelas
        $santriGrouped = collect();
        $allSantris = Santri::where('status', 'Aktif')
            ->with(['kelasSantri.kelas', 'kelasPrimary.kelas'])
            ->orderBy('nama_lengkap')
            ->get();

        if ($kegiatan->isForAllClasses()) {
            // Kegiatan umum: group by primary kelas
            $santriGrouped = $allSantris->groupBy(function($s) {
                $primary = $s->kelasPrimary;
                return $primary && $primary->kelas ? $primary->kelas->nama_kelas : 'Tanpa Kelas';
            })->sortKeys();
        } else {
            // Kegiatan khusus: group by kelas yang di-assign ke kegiatan
            $placedIds = [];
            foreach ($kegiatan->kelasKegiatan as $kelas) {
                $santriForKelas = $allSantris->filter(function($s) use ($kelas, &$placedIds) {
                    if (in_array($s->id_santri, $placedIds)) return false;
                    return $s->kelasSantri->contains('id_kelas', $kelas->id);
                });
                foreach ($santriForKelas as $s) {
                    $placedIds[] = $s->id_santri;
                }
                if ($santriForKelas->count() > 0) {
                    $santriGrouped[$kelas->nama_kelas] = $santriForKelas;
                }
            }
            // Santri yang tidak termasuk kelas kegiatan manapun
            $santriLainnya = $allSantris->whereNotIn('id_santri', $placedIds);
            if ($santriLainnya->count() > 0) {
                $santriGrouped['Kelas Lain'] = $santriLainnya;
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

        // Build kelas list for filter dropdown — selalu tampilkan semua kelas aktif
        $kelasFilterList = Kelas::active()->ordered()->get();

        // Grup per kelas — selalu group by kelas_name santri
        $absensiPerKelas = $absensis->groupBy(function ($item) {
            return $item->santri->kelas_name ?? 'Belum Ada Kelas';
        })->sortKeys();

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

        // ── Hitung total SEMUA santri aktif ──
        $allSantriQuery = Santri::where('status', 'Aktif');
        if ($request->filled('kelas_id')) {
            $allSantriQuery->whereHas('kelasSantri', function($q) use ($request) {
                $q->where('id_kelas', $request->kelas_id);
            });
        }
        $totalSantriEligible = $allSantriQuery->count();

        // Hitung santri unik yang sudah tercatat absensi (sesuai filter)
        $recordedQuery = AbsensiKegiatan::where('kegiatan_id', $kegiatan_id);
        if ($request->filled('tanggal')) {
            $recordedQuery->whereDate('tanggal', $request->tanggal);
        }
        if ($request->filled('bulan')) {
            $recordedQuery->whereMonth('tanggal', date('m', strtotime($request->bulan)))
                          ->whereYear('tanggal', date('Y', strtotime($request->bulan)));
        }
        if ($request->filled('kelas_id')) {
            $recordedQuery->whereHas('santri.kelasSantri', function($q) use ($request) {
                $q->where('id_kelas', $request->kelas_id);
            });
        }
        $santriSudahAbsen = $recordedQuery->distinct('id_santri')->count('id_santri');
        $belumAbsen = max(0, $totalSantriEligible - $santriSudahAbsen);

        // Persentase kehadiran berdasarkan total semua santri aktif
        $totalRecorded = array_sum($stats);
        $hadirCount = ($stats['Hadir'] ?? 0) + ($stats['Terlambat'] ?? 0);
        $persenHadir = $totalSantriEligible > 0 ? round($hadirCount / $totalSantriEligible * 100, 1) : 0;

        // Daftar santri yang belum absen (selalu ditampilkan)
        $santriBelumAbsen = collect();

        // Bangun query ID santri yang sudah absen (sesuai filter aktif)
        $sudahAbsenQuery = AbsensiKegiatan::where('kegiatan_id', $kegiatan_id);
        if ($request->filled('tanggal')) {
            $sudahAbsenQuery->whereDate('tanggal', $request->tanggal);
        }
        if ($request->filled('bulan')) {
            $sudahAbsenQuery->whereMonth('tanggal', date('m', strtotime($request->bulan)))
                            ->whereYear('tanggal', date('Y', strtotime($request->bulan)));
        }
        if ($request->filled('kelas_id')) {
            $sudahAbsenQuery->whereHas('santri.kelasSantri', function($q) use ($request) {
                $q->where('id_kelas', $request->kelas_id);
            });
        }
        $idSantriSudahAbsen = $sudahAbsenQuery->pluck('id_santri')->unique()->toArray();

        $belumQuery = Santri::where('status', 'Aktif');
        if ($request->filled('kelas_id')) {
            $belumQuery->whereHas('kelasSantri', function($q) use ($request) {
                $q->where('id_kelas', $request->kelas_id);
            });
        }
        $santriBelumAbsen = $belumQuery
            ->whereNotIn('id_santri', $idSantriSudahAbsen)
            ->with(['kelasPrimary.kelas'])
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.kegiatan.absensi.rekap', compact(
            'kegiatan', 'absensis', 'absensiPerKelas', 'stats', 'kelasFilterList',
            'totalSantriEligible', 'santriSudahAbsen', 'belumAbsen', 'persenHadir',
            'totalRecorded', 'hadirCount', 'santriBelumAbsen'
        ));
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