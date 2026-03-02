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
    // ══════════════════════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════════════════════

    public function index(Request $request)
    {
        // Default tab
        $tab = $request->get('tab', 'belum-bayar');

        // Default bulan dan tahun ke bulan/tahun saat ini jika tidak ada filter
        $bulan = $request->filled('bulan') ? $request->bulan : date('n');
        $tahun = $request->filled('tahun') ? $request->tahun : date('Y');

        // Data untuk filter tahun
        $tahunList = PembayaranSpp::selectRaw('DISTINCT tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Tambahkan tahun saat ini jika belum ada
        if (!$tahunList->contains(date('Y'))) {
            $tahunList->prepend(date('Y'));
        }

        // Get santri dengan status pembayaran untuk periode yang dipilih
        $santriList = Santri::where('status', 'Aktif')
            ->with(['pembayaranSpp' => function ($q) use ($bulan, $tahun) {
                $q->where('bulan', $bulan)->where('tahun', $tahun);
            }])
            ->get()
            ->map(function ($santri) {
                $p = $santri->pembayaranSpp->first();
                return [
                    'id_santri'    => $santri->id_santri,
                    'nama_lengkap' => $santri->nama_lengkap,
                    'nis'          => $santri->nis,
                    'kelas'        => $santri->kelas,
                    'pembayaran'   => $p,
                    // status virtual: Lunas / Cicilan / Belum Lunas / Belum Ada Tagihan
                    'status'       => $p ? ($p->status === 'Lunas' ? 'Lunas' : ($p->isCicilan() ? 'Cicilan' : 'Belum Lunas')) : 'Belum Ada Tagihan',
                    'is_telat'     => $p ? $p->isTelat() : false,
                    'nominal'      => $p ? (float) $p->nominal : 0,
                    'tanggal_bayar'=> $p ? $p->tanggal_bayar : null,
                    'batas_bayar'  => $p ? $p->batas_bayar : null,
                ];
            });

        // ─── KPI (hitung dari data PENUH sebelum filter tab) ─────────
        $totalSantriAll       = $santriList->count();
        $totalLunas           = $santriList->where('status', 'Lunas')->count();
        $totalCicilan         = $santriList->where('status', 'Cicilan')->count();
        $totalBelumBayar      = $santriList->whereIn('status', ['Belum Lunas', 'Belum Ada Tagihan'])->count();
        $totalTelat           = $santriList->where('is_telat', true)->count();
        $totalBelumAdaTagihan = $santriList->where('status', 'Belum Ada Tagihan')->count();
        $nominalLunas         = $santriList->where('status', 'Lunas')->sum('nominal');
        $nominalBelumLunas    = $santriList->whereIn('status', ['Belum Lunas', 'Cicilan'])->sum('nominal');

        // ─── Filter tab ───────────────────────────────────────────────
        if ($tab === 'sudah-bayar') {
            $santriList = $santriList
                ->filter(fn($i) => $i['pembayaran'] && $i['status'] === 'Lunas')
                ->sortByDesc(fn($i) => $i['tanggal_bayar'] ? $i['tanggal_bayar']->timestamp : 0);

        } elseif ($tab === 'cicilan') {
            $santriList = $santriList
                ->filter(fn($i) => $i['pembayaran'] && $i['status'] === 'Cicilan')
                ->sortBy('nama_lengkap');

        } else {
            // belum-bayar: status Belum Lunas atau Belum Ada Tagihan
            $santriList = $santriList
                ->filter(fn($i) => in_array($i['status'], ['Belum Lunas', 'Belum Ada Tagihan']))
                ->sortBy('nama_lengkap');
        }

        // ─── Search ───────────────────────────────────────────────────
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $santriList = $santriList->filter(fn($i) =>
                str_contains(strtolower($i['nama_lengkap']), $search) ||
                str_contains(strtolower($i['id_santri']), $search) ||
                str_contains(strtolower($i['nis']), $search)
            );
        }

        // ─── Filter status spesifik (tab belum-bayar) ─────────────────
        if ($request->filled('filter_status')) {
            if ($request->filter_status === 'Telat') {
                $santriList = $santriList->filter(fn($i) => $i['is_telat']);
            } elseif ($request->filter_status === 'Belum Ada Tagihan') {
                $santriList = $santriList->filter(fn($i) => !$i['pembayaran']);
            } else {
                $santriList = $santriList->filter(fn($i) => $i['status'] === $request->filter_status);
            }
        }

        // ─── Pagination manual ────────────────────────────────────────
        $santriList      = $santriList->values();
        $perPage         = 20;
        $currentPage     = $request->get('page', 1);
        $offset          = ($currentPage - 1) * $perPage;
        $santriPaginated = $santriList->slice($offset, $perPage)->values();
        $totalPages      = ceil($santriList->count() / $perPage);
        $totalSantri     = $santriList->count();

        return view('admin.pembayaran-spp.index', compact(
            'santriPaginated', 'tab', 'bulan', 'tahun', 'tahunList',
            'totalSantri', 'totalSantriAll',
            'totalLunas', 'totalCicilan', 'totalBelumBayar',
            'totalTelat', 'totalBelumAdaTagihan',
            'nominalLunas', 'nominalBelumLunas',
            'currentPage', 'totalPages'
        ));
    }

    // ══════════════════════════════════════════════════════
    // CREATE / STORE
    // ══════════════════════════════════════════════════════

    public function create()
    {
        $santris = Santri::where('status', 'Aktif')->orderBy('nama_lengkap', 'asc')->get();
        $last    = PembayaranSpp::orderBy('id', 'desc')->first();
        $nextNum = $last ? intval(substr($last->id_pembayaran, 3)) + 1 : 1;
        $nextId  = 'SPP' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        return view('admin.pembayaran-spp.create', compact('santris', 'nextId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_santri'    => 'required|exists:santris,id_santri',
            'bulan'        => 'required|integer|min:1|max:12',
            'tahun'        => 'required|integer|min:2020|max:2100',
            'nominal'      => 'required|numeric|min:0',
            'status'       => 'required|in:Lunas,Belum Lunas',
            'tanggal_bayar'=> 'nullable|date',
            'batas_bayar'  => 'required|date',
            'keterangan'   => 'nullable|string',
        ], [
            'id_santri.required' => 'Santri wajib dipilih.',
            'id_santri.exists'   => 'Santri tidak ditemukan.',
            'bulan.required'     => 'Bulan wajib diisi.',
            'bulan.min'          => 'Bulan harus antara 1-12.',
            'bulan.max'          => 'Bulan harus antara 1-12.',
            'tahun.required'     => 'Tahun wajib diisi.',
            'nominal.required'   => 'Nominal wajib diisi.',
            'nominal.min'        => 'Nominal minimal 0.',
            'status.required'    => 'Status wajib dipilih.',
            'batas_bayar.required' => 'Batas bayar wajib diisi.',
        ]);

        // Cek duplikasi — jika sudah ada, arahkan ke edit
        $existing = PembayaranSpp::where('id_santri', $validated['id_santri'])
            ->where('bulan', $validated['bulan'])
            ->where('tahun', $validated['tahun'])
            ->first();

        if ($existing) {
            return redirect()->route('admin.pembayaran-spp.edit', $existing->id)
                ->with('info', 'Data SPP untuk periode ini sudah ada. Silakan edit data berikut untuk mengubah status pembayaran.');
        }

        // Jika status lunas dan tanggal_bayar kosong, set ke hari ini
        if ($validated['status'] === 'Lunas' && empty($validated['tanggal_bayar'])) {
            $validated['tanggal_bayar'] = Carbon::now()->format('Y-m-d');
        }

        PembayaranSpp::create($validated);

        return redirect()->route('admin.pembayaran-spp.index')
            ->with('success', 'Data pembayaran SPP berhasil ditambahkan.');
    }

    // ══════════════════════════════════════════════════════
    // SHOW / EDIT / UPDATE / DESTROY
    // ══════════════════════════════════════════════════════

    public function show(PembayaranSpp $pembayaranSpp)
    {
        $pembayaranSpp->load('santri');
        return view('admin.pembayaran-spp.show', compact('pembayaranSpp'));
    }

    public function edit(PembayaranSpp $pembayaranSpp)
    {
        $santris = Santri::orderBy('nama_lengkap', 'asc')->get();
        return view('admin.pembayaran-spp.edit', compact('pembayaranSpp', 'santris'));
    }

    public function update(Request $request, PembayaranSpp $pembayaranSpp)
    {
        $validated = $request->validate([
            'id_santri'    => 'required|exists:santris,id_santri',
            'bulan'        => 'required|integer|min:1|max:12',
            'tahun'        => 'required|integer|min:2020|max:2100',
            'nominal'      => 'required|numeric|min:0',
            'status'       => 'required|in:Lunas,Belum Lunas',
            'tanggal_bayar'=> 'nullable|date',
            'batas_bayar'  => 'required|date',
            'keterangan'   => 'nullable|string',
        ], [
            'id_santri.required'   => 'Santri wajib dipilih.',
            'bulan.required'       => 'Bulan wajib diisi.',
            'tahun.required'       => 'Tahun wajib diisi.',
            'nominal.required'     => 'Nominal wajib diisi.',
            'status.required'      => 'Status wajib dipilih.',
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

        // Jika diubah ke Lunas, hapus data cicilan dari keterangan
        if ($validated['status'] === 'Lunas' && $pembayaranSpp->isCicilan()) {
            $validated['keterangan'] = $pembayaranSpp->catatan_teks; // simpan teks catatan saja
        }

        $pembayaranSpp->update($validated);

        return redirect()->route('admin.pembayaran-spp.index')
            ->with('success', 'Data pembayaran SPP berhasil diperbarui.');
    }

    public function destroy(PembayaranSpp $pembayaranSpp)
    {
        $periode = $pembayaranSpp->periode_lengkap;
        $santri  = $pembayaranSpp->santri->nama_lengkap;
        $pembayaranSpp->delete();
        return redirect()->route('admin.pembayaran-spp.index')
            ->with('success', "Data pembayaran SPP {$periode} untuk {$santri} berhasil dihapus.");
    }

    // ══════════════════════════════════════════════════════
    // QUICK ACTIONS
    // ══════════════════════════════════════════════════════

    /**
     * Tandai Lunas langsung (quick pay)
     */
    public function bayar(Request $request, PembayaranSpp $pembayaranSpp)
    {
        if ($pembayaranSpp->status === 'Lunas') {
            return redirect()->back()->with('info', 'Pembayaran ini sudah berstatus Lunas.');
        }

        $pembayaranSpp->update([
            'status'        => 'Lunas',
            'tanggal_bayar' => $request->filled('tanggal_bayar')
                ? $request->tanggal_bayar
                : Carbon::now()->format('Y-m-d'),
            // Bersihkan data cicilan dari keterangan, simpan catatan teks jika ada
            'keterangan'    => $pembayaranSpp->catatan_teks,
        ]);

        $nama    = $pembayaranSpp->santri->nama_lengkap;
        $periode = $pembayaranSpp->periode_lengkap;

        return redirect()->route('admin.pembayaran-spp.index', [
            'tab'   => 'sudah-bayar',
            'bulan' => $pembayaranSpp->bulan,
            'tahun' => $pembayaranSpp->tahun,
        ])->with('success', "Pembayaran SPP {$periode} untuk {$nama} berhasil ditandai Lunas.");
    }

    /**
     * Catat cicilan (tambah nominal terbayar)
     * Status DB tetap "Belum Lunas" — cicilan disimpan di keterangan (JSON).
     */
    public function catatCicilan(Request $request, PembayaranSpp $pembayaranSpp)
    {
        $request->validate([
            'nominal_cicilan' => 'required|numeric|min:1',
            'catatan'         => 'nullable|string|max:200',
        ]);

        $sudahTerbayar = $pembayaranSpp->nominal_terbayar;
        $totalTagihan  = (float) $pembayaranSpp->nominal;
        $baru          = $sudahTerbayar + (float) $request->nominal_cicilan;

        // Jika total cicilan >= tagihan → otomatis Lunas
        if ($baru >= $totalTagihan) {
            $pembayaranSpp->update([
                'status'        => 'Lunas',
                'tanggal_bayar' => Carbon::now()->format('Y-m-d'),
                'keterangan'    => $request->catatan ?? $pembayaranSpp->catatan_teks,
            ]);

            return redirect()->route('admin.pembayaran-spp.index', [
                'tab'   => 'sudah-bayar',
                'bulan' => $pembayaranSpp->bulan,
                'tahun' => $pembayaranSpp->tahun,
            ])->with('success', "Cicilan terakhir diterima. SPP {$pembayaranSpp->periode_lengkap} untuk {$pembayaranSpp->santri->nama_lengkap} sekarang Lunas.");
        }

        // Masih cicilan — update keterangan saja, status tetap "Belum Lunas"
        $pembayaranSpp->setCicilan($baru, $request->catatan ?? $pembayaranSpp->catatan_teks);
        $pembayaranSpp->save();

        $sisaFormat = 'Rp ' . number_format($totalTagihan - $baru, 0, ',', '.');
        $cicilanFormat = 'Rp ' . number_format((float) $request->nominal_cicilan, 0, ',', '.');

        return redirect()->back()
            ->with('success', "Cicilan {$cicilanFormat} berhasil dicatat. Sisa: {$sisaFormat}");
    }

    // ══════════════════════════════════════════════════════
    // RIWAYAT PER SANTRI
    // ══════════════════════════════════════════════════════

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
            'santri', 'pembayaranSpp', 'totalBayar', 'totalTunggakan', 'jumlahTelat'
        ));
    }

    // ══════════════════════════════════════════════════════
    // GENERATE SPP MASSAL
    // ══════════════════════════════════════════════════════

    public function generate(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'bulan'       => 'required|integer|min:1|max:12',
                'tahun'       => 'required|integer|min:2020|max:2100',
                'nominal'     => 'required|numeric|min:0',
                'batas_bayar' => 'required|date',
            ]);

            $santris   = Santri::where('status', 'Aktif')->get();
            $generated = 0;
            $skipped   = 0;

            foreach ($santris as $santri) {
                $exists = PembayaranSpp::where('id_santri', $santri->id_santri)
                    ->where('bulan', $validated['bulan'])
                    ->where('tahun', $validated['tahun'])
                    ->exists();

                if (!$exists) {
                    PembayaranSpp::create([
                        'id_santri'   => $santri->id_santri,
                        'bulan'       => $validated['bulan'],
                        'tahun'       => $validated['tahun'],
                        'nominal'     => $validated['nominal'],
                        'status'      => 'Belum Lunas',
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

    // ══════════════════════════════════════════════════════
    // LAPORAN & CETAK
    // ══════════════════════════════════════════════════════

    public function laporan()
    {
        return view('admin.pembayaran-spp.laporan');
    }

    public function cetakLaporan(Request $request)
    {
        $query = PembayaranSpp::with('santri');

        if ($request->filled('bulan')) $query->where('bulan', $request->bulan);
        if ($request->filled('tahun')) $query->where('tahun', $request->tahun);
        if ($request->filled('status')) {
            if ($request->status === 'Telat') {
                $query->telat();
            } else {
                $query->where('status', $request->status);
            }
        }

        $pembayaranSpp  = $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();
        $totalLunas     = $pembayaranSpp->where('status', 'Lunas')->sum('nominal');
        $totalTunggakan = $pembayaranSpp->where('status', 'Belum Lunas')->sum('nominal');
        $jumlahTelat    = $pembayaranSpp->filter(fn($s) => $s->isTelat())->count();

        return view('admin.pembayaran-spp.cetak-laporan', compact(
            'pembayaranSpp', 'totalLunas', 'totalTunggakan', 'jumlahTelat'
        ));
    }

    public function cetakLaporanSantri($id_santri)
    {
        $santri        = Santri::where('id_santri', $id_santri)->firstOrFail();
        $pembayaranSpp = PembayaranSpp::where('id_santri', $id_santri)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $totalLunas     = $pembayaranSpp->where('status', 'Lunas')->sum('nominal');
        $totalTunggakan = $pembayaranSpp->where('status', 'Belum Lunas')->sum('nominal');
        $jumlahTelat    = $pembayaranSpp->filter(fn($s) => $s->isTelat())->count();

        return view('admin.pembayaran-spp.cetak-laporan-santri', compact(
            'santri', 'pembayaranSpp', 'totalLunas', 'totalTunggakan', 'jumlahTelat'
        ));
    }

    public function cetakBukti(PembayaranSpp $pembayaranSpp)
    {
        $pembayaranSpp->load('santri');
        return view('admin.pembayaran-spp.cetak-bukti', compact('pembayaranSpp'));
    }
}