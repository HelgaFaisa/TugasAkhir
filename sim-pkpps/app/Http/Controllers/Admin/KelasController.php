<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KelompokKelas;
use App\Models\Santri;
use App\Models\SantriKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    // ==========================================
    // SECTION 1: CRUD KELAS
    // ==========================================

    public function index(Request $request)
    {
        $query = Kelas::with('kelompok');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('kode_kelas', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelompok')) {
            $query->where('id_kelompok', $request->kelompok);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $kelas = $query->orderBy('id_kelompok', 'asc')
                       ->orderBy('urutan', 'asc')
                       ->paginate(15)
                       ->appends(request()->query());

        $kelompokKelas = KelompokKelas::active()->ordered()->get();

        return view('admin.kelas.index', compact('kelas', 'kelompokKelas'));
    }

    public function create()
    {
        $nextKodeKelas = Cache::remember('next_kelas_kode', 60, function () {
            $lastKelas = Kelas::orderBy('id', 'desc')->first();
            $nextNum   = $lastKelas ? intval(substr($lastKelas->kode_kelas, 3)) + 1 : 1;
            return 'KLS' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        });

        $kelompokKelas = KelompokKelas::active()->ordered()->get();

        return view('admin.kelas.create', compact('nextKodeKelas', 'kelompokKelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas'  => 'required|string|max:100|unique:kelas,nama_kelas',
            'id_kelompok' => 'required|string|exists:kelompok_kelas,id_kelompok',
            'urutan'      => 'required|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        Kelas::create($validated);
        Cache::forget('next_kelas_kode');

        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Kelas $kela)
    {
        $kela->load(['kelompok', 'santriKelas.santri']);
        $santriCount = $kela->santriKelas()
            ->whereHas('santri', fn($q) => $q->where('status', 'Aktif'))
            ->count();

        return view('admin.kelas.show', compact('kela', 'santriCount'));
    }

    public function edit(Kelas $kela)
    {
        $kelompokKelas = KelompokKelas::active()->ordered()->get();
        return view('admin.kelas.edit', compact('kela', 'kelompokKelas'));
    }

    public function update(Request $request, Kelas $kela)
    {
        $validated = $request->validate([
            'nama_kelas'  => 'required|string|max:100|unique:kelas,nama_kelas,' . $kela->id,
            'id_kelompok' => 'required|string|exists:kelompok_kelas,id_kelompok',
            'urutan'      => 'required|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $kela->update($validated);

        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        $santriCount   = $kela->santriKelas()->count();
        $kegiatanCount = $kela->kegiatans()->count();

        if ($santriCount > 0) {
            return redirect()->route('admin.kelas.index')
                             ->with('error', "Kelas tidak dapat dihapus karena masih digunakan oleh {$santriCount} santri.");
        }

        if ($kegiatanCount > 0) {
            return redirect()->route('admin.kelas.index')
                             ->with('error', "Kelas tidak dapat dihapus karena masih memiliki {$kegiatanCount} kegiatan.");
        }

        $kela->delete();
        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Kelas berhasil dihapus.');
    }

    // ==========================================
    // SECTION 2: CRUD KELOMPOK KELAS
    // ==========================================

    public function kelompokIndex(Request $request)
    {
        $query = KelompokKelas::withCount('kelas');

        if ($request->filled('search')) {
            $query->where('nama_kelompok', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $kelompokKelas = $query->orderBy('urutan', 'asc')
                               ->paginate(15)
                               ->appends(request()->query());

        return view('admin.kelas.kelompok.index', compact('kelompokKelas'));
    }

    public function kelompokCreate()
    {
        $nextIdKelompok = Cache::remember('next_kelompok_id', 60, function () {
            $last    = KelompokKelas::orderBy('id', 'desc')->first();
            $nextNum = $last ? intval(substr($last->id_kelompok, 3)) + 1 : 1;
            return 'KEL' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        });

        return view('admin.kelas.kelompok.create', compact('nextIdKelompok'));
    }

    public function kelompokStore(Request $request)
    {
        $validated = $request->validate([
            'nama_kelompok' => 'required|string|max:100|unique:kelompok_kelas,nama_kelompok',
            'deskripsi'     => 'nullable|string|max:500',
            'urutan'        => 'required|integer|min:0',
            'is_active'     => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        KelompokKelas::create($validated);
        Cache::forget('next_kelompok_id');

        return redirect()->route('admin.kelas.kelompok.index')
                         ->with('success', 'Kelompok kelas berhasil ditambahkan.');
    }

    public function kelompokEdit($id)
    {
        $kelompok = KelompokKelas::findOrFail($id);
        $kelompok->loadCount('kelas');
        return view('admin.kelas.kelompok.edit', compact('kelompok'));
    }

    public function kelompokUpdate(Request $request, $id)
    {
        $kelompok  = KelompokKelas::findOrFail($id);
        $validated = $request->validate([
            'nama_kelompok' => 'required|string|max:100|unique:kelompok_kelas,nama_kelompok,' . $kelompok->id,
            'deskripsi'     => 'nullable|string|max:500',
            'urutan'        => 'required|integer|min:0',
            'is_active'     => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $kelompok->update($validated);

        return redirect()->route('admin.kelas.kelompok.index')
                         ->with('success', 'Kelompok kelas berhasil diperbarui.');
    }

    public function kelompokDestroy($id)
    {
        $kelompok   = KelompokKelas::findOrFail($id);
        $kelasCount = $kelompok->kelas()->count();

        if ($kelasCount > 0) {
            return redirect()->route('admin.kelas.kelompok.index')
                             ->with('error', "Kelompok tidak dapat dihapus karena masih memiliki {$kelasCount} kelas.");
        }

        $kelompok->delete();
        return redirect()->route('admin.kelas.kelompok.index')
                         ->with('success', 'Kelompok kelas berhasil dihapus.');
    }

    // ==========================================
    // SECTION 3: KENAIKAN KELAS MASSAL
    // ==========================================

    public function kenaikanIndex(Request $request)
    {
        $tahunAjaranAktif = $this->getActiveTahunAjaran();
        $tahunAjaranBaru  = $this->getNextAcademicYear($tahunAjaranAktif);
        $totalSantriAktif = Santri::where('status', 'Aktif')->count();

        $kelompokKelas = KelompokKelas::with([
            'kelas' => fn($q) => $q->where('is_active', true)->orderBy('urutan'),
        ])->active()->ordered()->get();

        $selectedKelompok = $request->get('kelompok');
        if (!$selectedKelompok && $kelompokKelas->isNotEmpty()) {
            $selectedKelompok = $kelompokKelas->first()->id_kelompok;
        }

        $kelasList = Kelas::with('kelompok')
            ->where('is_active', true)
            ->when($selectedKelompok, fn($q) => $q->where('id_kelompok', $selectedKelompok))
            ->withCount([
                'santriKelas as santri_aktif_count' => fn($q) => $q->whereHas('santri', fn($s) => $s->where('status', 'Aktif')),
            ])
            ->orderBy('urutan', 'asc')
            ->get();

        $allKelasList = Kelas::with('kelompok')
            ->where('is_active', true)
            ->orderBy('id_kelompok', 'asc')
            ->orderBy('urutan', 'asc')
            ->get();

        return view('admin.kelas.kenaikan.index', compact(
            'tahunAjaranAktif', 'tahunAjaranBaru', 'totalSantriAktif',
            'kelompokKelas', 'kelasList', 'allKelasList', 'selectedKelompok'
        ));
    }

    public function kenaikanPreview($id)
    {
        $kelas            = Kelas::with('kelompok')->findOrFail($id);
        $tahunAjaranAktif = $this->getActiveTahunAjaran();
        $tahunAjaranBaru  = $this->getNextAcademicYear($tahunAjaranAktif);

        $santriList = Santri::whereHas('kelasSantri', fn($q) => $q->where('id_kelas', $id))
            ->where('status', 'Aktif')
            ->orderBy('nama_lengkap')
            ->get();

        $kelasOptions = KelompokKelas::with([
            'kelas' => fn($q) => $q->where('is_active', true)->orderBy('urutan'),
        ])->active()->ordered()->get();

        return view('admin.kelas.kenaikan.preview', compact(
            'kelas', 'santriList', 'tahunAjaranAktif', 'tahunAjaranBaru', 'kelasOptions'
        ));
    }

    public function kenaikanProcess(Request $request)
    {
        $request->validate([
            'id_kelas_asal'   => 'required|exists:kelas,id',
            'id_kelas_tujuan' => 'required|exists:kelas,id|different:id_kelas_asal',
        ], [
            'id_kelas_tujuan.different' => 'Kelas tujuan tidak boleh sama dengan kelas asal.',
        ]);

        $kelasAsal   = Kelas::findOrFail($request->id_kelas_asal);
        $kelasTujuan = Kelas::findOrFail($request->id_kelas_tujuan);

        $santriIds = Santri::whereHas('kelasSantri', fn($q) => $q->where('id_kelas', $request->id_kelas_asal))
            ->where('status', 'Aktif')
            ->pluck('id_santri');

        if ($santriIds->isEmpty()) {
            return redirect()->route('admin.kelas.kenaikan.index')
                             ->with('error', 'Tidak ada santri aktif di kelas ' . $kelasAsal->nama_kelas . '.');
        }

        $processed = 0;

        DB::beginTransaction();
        try {
            foreach ($santriIds as $idSantri) {
                $record = SantriKelas::where('id_santri', $idSantri)
                    ->where('id_kelas', $kelasAsal->id)
                    ->orderBy('tahun_ajaran', 'desc')
                    ->first();

                if (!$record) continue;

                // Cek duplikasi: jika sudah ada di kelas tujuan + tahun_ajaran sama, hapus record lama
                $sudahAda = SantriKelas::where('id_santri', $idSantri)
                    ->where('id_kelas', $kelasTujuan->id)
                    ->where('tahun_ajaran', $record->tahun_ajaran)
                    ->exists();

                if ($sudahAda) {
                    $record->delete();
                } else {
                    $record->update(['id_kelas' => $kelasTujuan->id]);
                }

                $processed++;
            }

            DB::commit();

            return redirect()->route('admin.kelas.kenaikan.index')
                             ->with('success', "Berhasil menaikkan {$processed} santri dari {$kelasAsal->nama_kelas} ke {$kelasTujuan->nama_kelas}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.kelas.kenaikan.index')
                             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function kenaikanProcessSelected(Request $request)
    {
        $request->validate([
            'id_kelas_asal'   => 'required|exists:kelas,id',
            'id_kelas_tujuan' => 'required|exists:kelas,id|different:id_kelas_asal',
            'santri_ids'      => 'required|array|min:1',
            'santri_ids.*'    => 'exists:santris,id_santri',
        ], [
            'santri_ids.required'       => 'Pilih minimal 1 santri untuk dinaikkan kelasnya.',
            'santri_ids.min'            => 'Pilih minimal 1 santri untuk dinaikkan kelasnya.',
            'id_kelas_tujuan.different' => 'Kelas tujuan tidak boleh sama dengan kelas asal.',
        ]);

        $kelasAsal   = Kelas::findOrFail($request->id_kelas_asal);
        $kelasTujuan = Kelas::findOrFail($request->id_kelas_tujuan);

        $processed = 0;

        DB::beginTransaction();
        try {
            foreach ($request->santri_ids as $idSantri) {
                $record = SantriKelas::where('id_santri', $idSantri)
                    ->where('id_kelas', $kelasAsal->id)
                    ->orderBy('tahun_ajaran', 'desc')
                    ->first();

                if (!$record) continue;

                // Cek duplikasi: jika sudah ada di kelas tujuan + tahun_ajaran sama, hapus record lama
                $sudahAda = SantriKelas::where('id_santri', $idSantri)
                    ->where('id_kelas', $kelasTujuan->id)
                    ->where('tahun_ajaran', $record->tahun_ajaran)
                    ->exists();

                if ($sudahAda) {
                    $record->delete();
                } else {
                    $record->update(['id_kelas' => $kelasTujuan->id]);
                }

                $processed++;
            }

            DB::commit();

            return redirect()->route('admin.kelas.kenaikan.index')
                             ->with('success', "Berhasil menaikkan {$processed} santri dari {$kelasAsal->nama_kelas} ke {$kelasTujuan->nama_kelas}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.kelas.kenaikan.preview', $request->id_kelas_asal)
                             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Helper: tahun ajaran aktif berdasarkan data yang ada di santri_kelas.
     * Menggunakan tahun ajaran terbaru yang punya record, fallback ke kalkulasi.
     */
    private function getActiveTahunAjaran(): string
    {
        return SantriKelas::max('tahun_ajaran') ?? SantriKelas::getCurrentAcademicYear();
    }

    /**
     * Helper: hitung tahun ajaran berikutnya
     * Contoh: "2024/2025" -> "2025/2026"
     */
    private function getNextAcademicYear(string $currentYear): string
    {
        $parts = explode('/', $currentYear);
        return ((int) $parts[0] + 1) . '/' . ((int) $parts[1] + 1);
    }
}