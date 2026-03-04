<?php
// app/Http/Controllers/Admin/MesinMappingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MesinSantriMapping;
use App\Models\Santri;
use App\Services\EpposGLogParser;
use Illuminate\Http\Request;

class MesinMappingController extends Controller
{
    // ──────────────────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────────────────
    public function index()
    {
        $mappings = MesinSantriMapping::with('santri')
            ->orderByRaw('CAST(id_mesin AS UNSIGNED)')
            ->get();

        $santris = Santri::where('status', 'Aktif')
            ->orderBy('nama_lengkap')
            ->get(['id_santri', 'nama_lengkap']);

        return view('admin.mesin.mapping-santri.index', compact('mappings', 'santris'));
    }

    // ──────────────────────────────────────────────────────────
    // STORE (tambah manual)
    // ──────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'id_mesin'   => 'required|string|unique:mesin_santri_mappings,id_mesin',
            'id_santri'  => 'nullable|exists:santris,id_santri',
            'nama_mesin' => 'nullable|string|max:100',
            'catatan'    => 'nullable|string|max:255',
        ]);

        MesinSantriMapping::create($request->only(
            'id_mesin', 'id_santri', 'nama_mesin', 'catatan'
        ));

        return back()->with('success', "Mapping ID Mesin {$request->id_mesin} berhasil ditambahkan.");
    }

    // ──────────────────────────────────────────────────────────
    // UPDATE (ganti santri lewat dropdown)
    // ──────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $mapping = MesinSantriMapping::findOrFail($id);

        $request->validate([
            'id_santri' => 'nullable|exists:santris,id_santri',
        ]);

        $mapping->update(['id_santri' => $request->id_santri ?: null]);

        return back()->with('success', 'Mapping berhasil diperbarui.');
    }

    // ──────────────────────────────────────────────────────────
    // DESTROY
    // ──────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $mapping = MesinSantriMapping::findOrFail($id);
        $idMesin = $mapping->id_mesin;
        $mapping->delete();

        return back()->with('success', "Mapping ID Mesin {$idMesin} berhasil dihapus.");
    }

    // ──────────────────────────────────────────────────────────
    // IMPORT FROM INFO.XLS
    // ──────────────────────────────────────────────────────────
    public function importFromInfo(Request $request)
    {
        $request->validate([
            'file_info' => 'required|file|mimes:xls,xlsx|max:10240',
        ]);

        $parser   = app(EpposGLogParser::class);
        $infoData = $parser->parseInfoFile($request->file('file_info')->getPathname());
        $jadwal   = $infoData['jadwal'];

        $added   = 0;
        $skipped = 0;
        $matched = 0;

        // Ambil semua santri aktif sekali saja (efisien, tidak query per-santri)
        $semuaSantri = Santri::where('status', 'Aktif')
            ->get(['id_santri', 'nama_lengkap']);

        foreach ($jadwal as $idMesin => $data) {
            // Skip jika mapping sudah ada
            if (MesinSantriMapping::where('id_mesin', $idMesin)->exists()) {
                $skipped++;
                continue;
            }

            // Coba cocokkan nama dengan berbagai strategi
            $santri = $this->cariSantriByNama($data['nama'], $semuaSantri);

            MesinSantriMapping::create([
                'id_mesin'   => $idMesin,
                'id_santri'  => $santri?->id_santri,
                'nama_mesin' => $data['nama'],
                'dept_mesin' => $data['dept'] ?? null,
            ]);

            if ($santri) $matched++;
            $added++;
        }

        $msg = "{$added} mapping ditambahkan ({$matched} otomatis cocok nama), {$skipped} sudah ada.";
        if ($added > $matched) {
            $belum = $added - $matched;
            $msg  .= " {$belum} perlu dipetakan manual (nama tidak cocok).";
        }

        return back()->with('success', $msg);
    }

    // ──────────────────────────────────────────────────────────
    // HELPER: Cari Santri Berdasarkan Nama (Fuzzy Matching)
    //
    // Strategi (urutan prioritas):
    // 1. Exact match (nama lengkap sama persis, case-insensitive)
    // 2. Nama mesin ada di dalam nama lengkap santri
    //    → "helga faisa" ditemukan di "helga faisa fahar"
    // 3. Nama lengkap santri ada di dalam nama mesin
    //    → "helga" ditemukan di "helga faisa fahar"
    // 4. Semua kata dari nama mesin ada di nama santri
    //    → nama mesin "helga faisa" → cari santri yang punya "helga" DAN "faisa"
    // 5. Minimal 1 kata dari nama mesin cocok, pilih santri
    //    dengan skor kata paling banyak cocok
    // ──────────────────────────────────────────────────────────
    private function cariSantriByNama(string $namaMesin, $semuaSantri): ?Santri
    {
        $namaMesinBersih = strtolower(trim($namaMesin));

        if (empty($namaMesinBersih)) return null;

        // ── Strategi 1: Exact match ───────────────────────────
        foreach ($semuaSantri as $santri) {
            $namaDb = strtolower(trim($santri->nama_lengkap));
            if ($namaDb === $namaMesinBersih) {
                return $santri;
            }
        }

        // ── Strategi 2: nama mesin ada di nama santri ─────────
        // Contoh: nama mesin "helga faisa" → santri "helga faisa fahar" ✓
        foreach ($semuaSantri as $santri) {
            $namaDb = strtolower(trim($santri->nama_lengkap));
            if (str_contains($namaDb, $namaMesinBersih)) {
                return $santri;
            }
        }

        // ── Strategi 3: nama santri ada di nama mesin ─────────
        // Contoh: nama mesin "helga faisa fahar" → santri "helga faisa" ✓
        foreach ($semuaSantri as $santri) {
            $namaDb = strtolower(trim($santri->nama_lengkap));
            if (str_contains($namaMesinBersih, $namaDb)) {
                return $santri;
            }
        }

        // ── Strategi 4 & 5: Skor berdasarkan kata ────────────
        // Pecah nama mesin jadi kata-kata
        // Contoh: "helga faisa" → ['helga', 'faisa']
        $kataMesin = array_filter(explode(' ', $namaMesinBersih));

        if (empty($kataMesin)) return null;

        $kandidatTerbaik = null;
        $skorTerbaik     = 0;

        foreach ($semuaSantri as $santri) {
            $namaDb   = strtolower(trim($santri->nama_lengkap));
            $kataDb   = array_filter(explode(' ', $namaDb));

            $skorCocok = 0;

            foreach ($kataMesin as $kata) {
                // Minimal 3 karakter agar tidak false positive (mis. "al", "bin")
                if (strlen($kata) < 3) continue;

                foreach ($kataDb as $kataDbItem) {
                    if (
                        $kataDbItem === $kata ||                     // kata persis sama
                        str_contains($kataDbItem, $kata) ||          // kata mesin ada di kata db
                        str_contains($kata, $kataDbItem)             // kata db ada di kata mesin
                    ) {
                        $skorCocok++;
                        break; // sudah cocok, lanjut kata berikutnya
                    }
                }
            }

            // Hitung persentase kata yang cocok
            $persenCocok = $skorCocok / count($kataMesin);

            // Update kandidat jika skor lebih tinggi
            if ($persenCocok > $skorTerbaik) {
                $skorTerbaik     = $persenCocok;
                $kandidatTerbaik = $santri;
            }
        }

        // Ambil kandidat hanya jika minimal 50% kata cocok
        // Contoh: nama mesin "helga faisa" (2 kata) → butuh minimal 1 kata cocok
        // Contoh: nama mesin "helga faisa fahar" (3 kata) → butuh minimal 2 kata cocok
        if ($skorTerbaik >= 0.5) {
            return $kandidatTerbaik;
        }

        // Tidak ada yang cocok
        return null;
    }
}