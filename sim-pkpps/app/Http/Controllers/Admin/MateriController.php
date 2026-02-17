<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use App\Models\Santri;
use App\Models\Capaian;
use App\Models\Semester;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MateriController extends Controller
{
    /**
     * Display a listing of materi with filters
     */
    public function index(Request $request)
    {
        $query = Materi::query();

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->kategori($request->kategori);
        }

        // Filter berdasarkan kelas
        if ($request->filled('kelas')) {
            $query->kelas($request->kelas);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Select kolom yang diperlukan untuk optimasi
        $materis = $query->select(
                'id',
                'id_materi',
                'kategori',
                'kelas',
                'nama_kitab',
                'halaman_mulai',
                'halaman_akhir',
                'total_halaman',
                'created_at'
            )
            ->orderBy('kategori')
            ->orderBy('kelas')
            ->orderBy('nama_kitab')
            ->paginate(20)
            ->appends(request()->query());

        // Dynamic kelas list dari tabel kelas
        $kelasList = Kelas::active()->ordered()->get();

        return view('admin.materi.index', compact('materis', 'kelasList'));
    }

    /**
     * Show the form for creating a new materi
     */
    public function create()
    {
        // Generate next ID untuk preview
        $nextIdMateri = Cache::remember('next_materi_id', 60, function () {
            $lastMateri = Materi::select('id_materi')
                ->orderBy('id', 'desc')
                ->first();
            $nextNum = $lastMateri ? intval(substr($lastMateri->id_materi, 1)) + 1 : 1;
            return 'M' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        });

        // Dynamic kelas list dari tabel kelas
        $kelasList = Kelas::active()->ordered()->get();

        return view('admin.materi.create', compact('nextIdMateri', 'kelasList'));
    }

    /**
     * Store a newly created materi in storage
     */
    public function store(Request $request)
    {
        // Ambil nama kelas yang valid dari tabel kelas
        $validKelasNames = Kelas::active()->pluck('nama_kelas')->implode(',');

        $validated = $request->validate([
            'kategori' => 'required|in:Al-Qur\'an,Hadist,Materi Tambahan',
            'kelas' => 'required|in:' . $validKelasNames,
            'nama_kitab' => 'required|string|max:255',
            'halaman_mulai' => 'required|integer|min:1',
            'halaman_akhir' => 'required|integer|min:1|gte:halaman_mulai',
            'deskripsi' => 'nullable|string',
        ], [
            'kategori.required' => 'Kategori wajib dipilih.',
            'kelas.required' => 'Kelas wajib dipilih.',
            'kelas.in' => 'Kelas yang dipilih tidak valid.',
            'nama_kitab.required' => 'Nama kitab wajib diisi.',
            'halaman_mulai.required' => 'Halaman mulai wajib diisi.',
            'halaman_mulai.min' => 'Halaman mulai minimal 1.',
            'halaman_akhir.required' => 'Halaman akhir wajib diisi.',
            'halaman_akhir.gte' => 'Halaman akhir harus lebih besar atau sama dengan halaman mulai.',
        ]);

        // Create materi
        $materi = Materi::create($validated);

        // Auto-create capaian untuk semua santri di kelas tersebut (via relasi baru)
        $santris = Santri::kelasByName($validated['kelas'])
            ->where('status', 'Aktif')
            ->get();

        // Get semester aktif
        $semesterAktif = Semester::aktif()->first();

        if ($semesterAktif && $santris->count() > 0) {
            foreach ($santris as $santri) {
                // Create capaian dengan progress 0
                Capaian::create([
                    'id_santri' => $santri->id_santri,
                    'id_materi' => $materi->id_materi,
                    'id_semester' => $semesterAktif->id_semester,
                    'halaman_selesai' => '',
                    'persentase' => 0,
                    'catatan' => 'Auto-created untuk materi baru',
                    'tanggal_input' => now(),
                ]);
            }
        }

        // Clear cache
        Cache::forget('next_materi_id');

        return redirect()->route('admin.materi.index')
            ->with('success', "Data materi berhasil ditambahkan. Capaian otomatis dibuat untuk {$santris->count()} santri kelas {$validated['kelas']}.");
    }

    /**
     * Display the specified materi
     */
    public function show(Materi $materi)
    {
        // Load relasi capaian jika ada (nanti di langkah 2)
        // $materi->load('capaian.santri');

        return view('admin.materi.show', compact('materi'));
    }

    /**
     * Show the form for editing the specified materi
     */
    public function edit(Materi $materi)
    {
        $kelasList = Kelas::active()->ordered()->get();
        return view('admin.materi.edit', compact('materi', 'kelasList'));
    }

    /**
     * Update the specified materi in storage
     */
    public function update(Request $request, Materi $materi)
    {
        $validKelasNames = Kelas::active()->pluck('nama_kelas')->implode(',');

        $validated = $request->validate([
            'kategori' => 'required|in:Al-Qur\'an,Hadist,Materi Tambahan',
            'kelas' => 'required|in:' . $validKelasNames,
            'nama_kitab' => 'required|string|max:255',
            'halaman_mulai' => 'required|integer|min:1',
            'halaman_akhir' => 'required|integer|min:1|gte:halaman_mulai',
            'deskripsi' => 'nullable|string',
        ], [
            'kategori.required' => 'Kategori wajib dipilih.',
            'kelas.required' => 'Kelas wajib dipilih.',
            'kelas.in' => 'Kelas yang dipilih tidak valid.',
            'nama_kitab.required' => 'Nama kitab wajib diisi.',
            'halaman_mulai.required' => 'Halaman mulai wajib diisi.',
            'halaman_mulai.min' => 'Halaman mulai minimal 1.',
            'halaman_akhir.required' => 'Halaman akhir wajib diisi.',
            'halaman_akhir.gte' => 'Halaman akhir harus lebih besar atau sama dengan halaman mulai.',
        ]);

        $materi->update($validated);

        return redirect()->route('admin.materi.index')
            ->with('success', 'Data materi berhasil diperbarui.');
    }

    /**
     * Remove the specified materi from storage
     */
    public function destroy(Materi $materi)
    {
        $namaKitab = $materi->nama_kitab;
        
        // TODO: Check jika ada capaian yang terkait (Langkah 2)
        // if ($materi->capaian()->exists()) {
        //     return redirect()->route('admin.materi.index')
        //         ->with('error', 'Tidak dapat menghapus materi yang sudah memiliki data capaian.');
        // }

        $materi->delete();

        return redirect()->route('admin.materi.index')
            ->with('success', 'Data materi "' . $namaKitab . '" berhasil dihapus.');
    }
}