<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keuangan;
use App\Models\PembayaranSpp;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        $query = Keuangan::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->bulan($request->bulan, $request->tahun);
        }

        $transaksi = $query->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->appends(request()->query());

        return view('admin.keuangan.index', compact('transaksi'));
    }

    public function create()
    {
        return view('admin.keuangan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis'      => 'required|in:pemasukan,pengeluaran',
            'nominal'    => 'required|numeric|min:1',
            'keterangan' => 'nullable|string|max:500',
            'tanggal'    => 'required|date',
        ], [
            'jenis.required'   => 'Jenis transaksi wajib dipilih.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.min'      => 'Nominal minimal Rp 1.',
            'tanggal.required' => 'Tanggal wajib diisi.',
        ]);

        Keuangan::create($validated);

        return redirect()->route('admin.keuangan.index')
            ->with('success', 'Transaksi keuangan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $transaksi = Keuangan::findOrFail($id);
        return view('admin.keuangan.show', compact('transaksi'));
    }

    public function edit($id)
    {
        $transaksi = Keuangan::findOrFail($id);
        return view('admin.keuangan.edit', compact('transaksi'));
    }

    public function update(Request $request, $id)
    {
        $transaksi = Keuangan::findOrFail($id);

        $validated = $request->validate([
            'jenis'      => 'required|in:pemasukan,pengeluaran',
            'nominal'    => 'required|numeric|min:1',
            'keterangan' => 'nullable|string|max:500',
            'tanggal'    => 'required|date',
        ]);

        $transaksi->update($validated);

        return redirect()->route('admin.keuangan.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Keuangan::findOrFail($id)->delete();

        return redirect()->route('admin.keuangan.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Laporan Neraca: SPP terkumpul vs pengeluaran pondok = sisa kas
     */
    public function laporan(Request $request)
    {
        $bulan = $request->get('bulan', (int) date('n'));
        $tahun = $request->get('tahun', (int) date('Y'));

        // SPP terkumpul bulan ini
        $sppTerkumpul = PembayaranSpp::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->lunas()
            ->sum('nominal');

        // Pemasukan pondok (kas masuk non-SPP)
        $pemasukanPondok = Keuangan::pemasukan()->bulan($bulan, $tahun)->sum('nominal');

        // Pengeluaran pondok
        $pengeluaranPondok = Keuangan::pengeluaran()->bulan($bulan, $tahun)->sum('nominal');

        $totalPemasukan = $sppTerkumpul + $pemasukanPondok;
        $sisaKas = $totalPemasukan - $pengeluaranPondok;

        // Detail pengeluaran terbesar
        $detailPengeluaran = Keuangan::pengeluaran()
            ->bulan($bulan, $tahun)
            ->orderByDesc('nominal')
            ->limit(10)
            ->get();

        // Detail pemasukan non-SPP
        $detailPemasukan = Keuangan::pemasukan()
            ->bulan($bulan, $tahun)
            ->orderByDesc('nominal')
            ->limit(10)
            ->get();

        return view('admin.keuangan.laporan', compact(
            'bulan', 'tahun',
            'sppTerkumpul', 'pemasukanPondok', 'pengeluaranPondok',
            'totalPemasukan', 'sisaKas',
            'detailPengeluaran', 'detailPemasukan'
        ));
    }
}
