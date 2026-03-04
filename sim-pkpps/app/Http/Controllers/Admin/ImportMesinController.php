<?php
// app/Http/Controllers/Admin/ImportMesinController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKegiatan;
use App\Models\ImportMesinLog;
use App\Models\Kegiatan;
use App\Models\Kepulangan;
use App\Models\MesinSantriMapping;
use App\Services\EpposGLogParser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportMesinController extends Controller
{
    public function __construct(private EpposGLogParser $parser) {}

    // ──────────────────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────────────────
    public function index()
    {
        // Hitung yang benar-benar belum punya santri (id_santri null atau kosong)
        $belumMapping = MesinSantriMapping::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('id_santri')->orWhere('id_santri', '');
            })->count();

        $riwayat = ImportMesinLog::with('user')->latest()->take(10)->get();

        return view('admin.mesin.import.index', compact('belumMapping', 'riwayat'));
    }

    // ──────────────────────────────────────────────────────────
    // PREVIEW — hanya POST
    // Setelah proses selesai, redirect ke showPreview (GET)
    // Ini mencegah error "MethodNotAllowed" saat user refresh halaman preview
    // ──────────────────────────────────────────────────────────
    public function preview(Request $request)
    {
        $request->validate([
            'file_glog'         => 'required|file|max:20480',
            'tol_sebelum'       => 'nullable|integer|min:0|max:60',
            'tol_sesudah'       => 'nullable|integer|min:0|max:60',
            'isi_alpa'          => 'nullable',
            'conflict_strategy' => 'nullable|in:mesin,exist,manual',
        ]);

        $tolSebelum       = (int)($request->tol_sebelum ?? 15);
        $tolSesudah       = (int)($request->tol_sesudah ?? 10);
        $isiAlpa          = $request->has('isi_alpa');
        $conflictStrategy = $request->input('conflict_strategy', 'mesin');

        // ── Parse GLog ────────────────────────────────────────
        try {
            $glogRecords = $this->parser->parseGLog(
                $request->file('file_glog')->getPathname()
            );
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membaca file GLog: ' . $e->getMessage());
        }

        if (empty($glogRecords)) {
            return back()->with('error',
                'File GLog tidak mengandung data scan yang valid. ' .
                'Pastikan file yang diupload benar (format GLog dari Eppos).'
            );
        }

        // ── Ambil infoData dari mapping yang sudah ada ────────
        // Kita bangun infoData dari tabel mesin_santri_mappings
        // sehingga tidak perlu upload INFO.XLS lagi
        $mappingAll = MesinSantriMapping::where('is_active', true)->get();

        // Bangun struktur infoData['jadwal'] dari mapping yang ada
        // shifts dikosongkan karena matching pakai jam langsung
        $infoData = [
            'shifts' => [],
            'jadwal' => [],
        ];
        foreach ($mappingAll as $m) {
            $infoData['jadwal'][$m->id_mesin] = [
                'nama'  => $m->nama_mesin ?? '',
                'dept'  => $m->dept_mesin ?? '',
                'shift' => 1, // default, tidak dipakai untuk matching jam
            ];
        }

        // ── Kegiatan dari DB ──────────────────────────────────
        // Ambil semua kegiatan — waktu_selesai boleh null, pakai waktu_mulai sebagai fallback
        // getRawOriginal() bypass Eloquent cast (datetime:H:i → Carbon)
        // sehingga kita dapat string murni "04:00:00" dari DB, lalu substr → "04:00"
        $kegiatans = Kegiatan::orderBy('hari')->orderBy('waktu_mulai')
            ->get()
            ->map(function ($k) {
                $rawMulai   = $k->getRawOriginal('waktu_mulai');
                $rawSelesai = $k->getRawOriginal('waktu_selesai');
                $mulai      = $rawMulai   ? substr($rawMulai, 0, 5)   : '00:00';
                $selesai    = $rawSelesai ? substr($rawSelesai, 0, 5) : $mulai;
                return [
                    'kegiatan_id'   => $k->kegiatan_id,
                    'nama'          => $k->nama_kegiatan,
                    'hari'          => $k->hari,
                    'waktu_mulai'   => $mulai,
                    'waktu_selesai' => $selesai,
                ];
            })->toArray();

        if (empty($kegiatans)) {
            return back()->with('error',
                'Tidak ada kegiatan tersimpan di database. ' .
                'Tambahkan kegiatan terlebih dahulu di menu Kegiatan.'
            );
        }

        // ── Match ─────────────────────────────────────────────
        $glogGrouped = $this->parser->groupGLogByDay($glogRecords);
        $rawHasil    = $this->parser->matchToKegiatan(
            $glogGrouped, $infoData, $kegiatans, $tolSebelum, $tolSesudah
        );

        // ── Enrich (santri web + kepulangan + konflik) ────────
        $kepulanganCache = [];
        $hasilEnriched   = [];

        foreach ($rawHasil as $dayData) {
            $tanggal = $dayData['tanggal'];
            $idMesin = $dayData['id_mesin'];

            $mapping  = MesinSantriMapping::where('id_mesin', $idMesin)
                ->where('is_active', true)
                ->with('santri')
                ->first();

            $idSantri = $mapping?->santri?->id_santri;
            $namaWeb  = $mapping?->santri?->nama_lengkap;
            $kelas    = $mapping?->santri?->kelasPrimary?->kelas?->nama_kelas ?? '-';

            // Cache kepulangan per tanggal agar tidak query berulang
            if (!isset($kepulanganCache[$tanggal])) {
                $kepulanganCache[$tanggal] = Kepulangan::where('status', 'Disetujui')
                    ->where('tanggal_pulang', '<=', $tanggal)
                    ->where('tanggal_kembali', '>=', $tanggal)
                    ->pluck('id_santri')->toArray();
            }
            $isPulang = $idSantri && in_array($idSantri, $kepulanganCache[$tanggal]);

            $rows = array_map(
                function ($row) use ($idSantri, $tanggal, $isPulang, $isiAlpa) {
                    // Override jika santri sedang kepulangan
                    $statusFinal = $isPulang ? 'Pulang' : $row['status'];

                    // Jangan isi Alpa jika opsi tidak aktif
                    if (!$isiAlpa && $statusFinal === 'Alpa' && !$row['matched']) {
                        $statusFinal = null;
                    }

                    $existing   = null;
                    $isConflict = false;

                    if ($idSantri) {
                        $rec = AbsensiKegiatan::where('kegiatan_id', $row['kegiatan_id'])
                            ->where('id_santri', $idSantri)
                            ->whereDate('tanggal', $tanggal)
                            ->first();

                        if ($rec) {
                            // getRawOriginal bypass Eloquent datetime cast
                            $rawWaktu = $rec->getRawOriginal('waktu_absen');
                            $existing = [
                                'status' => $rec->status,
                                'waktu'  => $rawWaktu
                                    ? substr($rawWaktu, 0, 5) : null,
                                'metode' => $rec->metode_absen ?? 'Manual',
                            ];

                            // PENTING: Jika mesin TIDAK punya scan untuk kegiatan
                            // ini (matched=false, status=Alpa), jangan override
                            // data manual yang sudah ada. "Tidak ada scan" ≠ "Alpa".
                            // Pertahankan data lama secara otomatis.
                            if (!$row['matched'] && $statusFinal === 'Alpa') {
                                // Tidak override — pakai data existing
                                $statusFinal = $rec->status;
                                $isConflict  = false;
                            } else {
                                // Konflik hanya jika mesin MEMANG punya scan
                                // (matched=true) tapi statusnya beda dari manual
                                $isConflict = ($rec->metode_absen !== 'Import_Mesin')
                                    && ($rec->status !== $statusFinal)
                                    && $statusFinal !== null;
                            }
                        }
                    }

                    return array_merge($row, [
                        'status_final' => $statusFinal,
                        'existing'     => $existing,
                        'is_conflict'  => $isConflict,
                    ]);
                },
                $dayData['rows']
            );

            $hasilEnriched[] = array_merge($dayData, [
                'id_santri'    => $idSantri,
                'nama_web'     => $namaWeb,
                'kelas'        => $kelas,
                'match_status' => $mapping
                    ? ($idSantri ? 'OK' : 'NO_SANTRI')
                    : 'NOT_MAPPED',
                'is_pulang'    => $isPulang,
                'rows'         => $rows,
            ]);
        }

        // Urutkan: tanggal → nama
        usort($hasilEnriched, fn($a, $b) =>
            [$a['tanggal'], $a['nama_web'] ?? $a['nama_mesin']]
            <=> [$b['tanggal'], $b['nama_web'] ?? $b['nama_mesin']]
        );

        // ── Simpan ke session lalu REDIRECT ke showPreview ────
        // Ini adalah PRG Pattern (Post-Redirect-Get):
        // POST  /import/preview  → proses → session → redirect
        // GET   /import/preview  → ambil dari session → tampilkan view
        // Sehingga refresh halaman tidak error MethodNotAllowed
        session([
            'eppos_hasil'        => $hasilEnriched,
            'tol_sebelum'        => $tolSebelum,
            'tol_sesudah'        => $tolSesudah,
            'isi_alpa'           => $isiAlpa,
            'conflict_strategy'  => $conflictStrategy,
        ]);

        return redirect()->route('admin.mesin.import.show-preview');
    }

    // ──────────────────────────────────────────────────────────
    // SHOW PREVIEW — GET (aman di-refresh)
    // ──────────────────────────────────────────────────────────
    public function showPreview()
    {
        $hasilEnriched = session('eppos_hasil');

        // Jika session kosong (user buka langsung tanpa upload)
        if (empty($hasilEnriched)) {
            return redirect()->route('admin.mesin.import.index')
                ->with('error', 'Tidak ada data preview. Silakan upload file GLog terlebih dahulu.');
        }

        $tolSebelum       = session('tol_sebelum', 15);
        $tolSesudah       = session('tol_sesudah', 10);
        $isiAlpa          = session('isi_alpa', true);
        $conflictStrategy = session('conflict_strategy', 'mesin');

        $tanggalList = array_unique(array_column($hasilEnriched, 'tanggal'));
        sort($tanggalList);

        // Debug: kumpulkan info scan yang tidak cocok untuk ditampilkan
        $debugScans = [];
        foreach ($hasilEnriched as $h) {
            if (!empty($h['unmatched_scans'])) {
                $debugScans[] = [
                    'nama'     => $h['nama_web'] ?? $h['nama_mesin'],
                    'tanggal'  => $h['tanggal'],
                    'id_mesin' => $h['id_mesin'],
                    'scans'    => $h['all_scans'],
                    'unmatched'=> $h['unmatched_scans'],
                ];
            }
        }

        $allRows = collect($hasilEnriched)->flatMap(fn($h) => $h['rows']);

        $stats = [
            'total_santri' => count($hasilEnriched),
            'ok'           => collect($hasilEnriched)->where('match_status', 'OK')->count(),
            'not_mapped'   => collect($hasilEnriched)->where('match_status', 'NOT_MAPPED')->count(),
            'hadir'        => $allRows->where('status_final', 'Hadir')->count(),
            'terlambat'    => $allRows->where('status_final', 'Terlambat')->count(),
            'alpa'         => $allRows->where('status_final', 'Alpa')->count(),
            'konflik'      => $allRows->where('is_conflict', true)->count(),
        ];

        return view('admin.mesin.import.preview', compact(
            'hasilEnriched', 'tanggalList', 'stats',
            'tolSebelum', 'tolSesudah', 'isiAlpa',
            'debugScans', 'conflictStrategy'
        ));
    }

    // ──────────────────────────────────────────────────────────
    // STORE — simpan ke database
    // ──────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $hasilEnriched = session('eppos_hasil', []);

        if (empty($hasilEnriched)) {
            return redirect()->route('admin.mesin.import.index')
                ->with('error', 'Sesi expired. Silakan upload ulang file GLog.');
        }

        $bulkStrategy = $request->input('conflict_strategy', 'manual');
        $choices      = $request->input('conflict_choices', []);
        $counters     = [
            'created'    => 0,
            'updated'    => 0,
            'kept'       => 0,
            'skipped'    => 0,
            'no_santri'  => 0,
            'null_skip'  => 0,
        ];

        DB::beginTransaction();
        try {
            foreach ($hasilEnriched as $dayData) {
                if (!$dayData['id_santri']) {
                    $counters['no_santri']++;
                    continue;
                }

                foreach ($dayData['rows'] as $row) {
                    // Status null = tidak perlu disimpan
                    if ($row['status_final'] === null) {
                        $counters['null_skip']++;
                        continue;
                    }

                    // Alpa tanpa scan (matched=false) + sudah ada data existing
                    // → pertahankan data lama, jangan simpan Alpa
                    if (!$row['matched'] && $row['status_final'] === 'Alpa' && !empty($row['existing'])) {
                        $counters['skipped']++;
                        continue;
                    }

                    // Jika mesin tidak punya scan dan statusFinal = status existing
                    // (artinya sudah diset ke status existing di preview), skip
                    if (!$row['matched'] && !empty($row['existing'])
                        && $row['status_final'] === $row['existing']['status']) {
                        $counters['skipped']++;
                        continue;
                    }

                    $key         = "{$row['kegiatan_id']}_{$dayData['id_santri']}_{$dayData['tanggal']}";
                    $hasExisting = !empty($row['existing']);
                    $isConflict  = $row['is_conflict'] ?? false;

                    if (!$hasExisting) {
                        // Belum ada data → langsung buat
                        AbsensiKegiatan::create([
                            'kegiatan_id'  => $row['kegiatan_id'],
                            'id_santri'    => $dayData['id_santri'],
                            'tanggal'      => $dayData['tanggal'],
                            'status'       => $row['status_final'],
                            'metode_absen' => 'Import_Mesin',
                            'waktu_absen'  => $row['jam_scan']
                                ? Carbon::parse(
                                    $dayData['tanggal'] . ' ' . $row['jam_scan']
                                  )->format('H:i:s')
                                : Carbon::parse($dayData['tanggal'])->format('H:i:s'),
                        ]);
                        $counters['created']++;
                        continue;
                    }

                    // Ada data existing tapi tidak konflik (status sama)
                    // → skip, tidak perlu diubah
                    if (!$isConflict) {
                        $counters['skipped']++;
                        continue;
                    }

                    // Ada konflik → lihat strategi bulk dulu, baru per-cell
                    $choice = ($bulkStrategy !== 'manual')
                        ? $bulkStrategy
                        : ($choices[$key] ?? null);

                    if ($choice === 'mesin') {
                        // Admin pilih: pakai data mesin
                        AbsensiKegiatan::where('kegiatan_id', $row['kegiatan_id'])
                            ->where('id_santri', $dayData['id_santri'])
                            ->whereDate('tanggal', $dayData['tanggal'])
                            ->update([
                                'status'          => $row['status_final'],
                                'metode_absen'    => 'Import_Mesin',
                                'waktu_absen'     => $row['jam_scan']
                                    ? Carbon::parse(
                                        $dayData['tanggal'] . ' ' . $row['jam_scan']
                                      )->format('H:i:s')
                                    : null,
                                'konflik_catatan' => 'Ditimpa import mesin '
                                    . now()->format('d/m/Y H:i')
                                    . ' (sebelumnya: '
                                    . $row['existing']['status']
                                    . ' via '
                                    . ($row['existing']['metode'] ?? 'Manual')
                                    . ')',
                            ]);
                        $counters['updated']++;
                    } else {
                        // Admin pilih: pertahankan data lama
                        // Tidak melakukan apa-apa
                        $counters['kept']++;
                    }
                }
            }

            // Catat ke log
            ImportMesinLog::create([
                'user_id'          => auth()->id(),
                'jumlah_scan'      => collect($hasilEnriched)
                    ->flatMap(fn($h) => $h['all_scans'])->count(),
                'berhasil'         => $counters['created'],
                'konflik_selesai'  => $counters['updated'],
                'dilewati'         => $counters['skipped'],
                'no_santri'        => $counters['no_santri'],
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }

        // Hapus session setelah berhasil
        session()->forget(['eppos_hasil', 'tol_sebelum', 'tol_sesudah', 'isi_alpa', 'conflict_strategy']);

        $msg = "Import selesai! "
             . "{$counters['created']} data baru tersimpan, "
             . "{$counters['updated']} konflik (pilih mesin), "
             . "{$counters['kept']} konflik (pertahankan data lama), "
             . "{$counters['skipped']} duplikat dilewati.";

        if ($counters['no_santri'] > 0) {
            $msg .= " | {$counters['no_santri']} santri belum ada mapping (tidak tersimpan).";
        }

        return redirect()->route('admin.riwayat-kegiatan.index')
            ->with('success', $msg);
    }
}