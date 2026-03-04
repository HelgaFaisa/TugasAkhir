<?php
/**
 * EpposGLogParser.php  — versi 2
 * app/Services/EpposGLogParser.php
 *
 * ═══════════════════════════════════════════════════════════════
 * PERUBAHAN UTAMA dari versi 1:
 *   IOMd TIDAK lagi diabaikan. Setiap slot di shift mesin
 *   (JK1 Masuk, JK1 Pulang, JK2 Masuk, JK2 Pulang, Lb Masuk, Lb Pulang)
 *   bisa dipetakan ke kegiatan web yang BERBEDA.
 *
 * CONTOH MESIN SHOLAT:
 *   JK1 Masuk  (IOMd=2) jam 04:00 → Shubuh
 *   JK1 Pulang (IOMd=4) jam 05:00 → Dhuhur
 *   JK2 Masuk  (IOMd=2) jam 11:45 → Ashar
 *   JK2 Pulang (IOMd=4) jam 12:20 → Maghrib
 *   Lb  Masuk  (IOMd=2) jam 15:05 → Isya
 *
 * CONTOH MESIN NGAJI:
 *   JK1 Masuk  (IOMd=2) jam 05:00 → Ngaji Shubuh
 *   JK1 Pulang (IOMd=4) jam 06:00 → sekolah
 *   JK2 Masuk  (IOMd=2) jam 13:00 → Ngaji Siang
 *   JK2 Pulang (IOMd=4) jam 15:00 → Ngaji Maghrib
 *   Lb  Masuk  (IOMd=2) jam 18:00 → Ngaji Malam
 *
 * ═══════════════════════════════════════════════════════════════
 * FORMAT GLOG.TXT (Tab-Separated):
 *   No | Mchn | EnNo | Name | Mode | IOMd | DateTime
 *   000001 | 1 | 000000001 | helga faisa | 1 | 2 | 2026/02/28  04:05:00
 *
 *   IOMd=2 → scan MASUK  (Check In)
 *   IOMd=4 → scan PULANG (Check Out)
 *
 * FORMAT INFO.XLS:
 *   Sheet "Shift"  → No.Shift | JK1 Msuk | JK1 Kluar | JK2 Msuk | JK2 Kluar | Lb Msuk | Lb Kluar
 *   Sheet "Jadwal" → No | Nama | Departemen | Shift
 * ═══════════════════════════════════════════════════════════════
 */

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class EpposGLogParser
{
    // IOMd values dari mesin Eppos
    const IOMD_MASUK  = 2;
    const IOMD_PULANG = 4;

    // 6 slot per shift (nama slot → key di array shift)
    // Urutan ini penting untuk matching prioritas
    const SLOT_KEYS = [
        'jk1_msuk',   // JK1 Masuk  (IOMd=2)
        'jk1_kluar',  // JK1 Pulang (IOMd=4)
        'jk2_msuk',   // JK2 Masuk  (IOMd=2)
        'jk2_kluar',  // JK2 Pulang (IOMd=4)
        'lb_msuk',    // Lembur Masuk  (IOMd=2)
        'lb_kluar',   // Lembur Pulang (IOMd=4)
    ];

    // Masing-masing slot → IOMd yang diharapkan
    const SLOT_IOMD = [
        'jk1_msuk'  => self::IOMD_MASUK,
        'jk1_kluar' => self::IOMD_PULANG,
        'jk2_msuk'  => self::IOMD_MASUK,
        'jk2_kluar' => self::IOMD_PULANG,
        'lb_msuk'   => self::IOMD_MASUK,
        'lb_kluar'  => self::IOMD_PULANG,
    ];

    // ─────────────────────────────────────────────────────────────
    // PARSE INFO.XLS
    // ─────────────────────────────────────────────────────────────

    /**
     * Parse INFO.XLS → konfigurasi shift dan daftar santri di mesin
     *
     * @return array [
     *   'shifts' => [
     *     1 => [
     *       'jk1_msuk'  => '04:00',
     *       'jk1_kluar' => '05:00',
     *       'jk2_msuk'  => '11:45',
     *       'jk2_kluar' => '12:20',
     *       'lb_msuk'   => '15:05',
     *       'lb_kluar'  => null,   // null = slot tidak dipakai
     *     ],
     *   ],
     *   'jadwal' => [
     *     '1' => ['nama'=>'helga faisa', 'dept'=>'Office', 'shift'=>1],
     *   ]
     * ]
     */
    public function parseInfoFile(string $path): array
    {
        $spreadsheet = IOFactory::load($path);

        return [
            'shifts' => $this->parseShifts($spreadsheet->getSheetByName('Shift')),
            'jadwal' => $this->parseJadwal($spreadsheet->getSheetByName('Jadwal')),
        ];
    }

    private function parseShifts($sheet): array
    {
        $shifts = [];
        // Kolom: A=No, B=JK1 Msuk, C=JK1 Kluar, D=JK2 Msuk, E=JK2 Kluar, F=Lb Msuk, G=Lb Kluar
        for ($row = 6; $row <= $sheet->getHighestRow(); $row++) {
            $no = $sheet->getCell("A{$row}")->getValue();
            if (!is_numeric($no)) continue;

            $s = [
                'jk1_msuk'  => $this->readTime($sheet->getCell("B{$row}")->getValue()),
                'jk1_kluar' => $this->readTime($sheet->getCell("C{$row}")->getValue()),
                'jk2_msuk'  => $this->readTime($sheet->getCell("D{$row}")->getValue()),
                'jk2_kluar' => $this->readTime($sheet->getCell("E{$row}")->getValue()),
                'lb_msuk'   => $this->readTime($sheet->getCell("F{$row}")->getValue()),
                'lb_kluar'  => $this->readTime($sheet->getCell("G{$row}")->getValue()),
            ];

            // Skip shift yang semua slot-nya kosong
            $adaIsi = array_filter($s);
            if (empty($adaIsi)) continue;

            $shifts[(int)$no] = $s;
        }
        return $shifts;
    }

    private function parseJadwal($sheet): array
    {
        $jadwal = [];
        for ($row = 3; $row <= $sheet->getHighestRow(); $row++) {
            $no   = $sheet->getCell("A{$row}")->getValue();
            $nama = $sheet->getCell("B{$row}")->getValue();
            if (!is_numeric($no) || empty($nama)) continue;

            $jadwal[(string)(int)$no] = [
                'nama'  => trim((string)$nama),
                'dept'  => trim((string)($sheet->getCell("C{$row}")->getValue() ?? '')),
                'shift' => (int)($sheet->getCell("D{$row}")->getValue() ?? 1),
            ];
        }
        return $jadwal;
    }

    // ─────────────────────────────────────────────────────────────
    // PARSE GLOG.TXT  ← PERUBAHAN UTAMA: simpan IOMd per scan
    // ─────────────────────────────────────────────────────────────

    /**
     * Parse GLog.txt → semua record scan, TERMASUK IOMd
     *
     * @return array [
     *   [
     *     'id_mesin'   => '1',
     *     'nama_mesin' => 'helga faisa',
     *     'tanggal'    => '2026-02-28',
     *     'jam'        => '04:05',
     *     'iomd'       => 2,          // ← BARU: 2=Masuk, 4=Pulang
     *     'dt_raw'     => '2026/02/28 04:05:00',
     *   ],
     * ]
     */
    public function parseGLog(string $path): array
    {
        $content = file_get_contents($path);
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $lines   = explode("\n", trim($content));
        $records = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $cols = explode("\t", $line);
            $cols = array_values(array_filter(array_map('trim', $cols), fn($v) => $v !== ''));

            // Minimal 7 kolom: No | Mchn | EnNo | Name | Mode | IOMd | DateTime
            if (count($cols) < 7) continue;
            if ($cols[0] === 'No') continue; // header

            $enno      = $cols[2] ?? '';
            $namaMesin = $cols[3] ?? '';
            $iomdRaw   = $cols[5] ?? '';   // kolom ke-6 (index 5)
            $dtRaw     = $cols[6] ?? '';

            if (!is_numeric(ltrim($enno, '0') ?: '0')) continue;
            if (empty($dtRaw)) continue;

            // IOMd: harus 2 atau 4
            $iomd = (int)$iomdRaw;
            if (!in_array($iomd, [self::IOMD_MASUK, self::IOMD_PULANG])) continue;

            // Parse DateTime
            $dtRaw  = preg_replace('/\s+/', ' ', trim($dtRaw));
            $parts  = explode(' ', $dtRaw);
            if (count($parts) < 2) continue;

            $tglStr = $parts[0]; // "2026/02/28"
            $jamStr = substr($parts[1], 0, 5); // "04:05"

            if (!preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $tglStr)) continue;
            if (!preg_match('/^\d{2}:\d{2}$/', $jamStr)) continue;

            $tanggal = str_replace('/', '-', $tglStr);
            $idMesin = (string)(int)ltrim($enno, '0') ?: '0';

            $records[] = [
                'id_mesin'   => $idMesin,
                'nama_mesin' => trim($namaMesin),
                'tanggal'    => $tanggal,
                'jam'        => $jamStr,
                'iomd'       => $iomd,   // ← BARU
                'dt_raw'     => $dtRaw,
            ];
        }

        return $records;
    }

    // ─────────────────────────────────────────────────────────────
    // GROUP BY DAY  ← PERUBAHAN: scans sekarang simpan iomd
    // ─────────────────────────────────────────────────────────────

    /**
     * Kelompokkan per (id_mesin + tanggal)
     * scans sekarang array of ['jam'=>'04:05','iomd'=>2]
     *
     * @return array [
     *   '1_2026-02-28' => [
     *     'id_mesin'   => '1',
     *     'nama_mesin' => 'helga faisa',
     *     'tanggal'    => '2026-02-28',
     *     'scans'      => [
     *       ['jam'=>'04:05','iomd'=>2],
     *       ['jam'=>'05:10','iomd'=>4],
     *     ],
     *   ],
     * ]
     */
    public function groupGLogByDay(array $records): array
    {
        $grouped = [];

        foreach ($records as $r) {
            $key = "{$r['id_mesin']}_{$r['tanggal']}";

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'id_mesin'   => $r['id_mesin'],
                    'nama_mesin' => $r['nama_mesin'],
                    'tanggal'    => $r['tanggal'],
                    'scans'      => [],
                ];
            }

            // Hindari duplikat jam+iomd yang persis sama
            $duplikat = array_filter(
                $grouped[$key]['scans'],
                fn($s) => $s['jam'] === $r['jam'] && $s['iomd'] === $r['iomd']
            );
            if (!empty($duplikat)) continue;

            $grouped[$key]['scans'][] = [
                'jam'  => $r['jam'],
                'iomd' => $r['iomd'],
            ];
        }

        // Sort scan berurutan berdasarkan jam
        foreach ($grouped as &$g) {
            usort($g['scans'], fn($a, $b) => strcmp($a['jam'], $b['jam']));
        }

        return $grouped;
    }

    // ─────────────────────────────────────────────────────────────
    // MATCH TO KEGIATAN  ← PERUBAHAN UTAMA
    // ─────────────────────────────────────────────────────────────

    /**
     * Cocokkan setiap scan ke kegiatan web.
     *
     * LOGIKA BARU (pakai IOMd):
     * ──────────────────────────────────────────────────────────
     * 1. Ambil shift santri dari infoData['jadwal']
     * 2. Buat "slot windows" dari shift tersebut:
     *    Setiap slot (jk1_msuk, jk1_kluar, dst) punya jam + IOMd
     * 3. Untuk setiap scan (jam + iomd):
     *    a. Cari slot yang IOMd-nya cocok DAN jam scan masuk window ±toleransi
     *    b. Dari slot yang cocok, cari kegiatan web hari ini yang waktunya paling dekat
     * 4. Hasilkan baris per kegiatan: Hadir / Terlambat / Alpa
     *
     * FALLBACK (tanpa IOMd, jika infoData kosong):
     *    Jika santri tidak ada di infoData (baru daftar, seperti firda),
     *    cocokkan hanya berdasarkan jam (abaikan IOMd) dengan toleransi lebih sempit.
     * ──────────────────────────────────────────────────────────
     *
     * @param array $glogGrouped  Output groupGLogByDay()
     * @param array $infoData     Output parseInfoFile() — ['shifts'=>[...],'jadwal'=>[...]]
     * @param array $kegiatans    Dari DB: [['kegiatan_id','nama','hari','waktu_mulai','waktu_selesai'],...]
     * @param int   $tolSebelum   Menit toleransi SEBELUM waktu_mulai kegiatan
     * @param int   $tolSesudah   Menit toleransi SESUDAH waktu_selesai kegiatan
     */
    public function matchToKegiatan(
        array $glogGrouped,
        array $infoData,
        array $kegiatans,
        int $tolSebelum = 15,
        int $tolSesudah = 10
    ): array {
        $hasil = [];

        foreach ($glogGrouped as $dayData) {
            $tanggal   = $dayData['tanggal'];
            $idMesin   = $dayData['id_mesin'];
            $scans     = $dayData['scans']; // [['jam'=>'04:05','iomd'=>2], ...]
            $hari      = $this->tanggalToHari($tanggal);

            // Kegiatan hari ini dari web
            $kegHariIni = array_values(
                array_filter($kegiatans, fn($k) => $k['hari'] === $hari)
            );

            // Info shift santri ini dari INFO.XLS
            $jadwalInfo   = $infoData['jadwal'][$idMesin] ?? null;
            $nomorShift   = $jadwalInfo ? ($jadwalInfo['shift'] ?? 1) : null;
            $shiftData    = ($nomorShift && isset($infoData['shifts'][$nomorShift]))
                            ? $infoData['shifts'][$nomorShift]
                            : null;

            // Build slot windows dari shift santri
            // slotWindows: [ ['slot'=>'jk1_msuk','jam'=>'04:00','iomd'=>2], ... ]
            $slotWindows = $shiftData
                ? $this->buildSlotWindows($shiftData)
                : [];

            // ── Matching ────────────────────────────────────────────
            $matchedKg  = []; // kegiatan_id → true (sudah dapat scan)
            $usedScans  = []; // index scan yang sudah dipakai
            $rowMap     = []; // kegiatan_id → result row

            foreach ($scans as $idx => $scan) {
                $scanJam  = $scan['jam'];
                $scanIomd = $scan['iomd'];
                $scanMnt  = $this->toMinutes($scanJam);

                $bestKg      = null;
                $bestSelisih = PHP_INT_MAX;
                $bestSlot    = null;

                if (!empty($slotWindows)) {
                    // ── MODE UTAMA: pakai IOMd dari shift ──────────────
                    // Langkah 1: cari slot yang IOMd-nya cocok DAN jam dalam window
                    foreach ($slotWindows as $sw) {
                        if ($sw['iomd'] !== $scanIomd) continue; // IOMd harus cocok
                        if ($sw['jam'] === null) continue;       // slot tidak diset

                        $slotMnt     = $this->toMinutes($sw['jam']);
                        $windowMulai = $slotMnt - $tolSebelum;
                        $windowAkhir = $slotMnt + $tolSesudah;

                        if ($scanMnt < $windowMulai || $scanMnt > $windowAkhir) continue;

                        // Slot cocok — sekarang cari kegiatan web yang paling dekat
                        foreach ($kegHariIni as $kg) {
                            if (isset($matchedKg[$kg['kegiatan_id']])) continue;

                            $kgMulaiMnt   = $this->toMinutes($kg['waktu_mulai']);
                            $kgSelesaiMnt = $this->toMinutes($kg['waktu_selesai'] ?: $kg['waktu_mulai']);
                            $kgWindowMul  = $kgMulaiMnt - $tolSebelum;
                            $kgWindowAkh  = $kgSelesaiMnt + $tolSesudah;

                            // Jam slot harus masuk window kegiatan
                            if ($slotMnt < $kgWindowMul || $slotMnt > $kgWindowAkh) continue;

                            $selisih = abs($slotMnt - $kgMulaiMnt);
                            if ($selisih < $bestSelisih) {
                                $bestSelisih = $selisih;
                                $bestKg      = $kg;
                                $bestSlot    = $sw;
                            }
                        }
                    }
                } else {
                    // ── FALLBACK: shifts kosong, matching hanya berdasarkan jam ──
                    // Pakai toleransi penuh (bukan dikurangi)
                    // Cari kegiatan yang paling dekat jamnya dengan scan
                    foreach ($kegHariIni as $kg) {
                        if (isset($matchedKg[$kg['kegiatan_id']])) continue;

                        $kgMulaiMnt   = $this->toMinutes($kg['waktu_mulai']);
                        $kgSelesaiMnt = $this->toMinutes($kg['waktu_selesai'] ?: $kg['waktu_mulai']);

                        // Window: tolSebelum menit sebelum mulai s/d tolSesudah menit setelah selesai
                        $kgWindowMul = $kgMulaiMnt - $tolSebelum;
                        $kgWindowAkh = $kgSelesaiMnt + $tolSesudah;

                        if ($scanMnt < $kgWindowMul || $scanMnt > $kgWindowAkh) continue;

                        $selisih = abs($scanMnt - $kgMulaiMnt);
                        if ($selisih < $bestSelisih) {
                            $bestSelisih = $selisih;
                            $bestKg      = $kg;
                        }
                    }
                }

                // ── Simpan hasil match ────────────────────────────
                if ($bestKg) {
                    $kgMulaiMnt = $this->toMinutes($bestKg['waktu_mulai']);

                    // Grace period: scan sampai 5 menit setelah mulai → masih Hadir
                    // Lebih dari 5 menit → Terlambat
                    $graceMnt   = 5;
                    $selisih    = $scanMnt - $kgMulaiMnt;
                    $status     = $selisih <= $graceMnt ? 'Hadir' : 'Terlambat';

                    $matchedKg[$bestKg['kegiatan_id']] = true;
                    $usedScans[] = $idx;

                    $rowMap[$bestKg['kegiatan_id']] = [
                        'kegiatan_id'   => $bestKg['kegiatan_id'],
                        'nama_kegiatan' => $bestKg['nama'],
                        'waktu_mulai'   => $bestKg['waktu_mulai'],
                        'jam_scan'      => $scanJam,
                        'iomd_scan'     => $scanIomd,
                        'label_iomd'    => $scanIomd === self::IOMD_MASUK ? 'Masuk' : 'Pulang',
                        'status'        => $status,
                        'selisih_menit' => max(0, $selisih - $graceMnt), // hanya menit yg melebihi grace
                        'matched'       => true,
                    ];
                }
            }

            // ── Isi Alpa untuk kegiatan tanpa scan ───────────────
            foreach ($kegHariIni as $kg) {
                if (!isset($rowMap[$kg['kegiatan_id']])) {
                    $rowMap[$kg['kegiatan_id']] = [
                        'kegiatan_id'   => $kg['kegiatan_id'],
                        'nama_kegiatan' => $kg['nama'],
                        'waktu_mulai'   => $kg['waktu_mulai'],
                        'jam_scan'      => null,
                        'iomd_scan'     => null,
                        'label_iomd'    => null,
                        'status'        => 'Alpa',
                        'selisih_menit' => null,
                        'matched'       => false,
                    ];
                }
            }

            // Scan yang tidak cocok ke kegiatan apapun
            $unmatchedScans = [];
            foreach ($scans as $idx => $scan) {
                if (!in_array($idx, $usedScans)) {
                    $unmatchedScans[] = $scan['jam'] . ' (' . ($scan['iomd'] === 2 ? 'Masuk' : 'Pulang') . ')';
                }
            }

            $rows = collect($rowMap)->sortBy('waktu_mulai')->values()->toArray();

            $hasil[] = [
                'id_mesin'        => $idMesin,
                'nama_mesin'      => $dayData['nama_mesin'],
                'tanggal'         => $tanggal,
                'hari'            => $hari,
                'all_scans'       => $scans,
                'unmatched_scans' => $unmatchedScans,
                'shift_dipakai'   => $nomorShift,  // ← BARU: untuk debug di preview
                'rows'            => $rows,
            ];
        }

        return $hasil;
    }

    // ─────────────────────────────────────────────────────────────
    // BUILD SLOT WINDOWS dari data shift
    // ─────────────────────────────────────────────────────────────

    /**
     * Dari satu shift, buat array slot windows yang bisa dicocokkan dengan scan.
     *
     * @param  array $shiftData ['jk1_msuk'=>'04:00','jk1_kluar'=>'05:00', ...]
     * @return array [
     *   ['slot'=>'jk1_msuk',  'jam'=>'04:00', 'iomd'=>2],
     *   ['slot'=>'jk1_kluar', 'jam'=>'05:00', 'iomd'=>4],
     *   ['slot'=>'jk2_msuk',  'jam'=>'11:45', 'iomd'=>2],
     *   ['slot'=>'jk2_kluar', 'jam'=>'12:20', 'iomd'=>4],
     *   ['slot'=>'lb_msuk',   'jam'=>'15:05', 'iomd'=>2],
     *   ['slot'=>'lb_kluar',  'jam'=>null,    'iomd'=>4],  // null = tidak dipakai
     * ]
     */
    private function buildSlotWindows(array $shiftData): array
    {
        $windows = [];
        foreach (self::SLOT_KEYS as $slotKey) {
            $windows[] = [
                'slot' => $slotKey,
                'jam'  => $shiftData[$slotKey] ?? null,
                'iomd' => self::SLOT_IOMD[$slotKey],
            ];
        }
        return $windows;
    }

    // ─────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────

    private function toMinutes(string $hhmm): int
    {
        if (!str_contains($hhmm, ':')) return 0;
        [$h, $m] = explode(':', $hhmm);
        return (int)$h * 60 + (int)$m;
    }

    public function tanggalToHari(string $tanggal): string
    {
        return [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Ahad',
        ][Carbon::parse($tanggal)->format('l')] ?? 'Senin';
    }

    /**
     * Baca nilai jam dari Excel — bisa berupa string "05:00" atau float (serial Excel)
     */
    private function readTime($val): ?string
    {
        if ($val === null || $val === '') return null;

        if (is_float($val) || (is_string($val) && is_numeric($val) && str_contains($val, '.'))) {
            $totalMin = round((float)$val * 24 * 60);
            return sprintf('%02d:%02d', intdiv($totalMin, 60), $totalMin % 60);
        }

        $str = preg_replace('/\s+/', '', trim((string)$val));
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $str, $m)) {
            return sprintf('%02d:%02d', (int)$m[1], (int)$m[2]);
        }
        return null;
    }
}