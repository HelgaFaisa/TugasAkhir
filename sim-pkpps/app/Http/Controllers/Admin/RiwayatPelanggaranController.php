<?php
// app/Http/Controllers/Admin/RiwayatPelanggaranController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiwayatPelanggaran;
use App\Models\KategoriPelanggaran;
use App\Models\Santri;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RiwayatPelanggaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RiwayatPelanggaran::with(['santri', 'kategori']);

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $query->search($request->search);
        }

        // Filter berdasarkan santri
        if ($request->has('id_santri') && $request->id_santri != '') {
            $query->bySantri($request->id_santri);
        }

        // Filter berdasarkan kategori
        if ($request->has('id_kategori') && $request->id_kategori != '') {
            $query->byKategori($request->id_kategori);
        }

        // Filter berdasarkan tanggal
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai != '') {
            $tanggalSelesai = $request->tanggal_selesai ?? $request->tanggal_mulai;
            $query->byTanggal($request->tanggal_mulai, $tanggalSelesai);
        }

        // Filter bulan ini
        if ($request->has('bulan_ini') && $request->bulan_ini == '1') {
            $query->bulanIni();
        }

        $data = $query->terbaru()->paginate(15);

        // Data untuk filter dropdown
        $santriList = Santri::aktif()->orderBy('nama_lengkap')->get();
        $kategoriList = KategoriPelanggaran::orderBy('nama_pelanggaran')->get();

        // Statistik
        $totalPelanggaran = RiwayatPelanggaran::count();
        $pelanggaranBulanIni = RiwayatPelanggaran::bulanIni()->count();
        $totalPoin = RiwayatPelanggaran::sum('poin');

        return view('admin.riwayat_pelanggaran.index', compact(
            'data',
            'santriList',
            'kategoriList',
            'totalPelanggaran',
            'pelanggaranBulanIni',
            'totalPoin'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Generate preview ID riwayat berikutnya
        $lastRiwayat = RiwayatPelanggaran::orderBy('id', 'desc')->first();
        $nextNum = $lastRiwayat ? intval(substr($lastRiwayat->id_riwayat, 1)) + 1 : 1;
        $nextIdRiwayat = 'P' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        // Data untuk dropdown
        $santriList = Santri::aktif()->orderBy('nama_lengkap')->get();
        $kategoriList = KategoriPelanggaran::orderBy('nama_pelanggaran')->get();

        return view('admin.riwayat_pelanggaran.create', compact(
            'nextIdRiwayat',
            'santriList',
            'kategoriList'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'id_kategori' => 'required|exists:kategori_pelanggarans,id_kategori',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'id_santri.required' => 'Santri wajib dipilih.',
            'id_santri.exists' => 'Santri tidak ditemukan.',
            'id_kategori.required' => 'Kategori pelanggaran wajib dipilih.',
            'id_kategori.exists' => 'Kategori tidak ditemukan.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
        ]);

        // Ambil poin dari kategori
        $kategori = KategoriPelanggaran::where('id_kategori', $validated['id_kategori'])->first();
        $validated['poin'] = $kategori->poin;

        RiwayatPelanggaran::create($validated);

        return redirect()->route('admin.riwayat-pelanggaran.index')
            ->with('success', 'Riwayat pelanggaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RiwayatPelanggaran $riwayatPelanggaran)
    {
        $riwayatPelanggaran->load(['santri', 'kategori']);

        // Riwayat pelanggaran santri lainnya
        $riwayatLainnya = RiwayatPelanggaran::where('id_santri', $riwayatPelanggaran->id_santri)
            ->where('id', '!=', $riwayatPelanggaran->id)
            ->with('kategori')
            ->terbaru()
            ->limit(5)
            ->get();

        return view('admin.riwayat_pelanggaran.show', compact(
            'riwayatPelanggaran',
            'riwayatLainnya'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiwayatPelanggaran $riwayatPelanggaran)
    {
        $riwayatPelanggaran->load(['santri', 'kategori']);

        // Data untuk dropdown
        $santriList = Santri::aktif()->orderBy('nama_lengkap')->get();
        $kategoriList = KategoriPelanggaran::orderBy('nama_pelanggaran')->get();

        return view('admin.riwayat_pelanggaran.edit', compact(
            'riwayatPelanggaran',
            'santriList',
            'kategoriList'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiwayatPelanggaran $riwayatPelanggaran)
    {
        $validated = $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'id_kategori' => 'required|exists:kategori_pelanggarans,id_kategori',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'id_santri.required' => 'Santri wajib dipilih.',
            'id_santri.exists' => 'Santri tidak ditemukan.',
            'id_kategori.required' => 'Kategori pelanggaran wajib dipilih.',
            'id_kategori.exists' => 'Kategori tidak ditemukan.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
        ]);

        // Ambil poin dari kategori
        $kategori = KategoriPelanggaran::where('id_kategori', $validated['id_kategori'])->first();
        $validated['poin'] = $kategori->poin;

        $riwayatPelanggaran->update($validated);

        return redirect()->route('admin.riwayat-pelanggaran.index')
            ->with('success', 'Riwayat pelanggaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiwayatPelanggaran $riwayatPelanggaran)
    {
        $idRiwayat = $riwayatPelanggaran->id_riwayat;
        $namaSantri = $riwayatPelanggaran->santri->nama_lengkap ?? 'Unknown';

        $riwayatPelanggaran->delete();

        return redirect()->route('admin.riwayat-pelanggaran.index')
            ->with('success', 'Riwayat pelanggaran ' . $idRiwayat . ' untuk santri ' . $namaSantri . ' berhasil dihapus.');
    }

    /**
     * Tampilkan riwayat pelanggaran per santri
     */
    public function riwayatSantri($idSantri)
    {
        $santri = Santri::where('id_santri', $idSantri)->firstOrFail();
        
        $riwayat = RiwayatPelanggaran::with('kategori')
            ->bySantri($idSantri)
            ->terbaru()
            ->paginate(10);

        $totalPoin = RiwayatPelanggaran::bySantri($idSantri)->sum('poin');
        $totalPelanggaran = RiwayatPelanggaran::bySantri($idSantri)->count();

        return view('admin.riwayat_pelanggaran.riwayat_santri', compact(
            'santri',
            'riwayat',
            'totalPoin',
            'totalPelanggaran'
        ));
    }
}