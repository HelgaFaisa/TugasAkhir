<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SemesterController extends Controller
{
    /**
     * Display a listing of semester
     */
    public function index(Request $request)
    {
        $query = Semester::query();

        // Filter tahun ajaran
        if ($request->filled('tahun_ajaran')) {
            $query->tahunAjaran($request->tahun_ajaran);
        }

        $semesters = $query->orderBy('tahun_ajaran', 'desc')
            ->orderBy('periode', 'desc')
            ->paginate(10)
            ->appends(request()->query());

        return view('admin.semester.index', compact('semesters'));
    }

    /**
     * Show the form for creating a new semester
     */
    public function create()
    {
        $nextIdSemester = Cache::remember('next_semester_id', 60, function () {
            $last = Semester::orderBy('id', 'desc')->first();
            $num = $last ? intval(substr($last->id_semester, 3)) + 1 : 1;
            return 'SEM' . str_pad($num, 3, '0', STR_PAD_LEFT);
        });

        return view('admin.semester.create', compact('nextIdSemester'));
    }

    /**
     * Store a newly created semester
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20',
            'periode' => 'required|in:1,2',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean',
        ], [
            'tahun_ajaran.required' => 'Tahun ajaran wajib diisi.',
            'periode.required' => 'Periode wajib dipilih.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_akhir.required' => 'Tanggal akhir wajib diisi.',
            'tanggal_akhir.after' => 'Tanggal akhir harus setelah tanggal mulai.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Semester::create($validated);

        Cache::forget('next_semester_id');

        return redirect()->route('admin.semester.index')
            ->with('success', 'Semester berhasil ditambahkan.');
    }

    /**
     * Show the specified semester
     */
    public function show(Semester $semester)
    {
        // Load statistik capaian
        $semester->load(['capaian.santri', 'capaian.materi']);
        
        $totalCapaian = $semester->capaian()->count();
        $santriUnik = $semester->capaian()->distinct('id_santri')->count('id_santri');
        $rataRataPersentase = $semester->capaian()->avg('persentase') ?? 0;

        return view('admin.semester.show', compact('semester', 'totalCapaian', 'santriUnik', 'rataRataPersentase'));
    }

    /**
     * Show the form for editing the specified semester
     */
    public function edit(Semester $semester)
    {
        return view('admin.semester.edit', compact('semester'));
    }

    /**
     * Update the specified semester
     */
    public function update(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20',
            'periode' => 'required|in:1,2',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean',
        ], [
            'tahun_ajaran.required' => 'Tahun ajaran wajib diisi.',
            'periode.required' => 'Periode wajib dipilih.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_akhir.required' => 'Tanggal akhir wajib diisi.',
            'tanggal_akhir.after' => 'Tanggal akhir harus setelah tanggal mulai.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $semester->update($validated);

        return redirect()->route('admin.semester.index')
            ->with('success', 'Semester berhasil diperbarui.');
    }

    /**
     * Remove the specified semester
     */
    public function destroy(Semester $semester)
    {
        // Check jika ada capaian terkait
        if ($semester->capaian()->exists()) {
            return redirect()->route('admin.semester.index')
                ->with('error', 'Tidak dapat menghapus semester yang sudah memiliki data capaian.');
        }

        $namaSemester = $semester->nama_semester;
        $semester->delete();

        return redirect()->route('admin.semester.index')
            ->with('success', 'Semester "' . $namaSemester . '" berhasil dihapus.');
    }

    /**
     * Toggle status aktif semester
     */
    public function toggleAktif(Semester $semester)
    {
        $semester->is_active = !$semester->is_active;
        $semester->save();

        $status = $semester->is_active ? 'aktif' : 'tidak aktif';

        return redirect()->route('admin.semester.index')
            ->with('success', 'Semester berhasil diubah menjadi ' . $status . '.');
    }
}