<?php
// app/Http/Controllers/Admin/PembayaranSppController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembayaranSpp;
use App\Models\Santri;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PembayaranSppController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Default tab
        $tab = $request->get('tab', 'belum-bayar');
        
        // Default bulan dan tahun ke bulan/tahun saat ini jika tidak ada filter
        $bulan = $request->filled('bulan') ? $request->bulan : date('n');
        $tahun = $request->filled('tahun') ? $request->tahun : date('Y');
        
        // Query untuk mendapatkan data pembayaran berdasarkan filter
        $query = PembayaranSpp::with('santri')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun);

        // Data untuk filter
        $tahunList = PembayaranSpp::selectRaw('DISTINCT tahun')
                                   ->orderBy('tahun', 'desc')
                                   ->pluck('tahun');

        // Tambahkan tahun saat ini jika belum ada
        if (!$tahunList->contains(date('Y'))) {
            $tahunList->prepend(date('Y'));
        }

        // Get santri dengan status pembayaran untuk periode yang dipilih
        $santriList = Santri::where('status', 'Aktif')
            ->with(['pembayaranSpp' => function($q) use ($bulan, $tahun) {
                $q->where('bulan', $bulan)->where('tahun', $tahun);
            }])
            ->get()
            ->map(function($santri) use ($bulan, $tahun) {
                $pembayaran = $santri->pembayaranSpp->first();
                
                return [
                    'id_santri' => $santri->id_santri,
                    'nama_lengkap' => $santri->nama_lengkap,
                    'nis' => $santri->nis,
                    'kelas' => $santri->kelas,
                    'pembayaran' => $pembayaran,
                    'status' => $pembayaran ? $pembayaran->status : 'Belum Ada Tagihan',
                    'is_telat' => $pembayaran ? $pembayaran->isTelat() : false,
                    'nominal' => $pembayaran ? $pembayaran->nominal : 0,
                    'tanggal_bayar' => $pembayaran ? $pembayaran->tanggal_bayar : null,
                    'batas_bayar' => $pembayaran ? $pembayaran->batas_bayar : null,
                ];
            });

        // Filter berdasarkan tab
        if ($tab === 'sudah-bayar') {
            $santriList = $santriList->filter(function($item) {
                return $item['pembayaran'] && $item['status'] === 'Lunas';
            });
        } else {
            // Belum bayar (termasuk yang belum ada tagihan dan yang telat)
            $santriList = $santriList->filter(function($item) {
                return !$item['pembayaran'] || $item['status'] !== 'Lunas';
            });
        }

        // Filter search
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $santriList = $santriList->filter(function($item) use ($search) {
                return str_contains(strtolower($item['nama_lengkap']), $search) ||
                       str_contains(strtolower($item['id_santri']), $search) ||
                       str_contains(strtolower($item['nis']), $search);
            });
        }

        // Filter status spesifik
        if ($request->filled('filter_status')) {
            if ($request->filter_status === 'Telat') {
                $santriList = $santriList->filter(function($item) {
                    return $item['is_telat'];
                });
            } elseif ($request->filter_status === 'Belum Ada Tagihan') {
                $santriList = $santriList->filter(function($item) {
                    return !$item['pembayaran'];
                });
            } else {
                $santriList = $santriList->filter(function($item) use ($request) {
                    return $item['status'] === $request->filter_status;
                });
            }
        }

        // Hitung statistik
        $totalSantri = $santriList->count();
        $totalLunas = $santriList->where('status', 'Lunas')->count();
        $totalBelumBayar = $santriList->where('status', 'Belum Lunas')->count();
        $totalTelat = $santriList->where('is_telat', true)->count();
        $totalBelumAdaTagihan = $santriList->where('status', 'Belum Ada Tagihan')->count();
        
        $nominalLunas = $santriList->where('status', 'Lunas')->sum('nominal');
        $nominalBelumLunas = $santriList->where('status', 'Belum Lunas')->sum('nominal');

        // Sort
        $santriList = $santriList->sortBy('nama_lengkap')->values();

        // Manual pagination
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $santriPaginated = $santriList->slice($offset, $perPage)->values();
        $totalPages = ceil($santriList->count() / $perPage);

        return view('admin.pembayaran-spp.index', compact(
            'santriPaginated',
            'tab',
            'bulan',
            'tahun',
            'tahunList',
            'totalSantri',
            'totalLunas',
            'totalBelumBayar',
            'totalTelat',
            'totalBelumAdaTagihan',
            'nominalLunas',
            'nominalBelumLunas',
            'currentPage',
            'totalPages'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil santri yang aktif
        $santris = Santri::where('status', 'Aktif')
                        ->orderBy('nama_lengkap', 'asc')
                        ->get();

        // Generate preview ID
        $last = PembayaranSpp::orderBy('id', 'desc')->first();
        $nextNum = $last ? intval(substr($last->id_pembayaran, 3)) + 1 : 1;
        $nextId = 'SPP' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('admin.pembayaran-spp.create', compact('santris', 'nextId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100',
            'nominal' => 'required|numeric|min:0',
            'status' => 'required|in:Lunas,Belum Lunas',
            'tanggal_bayar' => 'nullable|date',
            'batas_bayar' => 'required|date',
            'keterangan' => 'nullable|string',
        ], [
            'id_santri.required' => 'Santri wajib dipilih.',
            'id_santri.exists' => 'Santri tidak ditemukan.',
            'bulan.required' => 'Bulan wajib diisi.',
            'bulan.min' => 'Bulan harus antara 1-12.',
            'bulan.max' => 'Bulan harus antara 1-12.',
            'tahun.required' => 'Tahun wajib diisi.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.min' => 'Nominal minimal 0.',
            'status.required' => 'Status wajib dipilih.',
            'batas_bayar.required' => 'Batas bayar wajib diisi.',
        ]);

        // Cek duplikasi
        $exists = PembayaranSpp::where('id_santri', $validated['id_santri'])
                               ->where('bulan', $validated['bulan'])
                               ->where('tahun', $validated['tahun'])
                               ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Data pembayaran untuk periode ini sudah ada.');
        }

        // Jika status lunas dan tanggal_bayar kosong, set ke hari ini
        if ($validated['status'] === 'Lunas' && empty($validated['tanggal_bayar'])) {
            $validated['tanggal_bayar'] = Carbon::now()->format('Y-m-d');
        }

        PembayaranSpp::create($validated);

        return redirect()->route('admin.pembayaran-spp.index')
                        ->with('success', 'Data pembayaran SPP berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PembayaranSpp $pembayaranSpp)
    {
        $pembayaranSpp->load('santri');
        return view('admin.pembayaran-spp.show', compact('pembayaranSpp'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PembayaranSpp $pembayaranSpp)
    {
        $santris = Santri::orderBy('nama_lengkap', 'asc')->get();
        return view('admin.pembayaran-spp.edit', compact('pembayaranSpp', 'santris'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PembayaranSpp $pembayaranSpp)
    {
        $validated = $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100',
            'nominal' => 'required|numeric|min:0',
            'status' => 'required|in:Lunas,Belum Lunas',
            'tanggal_bayar' => 'nullable|date',
            'batas_bayar' => 'required|date',
            'keterangan' => 'nullable|string',
        ], [
            'id_santri.required' => 'Santri wajib dipilih.',
            'bulan.required' => 'Bulan wajib diisi.',
            'tahun.required' => 'Tahun wajib diisi.',
            'nominal.required' => 'Nominal wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
            'batas_bayar.required' => 'Batas bayar wajib diisi.',
        ]);

        // Cek duplikasi (kecuali data sendiri)
        $exists = PembayaranSpp::where('id_santri', $validated['id_santri'])
                               ->where('bulan', $validated['bulan'])
                               ->where('tahun', $validated['tahun'])
                               ->where('id', '!=', $pembayaranSpp->id)
                               ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Data pembayaran untuk periode ini sudah ada.');
        }

        // Jika status lunas dan tanggal_bayar kosong, set ke hari ini
        if ($validated['status'] === 'Lunas' && empty($validated['tanggal_bayar'])) {
            $validated['tanggal_bayar'] = Carbon::now()->format('Y-m-d');
        }

        $pembayaranSpp->update($validated);

        return redirect()->route('admin.pembayaran-spp.index')
                        ->with('success', 'Data pembayaran SPP berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PembayaranSpp $pembayaranSpp)
    {
        $periode = $pembayaranSpp->periode_lengkap;
        $santri = $pembayaranSpp->santri->nama_lengkap;
        
        $pembayaranSpp->delete();

        return redirect()->route('admin.pembayaran-spp.index')
                        ->with('success', "Data pembayaran SPP {$periode} untuk {$santri} berhasil dihapus.");
    }

    /**
     * Tampilkan riwayat pembayaran per santri
     */
    public function riwayat($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        
        $pembayaranSpp = PembayaranSpp::where('id_santri', $id_santri)
                                      ->orderBy('tahun', 'desc')
                                      ->orderBy('bulan', 'desc')
                                      ->paginate(15);

        // Statistik
        $totalBayar = PembayaranSpp::where('id_santri', $id_santri)
                                   ->where('status', 'Lunas')
                                   ->sum('nominal');
        
        $totalTunggakan = PembayaranSpp::where('id_santri', $id_santri)
                                       ->where('status', 'Belum Lunas')
                                       ->sum('nominal');
        
        $jumlahTelat = PembayaranSpp::where('id_santri', $id_santri)
                                    ->where('status', 'Belum Lunas')
                                    ->where('batas_bayar', '<', Carbon::now())
                                    ->count();

        return view('admin.pembayaran-spp.riwayat', compact(
            'santri', 
            'pembayaranSpp', 
            'totalBayar', 
            'totalTunggakan', 
            'jumlahTelat'
        ));
    }

    /**
     * Generate SPP untuk semua santri aktif dalam periode tertentu
     */
    public function generate(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'bulan' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer|min:2020|max:2100',
                'nominal' => 'required|numeric|min:0',
                'batas_bayar' => 'required|date',
            ]);

            $santris = Santri::where('status', 'Aktif')->get();
            $generated = 0;
            $skipped = 0;

            foreach ($santris as $santri) {
                // Cek apakah sudah ada
                $exists = PembayaranSpp::where('id_santri', $santri->id_santri)
                                       ->where('bulan', $validated['bulan'])
                                       ->where('tahun', $validated['tahun'])
                                       ->exists();

                if (!$exists) {
                    PembayaranSpp::create([
                        'id_santri' => $santri->id_santri,
                        'bulan' => $validated['bulan'],
                        'tahun' => $validated['tahun'],
                        'nominal' => $validated['nominal'],
                        'status' => 'Belum Lunas',
                        'batas_bayar' => $validated['batas_bayar'],
                    ]);
                    $generated++;
                } else {
                    $skipped++;
                }
            }

            return redirect()->route('admin.pembayaran-spp.index')
                           ->with('success', "Berhasil generate {$generated} data SPP. {$skipped} data dilewati (sudah ada).");
        }

        return view('admin.pembayaran-spp.generate');
    }

    /**
     * Halaman pilihan laporan
     */
    public function laporan()
    {
        return view('admin.pembayaran-spp.laporan');
    }

    /**
     * Cetak laporan SPP (semua data atau filter)
     */
    public function cetakLaporan(Request $request)
    {
        $query = PembayaranSpp::with('santri');

        // Filter
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('status')) {
            if ($request->status === 'Telat') {
                $query->telat();
            } else {
                $query->where('status', $request->status);
            }
        }

        $pembayaranSpp = $query->orderBy('tahun', 'desc')
                              ->orderBy('bulan', 'desc')
                              ->get();

        // Statistik
        $totalLunas = $pembayaranSpp->where('status', 'Lunas')->sum('nominal');
        $totalTunggakan = $pembayaranSpp->where('status', 'Belum Lunas')->sum('nominal');
        $jumlahTelat = $pembayaranSpp->filter(function($spp) {
            return $spp->isTelat();
        })->count();

        return view('admin.pembayaran-spp.cetak-laporan', compact(
            'pembayaranSpp',
            'totalLunas',
            'totalTunggakan',
            'jumlahTelat'
        ));
    }

    /**
     * Cetak laporan SPP per santri
     */
    public function cetakLaporanSantri($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        
        $pembayaranSpp = PembayaranSpp::where('id_santri', $id_santri)
                                      ->orderBy('tahun', 'desc')
                                      ->orderBy('bulan', 'desc')
                                      ->get();

        // Statistik
        $totalLunas = $pembayaranSpp->where('status', 'Lunas')->sum('nominal');
        $totalTunggakan = $pembayaranSpp->where('status', 'Belum Lunas')->sum('nominal');
        $jumlahTelat = $pembayaranSpp->filter(function($spp) {
            return $spp->isTelat();
        })->count();

        return view('admin.pembayaran-spp.cetak-laporan-santri', compact(
            'santri',
            'pembayaranSpp',
            'totalLunas',
            'totalTunggakan',
            'jumlahTelat'
        ));
    }

    /**
     * Cetak bukti pembayaran
     */
    public function cetakBukti(PembayaranSpp $pembayaranSpp)
    {
        $pembayaranSpp->load('santri');
        return view('admin.pembayaran-spp.cetak-bukti', compact('pembayaranSpp'));
    }
}