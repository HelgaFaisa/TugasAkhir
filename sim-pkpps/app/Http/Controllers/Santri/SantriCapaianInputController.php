<?php
// app/Http/Controllers/Santri/SantriCapaianInputController.php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Capaian;
use App\Models\Materi;
use App\Models\Semester;
use App\Models\Santri;
use App\Services\CapaianAccessService;
use Illuminate\Http\Request;

class SantriCapaianInputController extends Controller
{
    private function getSantri(): Santri
    {
        $idSantri = auth('santri')->user()->id_santri;
        return Santri::where('id_santri', $idSantri)
            ->with(['kelasSantri.kelas'])
            ->firstOrFail();
    }

    /**
     * Form input capaian untuk santri.
     * GET /santri/capaian/input
     */
    public function create(Request $request)
    {
        // Cek apakah akses sedang dibuka
        if (!CapaianAccessService::isOpen()) {
            return redirect()->route('santri.capaian.index')
                ->with('error', 'Saat ini belum ada jadwal input capaian. Silakan tunggu informasi dari admin.');
        }

        $santri        = $this->getSantri();
        $accessConfig  = CapaianAccessService::getConfig();
        $sisaWaktu     = CapaianAccessService::getSisaWaktu();

        // Ambil semester yang berlaku
        $idSemesterConfig = $accessConfig['id_semester'] ?? null;
        if ($idSemesterConfig) {
            $semesterAktif = Semester::where('id_semester', $idSemesterConfig)->first();
        } else {
            $semesterAktif = Semester::aktif()->first();
        }

        // Materi sesuai kelas santri
        $kelasNames    = $santri->kelasSantri->map(fn($sk) => $sk->kelas?->nama_kelas)->filter()->unique()->toArray();
        $materiOptions = Materi::whereIn('kelas', $kelasNames ?: [''])
            ->orderBy('kategori')->orderBy('nama_kitab')->get();

        // Capaian yang sudah ada di semester ini
        $existingCapaians = [];
        if ($semesterAktif) {
            $existingCapaians = Capaian::where('id_santri', $santri->id_santri)
                ->where('id_semester', $semesterAktif->id_semester)
                ->pluck('persentase', 'id_materi')
                ->toArray();
        }

        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();

        return view('santri.capaian.input', compact(
            'santri', 'semesterAktif', 'semesters', 'materiOptions',
            'existingCapaians', 'accessConfig', 'sisaWaktu'
        ));
    }

    /**
     * Simpan/update capaian oleh santri.
     * POST /santri/capaian/input
     */
    public function store(Request $request)
    {
        // Double-check akses masih terbuka
        if (!CapaianAccessService::isOpen()) {
            return redirect()->route('santri.capaian.index')
                ->with('error', 'Waktu input capaian telah berakhir.');
        }

        $santri = $this->getSantri();

        $validated = $request->validate([
            'id_materi'      => 'required|exists:materi,id_materi',
            'id_semester'    => 'required|exists:semester,id_semester',
            'halaman_selesai'=> 'required|string',
            'catatan'        => 'nullable|string|max:500',
            'tanggal_input'  => 'required|date',
        ]);

        // Pastikan semester yang dikirim sesuai dengan yang diizinkan
        $accessConfig = CapaianAccessService::getConfig();
        if (!empty($accessConfig['id_semester']) && $accessConfig['id_semester'] !== $validated['id_semester']) {
            return back()->with('error', 'Semester tidak sesuai dengan jadwal input yang dibuka admin.');
        }

        // Validasi materi sesuai kelas santri
        $kelasNames = $santri->kelasSantri->map(fn($sk) => $sk->kelas?->nama_kelas)->filter()->unique()->toArray();
        $materi     = Materi::where('id_materi', $validated['id_materi'])
            ->whereIn('kelas', $kelasNames ?: [''])->first();

        if (!$materi) {
            return back()->with('error', 'Materi tidak sesuai dengan kelas Anda.');
        }

        // Upsert capaian (create or update)
        $existing = Capaian::where('id_santri', $santri->id_santri)
            ->where('id_materi', $validated['id_materi'])
            ->where('id_semester', $validated['id_semester'])
            ->first();

        if ($existing) {
            $existing->update([
                'halaman_selesai' => $validated['halaman_selesai'],
                'catatan'         => $validated['catatan'],
                'tanggal_input'   => $validated['tanggal_input'],
            ]);
            $msg = "Capaian {$materi->nama_kitab} berhasil diperbarui.";
        } else {
            Capaian::create([
                'id_santri'      => $santri->id_santri,
                'id_materi'      => $validated['id_materi'],
                'id_semester'    => $validated['id_semester'],
                'halaman_selesai'=> $validated['halaman_selesai'],
                'catatan'        => $validated['catatan'],
                'tanggal_input'  => $validated['tanggal_input'],
            ]);
            $msg = "Capaian {$materi->nama_kitab} berhasil disimpan.";
        }

        return redirect()->route('santri.capaian.input.create')
            ->with('success', $msg);
    }

    /**
     * AJAX: Ambil detail materi + existing capaian santri ini.
     * POST /santri/capaian/input/ajax/detail-materi
     */
    public function ajaxDetailMateri(Request $request)
    {
        $santri = $this->getSantri();

        $materi = Materi::where('id_materi', $request->id_materi)->first();
        if (!$materi) return response()->json(['error' => 'Materi tidak ditemukan'], 404);

        $existing = null;
        if ($request->filled('id_semester')) {
            $existing = Capaian::where('id_santri', $santri->id_santri)
                ->where('id_materi', $request->id_materi)
                ->where('id_semester', $request->id_semester)
                ->first();
        }

        return response()->json([
            'materi'           => $materi,
            'existing_capaian' => $existing,
        ]);
    }

    /**
     * AJAX: Hitung persentase preview.
     */
    public function ajaxHitungPersentase(Request $request)
    {
        if (empty($request->halaman_selesai) || empty($request->id_materi)) {
            return response()->json(['persentase' => 0, 'jumlah' => 0]);
        }
        try {
            $persentase = Capaian::calculatePersentase($request->halaman_selesai, $request->id_materi);
            $pages      = Capaian::parseHalamanSelesai($request->halaman_selesai);
            return response()->json([
                'persentase' => number_format($persentase, 2),
                'jumlah'     => count($pages),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}