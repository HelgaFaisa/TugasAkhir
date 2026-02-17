<?php
/**
 * ============================================================================
 * LOKASI FILE: app/Http/Controllers/Admin/KelasController.php
 * ============================================================================
 * 
 * INSTRUKSI:
 * 1. Backup file KelasController.php yang lama
 * 2. Replace dengan file ini
 * 3. File ini sudah include semua fitur:
 *    - CRUD Kelas
 *    - CRUD Kelompok Kelas
 *    - Kenaikan Kelas Massal
 * ============================================================================
 */

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
    
    /**
     * Display a listing of kelas.
     */
    public function index(Request $request)
    {
        $query = Kelas::with('kelompok');

        // Search by nama kelas atau kode kelas
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('kode_kelas', 'like', "%{$search}%");
            });
        }

        // Filter by kelompok kelas
        if ($request->filled('kelompok')) {
            $query->where('id_kelompok', $request->kelompok);
        }

        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Order by kelompok then urutan
        $kelas = $query->orderBy('id_kelompok', 'asc')
                      ->orderBy('urutan', 'asc')
                      ->paginate(15)
                      ->appends(request()->query());

        // Get kelompok kelas for filter dropdown
        $kelompokKelas = KelompokKelas::active()->ordered()->get();

        return view('admin.kelas.index', compact('kelas', 'kelompokKelas'));
    }

    /**
     * Show the form for creating a new kelas.
     */
    public function create()
    {
        // Get next kode_kelas
        $nextKodeKelas = Cache::remember('next_kelas_kode', 60, function () {
            $lastKelas = Kelas::orderBy('id', 'desc')->first();
            $nextNum = $lastKelas ? intval(substr($lastKelas->kode_kelas, 3)) + 1 : 1;
            return 'KLS' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        });

        // Get kelompok kelas for dropdown
        $kelompokKelas = KelompokKelas::active()->ordered()->get();

        return view('admin.kelas.create', compact('nextKodeKelas', 'kelompokKelas'));
    }

    /**
     * Store a newly created kelas in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:100|unique:kelas,nama_kelas',
            'id_kelompok' => 'required|string|exists:kelompok_kelas,id_kelompok',
            'urutan' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas sudah digunakan.',
            'id_kelompok.required' => 'Kelompok kelas wajib dipilih.',
            'id_kelompok.exists' => 'Kelompok kelas tidak valid.',
            'urutan.required' => 'Urutan wajib diisi.',
            'urutan.integer' => 'Urutan harus berupa angka.',
            'urutan.min' => 'Urutan minimal 0.',
        ]);

        // Set is_active default to true if not provided
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Create kelas (kode_kelas will be auto-generated in model)
        Kelas::create($validated);

        // Clear cache
        Cache::forget('next_kelas_kode');

        return redirect()->route('admin.kelas.index')
                        ->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified kelas.
     */
    public function show(Kelas $kela)
    {
        // Load relationships
        $kela->load(['kelompok', 'santriKelas.santri']);
        
        // Get santri count in this kelas for current academic year
        $tahunAjaranAktif = SantriKelas::getCurrentAcademicYear();
        $santriCount = $kela->santriKelas()
            ->where('tahun_ajaran', $tahunAjaranAktif)
            ->whereHas('santri', function($q) {
                $q->where('status', 'Aktif');
            })
            ->count();
        
        return view('admin.kelas.show', compact('kela', 'santriCount', 'tahunAjaranAktif'));
    }

    /**
     * Show the form for editing the specified kelas.
     */
    public function edit(Kelas $kela)
    {
        // Get kelompok kelas for dropdown
        $kelompokKelas = KelompokKelas::active()->ordered()->get();

        return view('admin.kelas.edit', compact('kela', 'kelompokKelas'));
    }

    /**
     * Update the specified kelas in storage.
     */
    public function update(Request $request, Kelas $kela)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:100|unique:kelas,nama_kelas,' . $kela->id,
            'id_kelompok' => 'required|string|exists:kelompok_kelas,id_kelompok',
            'urutan' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas sudah digunakan.',
            'id_kelompok.required' => 'Kelompok kelas wajib dipilih.',
            'id_kelompok.exists' => 'Kelompok kelas tidak valid.',
            'urutan.required' => 'Urutan wajib diisi.',
            'urutan.integer' => 'Urutan harus berupa angka.',
            'urutan.min' => 'Urutan minimal 0.',
        ]);

        // Set is_active
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Update kelas
        $kela->update($validated);

        return redirect()->route('admin.kelas.index')
                        ->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified kelas from storage.
     */
    public function destroy(Kelas $kela)
    {
        // Check if kelas is still being used
        $santriCount = $kela->santriKelas()->count();
        $kegiatanCount = $kela->kegiatans()->count();

        if ($santriCount > 0) {
            return redirect()->route('admin.kelas.index')
                           ->with('error', "Kelas tidak dapat dihapus karena masih digunakan oleh {$santriCount} santri.");
        }

        if ($kegiatanCount > 0) {
            return redirect()->route('admin.kelas.index')
                           ->with('error', "Kelas tidak dapat dihapus karena masih memiliki {$kegiatanCount} kegiatan.");
        }

        // Delete kelas
        $kela->delete();

        return redirect()->route('admin.kelas.index')
                        ->with('success', 'Kelas berhasil dihapus.');
    }

    // ==========================================
    // SECTION 2: CRUD KELOMPOK KELAS
    // ==========================================
    
    /**
     * Display a listing of kelompok kelas.
     */
    public function kelompokIndex(Request $request)
    {
        $query = KelompokKelas::withCount('kelas');

        // Search by nama kelompok
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_kelompok', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Order by urutan
        $kelompokKelas = $query->orderBy('urutan', 'asc')
                              ->paginate(15)
                              ->appends(request()->query());

        return view('admin.kelas.kelompok.index', compact('kelompokKelas'));
    }

    /**
     * Show the form for creating a new kelompok kelas.
     */
    public function kelompokCreate()
    {
        // Get next id_kelompok
        $nextIdKelompok = Cache::remember('next_kelompok_id', 60, function () {
            $lastKelompok = KelompokKelas::orderBy('id', 'desc')->first();
            $nextNum = $lastKelompok ? intval(substr($lastKelompok->id_kelompok, 3)) + 1 : 1;
            return 'KEL' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        });

        return view('admin.kelas.kelompok.create', compact('nextIdKelompok'));
    }

    /**
     * Store a newly created kelompok kelas in storage.
     */
    public function kelompokStore(Request $request)
    {
        $validated = $request->validate([
            'nama_kelompok' => 'required|string|max:100|unique:kelompok_kelas,nama_kelompok',
            'deskripsi' => 'nullable|string|max:500',
            'urutan' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'nama_kelompok.required' => 'Nama kelompok wajib diisi.',
            'nama_kelompok.unique' => 'Nama kelompok sudah digunakan.',
            'urutan.required' => 'Urutan wajib diisi.',
            'urutan.integer' => 'Urutan harus berupa angka.',
            'urutan.min' => 'Urutan minimal 0.',
            'deskripsi.max' => 'Deskripsi maksimal 500 karakter.',
        ]);

        // Set is_active default to true if not provided
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Create kelompok (id_kelompok will be auto-generated in model)
        KelompokKelas::create($validated);

        // Clear cache
        Cache::forget('next_kelompok_id');

        return redirect()->route('admin.kelas.kelompok.index')
                        ->with('success', 'Kelompok kelas berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified kelompok kelas.
     */
    public function kelompokEdit($id)
    {
        $kelompok = KelompokKelas::findOrFail($id);
        $kelompok->loadCount('kelas');
        
        return view('admin.kelas.kelompok.edit', compact('kelompok'));
    }

    /**
     * Update the specified kelompok kelas in storage.
     */
    public function kelompokUpdate(Request $request, $id)
    {
        $kelompok = KelompokKelas::findOrFail($id);
        
        $validated = $request->validate([
            'nama_kelompok' => 'required|string|max:100|unique:kelompok_kelas,nama_kelompok,' . $kelompok->id,
            'deskripsi' => 'nullable|string|max:500',
            'urutan' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'nama_kelompok.required' => 'Nama kelompok wajib diisi.',
            'nama_kelompok.unique' => 'Nama kelompok sudah digunakan.',
            'urutan.required' => 'Urutan wajib diisi.',
            'urutan.integer' => 'Urutan harus berupa angka.',
            'urutan.min' => 'Urutan minimal 0.',
            'deskripsi.max' => 'Deskripsi maksimal 500 karakter.',
        ]);

        // Set is_active
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Update kelompok
        $kelompok->update($validated);

        return redirect()->route('admin.kelas.kelompok.index')
                        ->with('success', 'Kelompok kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified kelompok kelas from storage.
     */
    public function kelompokDestroy($id)
    {
        $kelompok = KelompokKelas::findOrFail($id);
        
        // Check if kelompok still has kelas
        $kelasCount = $kelompok->kelas()->count();

        if ($kelasCount > 0) {
            return redirect()->route('admin.kelas.kelompok.index')
                           ->with('error', "Kelompok tidak dapat dihapus karena masih memiliki {$kelasCount} kelas.");
        }

        // Delete kelompok
        $kelompok->delete();

        return redirect()->route('admin.kelas.kelompok.index')
                        ->with('success', 'Kelompok kelas berhasil dihapus.');
    }

    // ==========================================
    // SECTION 3: KENAIKAN KELAS MASSAL
    // ==========================================
    
    /**
     * Display kenaikan kelas index page
     */
    public function kenaikanIndex(Request $request)
    {
        $tahunAjaranAktif = SantriKelas::getCurrentAcademicYear();
        $tahunAjaranBaru = $this->getNextAcademicYear($tahunAjaranAktif);
        
        // Get total santri aktif
        $totalSantriAktif = Santri::where('status', 'Aktif')->count();
        
        // Get all kelompok kelas for dropdown
        $kelompokKelas = KelompokKelas::with(['kelas' => function($q) {
            $q->where('is_active', true)->orderBy('urutan');
        }])
        ->active()
        ->ordered()
        ->get();
        
        // Determine selected kelompok (default: first kelompok)
        $selectedKelompok = $request->get('kelompok');
        if (!$selectedKelompok && $kelompokKelas->isNotEmpty()) {
            $selectedKelompok = $kelompokKelas->first()->id_kelompok;
        }
        
        // Get kelas list for selected kelompok only
        $kelasList = Kelas::with('kelompok')
            ->where('is_active', true)
            ->when($selectedKelompok, function($q) use ($selectedKelompok) {
                $q->where('id_kelompok', $selectedKelompok);
            })
            ->withCount(['santriKelas as santri_aktif_count' => function($q) use ($tahunAjaranAktif) {
                $q->where('tahun_ajaran', $tahunAjaranAktif)
                  ->whereHas('santri', function($q2) {
                      $q2->where('status', 'Aktif');
                  });
            }])
            ->orderBy('urutan', 'asc')
            ->get();
        
        // Get all kelas for dropdown selection (bisa naik ke kelas manapun)
        $allKelasList = Kelas::with('kelompok')
            ->where('is_active', true)
            ->orderBy('id_kelompok', 'asc')
            ->orderBy('urutan', 'asc')
            ->get();
        
        return view('admin.kelas.kenaikan.index', compact(
            'tahunAjaranAktif',
            'tahunAjaranBaru',
            'totalSantriAktif',
            'kelompokKelas',
            'kelasList',
            'allKelasList',
            'selectedKelompok'
        ));
    }
    
    /**
     * Preview santri in a class before kenaikan
     */
    public function kenaikanPreview($id)
    {
        $kelas = Kelas::with('kelompok')->findOrFail($id);
        $tahunAjaranAktif = SantriKelas::getCurrentAcademicYear();
        $tahunAjaranBaru = $this->getNextAcademicYear($tahunAjaranAktif);
        
        // Get santri in this class (tahun ajaran aktif, status aktif)
        $santriList = Santri::whereHas('kelasSantri', function($q) use ($id, $tahunAjaranAktif) {
            $q->where('id_kelas', $id)
              ->where('tahun_ajaran', $tahunAjaranAktif);
        })
        ->where('status', 'Aktif')
        ->orderBy('nama_lengkap')
        ->get();
        
        // Get all kelompok with kelas for dropdown
        $kelasOptions = KelompokKelas::with(['kelas' => function($q) {
            $q->where('is_active', true)->orderBy('urutan');
        }])
        ->active()
        ->ordered()
        ->get();
        
        return view('admin.kelas.kenaikan.preview', compact(
            'kelas',
            'santriList',
            'tahunAjaranAktif',
            'tahunAjaranBaru',
            'kelasOptions'
        ));
    }
    
    /**
     * Process kenaikan kelas for all santri in a class
     */
    public function kenaikanProcess(Request $request)
    {
        $request->validate([
            'id_kelas_asal' => 'required|exists:kelas,id',
            'id_kelas_tujuan' => 'required|exists:kelas,id',
        ]);
        
        $kelasAsal = Kelas::findOrFail($request->id_kelas_asal);
        $kelasTujuan = Kelas::findOrFail($request->id_kelas_tujuan);
        $tahunAjaranAktif = SantriKelas::getCurrentAcademicYear();
        
        // Get all santri aktif in kelas asal
        $santriIds = Santri::whereHas('kelasSantri', function($q) use ($request, $tahunAjaranAktif) {
            $q->where('id_kelas', $request->id_kelas_asal)
              ->where('tahun_ajaran', $tahunAjaranAktif);
        })
        ->where('status', 'Aktif')
        ->pluck('id_santri');
        
        if ($santriIds->isEmpty()) {
            return redirect()->route('admin.kelas.kenaikan.index')
                           ->with('error', 'Tidak ada santri aktif di kelas ' . $kelasAsal->nama_kelas);
        }
        
        $processed = 0;
        
        DB::beginTransaction();
        try {
            foreach ($santriIds as $idSantri) {
                // Cari record santri_kelas yg ada di kelas asal
                $record = SantriKelas::where('id_santri', $idSantri)
                    ->where('id_kelas', $kelasAsal->id)
                    ->where('tahun_ajaran', $tahunAjaranAktif)
                    ->first();
                
                if ($record) {
                    // Update record: ganti kelas saja, tahun_ajaran & is_primary TETAP
                    $record->update([
                        'id_kelas' => $kelasTujuan->id,
                    ]);
                    $processed++;
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.kelas.kenaikan.index')
                           ->with('success', "Berhasil menaikkan {$processed} santri dari kelas {$kelasAsal->nama_kelas} ke {$kelasTujuan->nama_kelas}.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.kelas.kenaikan.index')
                           ->with('error', 'Terjadi kesalahan saat memproses kenaikan kelas: ' . $e->getMessage());
        }
    }
    
    /**
     * Process kenaikan kelas for selected santri only
     */
    public function kenaikanProcessSelected(Request $request)
    {
        $request->validate([
            'id_kelas_asal' => 'required|exists:kelas,id',
            'id_kelas_tujuan' => 'required|exists:kelas,id',
            'santri_ids' => 'required|array|min:1',
            'santri_ids.*' => 'exists:santris,id_santri',
        ], [
            'santri_ids.required' => 'Pilih minimal 1 santri untuk dinaikkan kelasnya.',
            'santri_ids.min' => 'Pilih minimal 1 santri untuk dinaikkan kelasnya.',
        ]);
        
        $kelasAsal = Kelas::findOrFail($request->id_kelas_asal);
        $kelasTujuan = Kelas::findOrFail($request->id_kelas_tujuan);
        $tahunAjaranAktif = SantriKelas::getCurrentAcademicYear();
        
        $processed = 0;
        
        DB::beginTransaction();
        try {
            foreach ($request->santri_ids as $idSantri) {
                // Cari record santri_kelas yg ada di kelas asal
                $record = SantriKelas::where('id_santri', $idSantri)
                    ->where('id_kelas', $kelasAsal->id)
                    ->where('tahun_ajaran', $tahunAjaranAktif)
                    ->first();
                
                if ($record) {
                    // Update record: ganti kelas saja, tahun_ajaran & is_primary TETAP
                    $record->update([
                        'id_kelas' => $kelasTujuan->id,
                    ]);
                    $processed++;
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.kelas.kenaikan.index')
                           ->with('success', "Berhasil menaikkan {$processed} santri dari kelas {$kelasAsal->nama_kelas} ke {$kelasTujuan->nama_kelas}.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.kelas.kenaikan.preview', $request->id_kelas_asal)
                           ->with('error', 'Terjadi kesalahan saat memproses kenaikan kelas: ' . $e->getMessage());
        }
    }
    
    /**
     * Helper: Get next academic year
     * Input: 2024/2025
     * Output: 2025/2026
     */
    private function getNextAcademicYear($currentYear)
    {
        $parts = explode('/', $currentYear);
        $startYear = (int) $parts[0] + 1;
        $endYear = (int) $parts[1] + 1;
        
        return $startYear . '/' . $endYear;
    }
}