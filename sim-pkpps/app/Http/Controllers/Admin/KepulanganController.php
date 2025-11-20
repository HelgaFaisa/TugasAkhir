<?php
// app/Http/Controllers/Admin/KepulanganController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kepulangan;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class KepulanganController extends Controller
{
    /**
     * Display a listing of kepulangan
     */
    public function index(Request $request)
    {
        $query = Kepulangan::with('santri');

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pulang', $request->tahun);
        }

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_pulang', $request->bulan);
        }

        // Get data dengan pagination
        $kepulangan = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total_data' => Kepulangan::count(),
            'menunggu_approval' => Kepulangan::where('status', 'Menunggu')->count(),
            'sedang_izin' => Kepulangan::aktif()->count(),
            'over_limit_santri' => $this->getOverLimitSantri()->count(),
        ];

        // Get unique years for filter
        $tahunList = Kepulangan::selectRaw('YEAR(tanggal_pulang) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Get santri yang over limit untuk highlight
        $santriOverLimit = $this->getOverLimitSantri()->pluck('total_hari', 'id_santri');

        return view('admin.kepulangan.index', compact(
            'kepulangan',
            'stats',
            'tahunList',
            'santriOverLimit'
        ));
    }

    /**
     * Show the form for creating a new kepulangan
     */
    public function create()
    {
        $santriList = Santri::where('status', 'Aktif')
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.kepulangan.create', compact('santriList'));
    }

    /**
     * Store a newly created kepulangan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'tanggal_pulang' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after:tanggal_pulang',
            'alasan' => 'required|string|min:10|max:500',
        ], [
            'id_santri.required' => 'Santri wajib dipilih.',
            'id_santri.exists' => 'Santri tidak ditemukan.',
            'tanggal_pulang.required' => 'Tanggal pulang wajib diisi.',
            'tanggal_pulang.after_or_equal' => 'Tanggal pulang tidak boleh kurang dari hari ini.',
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_kembali.after' => 'Tanggal kembali harus setelah tanggal pulang.',
            'alasan.required' => 'Alasan kepulangan wajib diisi.',
            'alasan.min' => 'Alasan minimal 10 karakter.',
            'alasan.max' => 'Alasan maksimal 500 karakter.',
        ]);

        // Create kepulangan
        Kepulangan::create($validated);

        return redirect()->route('admin.kepulangan.index')
            ->with('success', 'Izin kepulangan berhasil diajukan.');
    }

    /**
     * Display the specified kepulangan
     */
    public function show($id_kepulangan)
    {
        // Cari data berdasarkan id_kepulangan (KP001, KP002, dst)
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)
            ->with('santri')
            ->firstOrFail();

        // Get detail izin tahun ini
        $tahunSekarang = Carbon::now()->year;
        $detailIzin = $this->getDetailIzinSantri($kepulangan->id_santri, $tahunSekarang);

        // Get history kepulangan santri (exclude current)
        $history = Kepulangan::where('id_santri', $kepulangan->id_santri)
            ->where('id_kepulangan', '!=', $id_kepulangan)
            ->orderBy('tanggal_pulang', 'desc')
            ->limit(5)
            ->get();

        return view('admin.kepulangan.show', compact(
            'kepulangan',
            'detailIzin',
            'history'
        ));
    }

    /**
     * Show the form for editing kepulangan
     */
    public function edit($id_kepulangan)
    {
        // Cari data berdasarkan id_kepulangan
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)->firstOrFail();
        
        // Hanya bisa edit jika status Menunggu
        if ($kepulangan->status !== 'Menunggu') {
            return redirect()->route('admin.kepulangan.index')
                ->with('error', 'Hanya izin dengan status "Menunggu" yang bisa diedit.');
        }

        $santriList = Santri::where('status', 'Aktif')
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.kepulangan.edit', compact('kepulangan', 'santriList'));
    }

    /**
     * Update the specified kepulangan
     */
    public function update(Request $request, $id_kepulangan)
    {
        // Cari data berdasarkan id_kepulangan
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)->firstOrFail();
        
        // Hanya bisa update jika status Menunggu
        if ($kepulangan->status !== 'Menunggu') {
            return redirect()->route('admin.kepulangan.index')
                ->with('error', 'Hanya izin dengan status "Menunggu" yang bisa diubah.');
        }

        $validated = $request->validate([
            'tanggal_pulang' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after:tanggal_pulang',
            'alasan' => 'required|string|min:10|max:500',
        ], [
            'tanggal_pulang.required' => 'Tanggal pulang wajib diisi.',
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_kembali.after' => 'Tanggal kembali harus setelah tanggal pulang.',
            'alasan.required' => 'Alasan kepulangan wajib diisi.',
            'alasan.min' => 'Alasan minimal 10 karakter.',
        ]);

        $kepulangan->update($validated);

        return redirect()->route('admin.kepulangan.index')
            ->with('success', 'Data kepulangan berhasil diperbarui.');
    }

    /**
     * Remove the specified kepulangan
     */
    public function destroy($id_kepulangan)
    {
        // Cari data berdasarkan id_kepulangan
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)->firstOrFail();
        
        // Hanya bisa hapus jika status Menunggu atau Ditolak
        if (!in_array($kepulangan->status, ['Menunggu', 'Ditolak'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya izin dengan status "Menunggu" atau "Ditolak" yang bisa dihapus.'
            ], 403);
        }

        $kepulangan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kepulangan berhasil dihapus.'
        ]);
    }

    /**
     * Approve kepulangan
     */
    public function approve(Request $request, $id_kepulangan)
    {
        // Cari data berdasarkan id_kepulangan
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)->firstOrFail();
        
        if ($kepulangan->status !== 'Menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Izin sudah diproses sebelumnya.'
            ], 400);
        }

        // Update status - catatan opsional (tidak perlu validasi)
        $kepulangan->update([
            'status' => 'Disetujui',
            'approved_by' => Auth::user()->name,
            'approved_at' => now(),
            'catatan' => $request->catatan ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Izin kepulangan berhasil disetujui.'
        ]);
    }

    /**
     * Reject kepulangan
     */
    public function reject(Request $request, $id_kepulangan)
    {
        // Cari data berdasarkan id_kepulangan
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)->firstOrFail();
        
        // Validasi alasan penolakan (wajib diisi minimal 10 karakter)
        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|min:10',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

        if ($kepulangan->status !== 'Menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Izin sudah diproses sebelumnya.'
            ], 400);
        }

        $kepulangan->update([
            'status' => 'Ditolak',
            'approved_by' => Auth::user()->name,
            'approved_at' => now(),
            'catatan' => $validated['alasan_penolakan'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Izin kepulangan telah ditolak.'
        ]);
    }

    /**
     * Complete kepulangan (mark as selesai)
     */
    public function complete($id_kepulangan)
    {
        // Cari data berdasarkan id_kepulangan
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)->firstOrFail();
        
        if ($kepulangan->status !== 'Disetujui') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya izin yang disetujui yang bisa diselesaikan.'
            ], 400);
        }

        $kepulangan->update([
            'status' => 'Selesai',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kepulangan santri berhasil diselesaikan.'
        ]);
    }

    /**
     * Print surat izin kepulangan
     */
    public function print($id_kepulangan)
    {
        // Cari data berdasarkan id_kepulangan
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)
            ->with('santri')
            ->firstOrFail();
        
        if ($kepulangan->status !== 'Disetujui') {
            return redirect()->route('admin.kepulangan.show', $id_kepulangan)
                ->with('error', 'Hanya izin yang disetujui yang bisa dicetak.');
        }

        $santri = $kepulangan->santri;
        $tanggalCetak = Carbon::now()->format('d F Y');

        $pdf = Pdf::loadView('admin.kepulangan.surat-pdf', compact(
            'kepulangan',
            'santri',
            'tanggalCetak'
        ));

        return $pdf->stream('Surat-Izin-' . $kepulangan->id_kepulangan . '.pdf');
    }

    /**
     * API: Get santri data with penggunaan izin
     */
    public function getSantriData($idSantri)
    {
        $santri = Santri::where('id_santri', $idSantri)->first();

        if (!$santri) {
            return response()->json([
                'success' => false,
                'message' => 'Santri tidak ditemukan.'
            ], 404);
        }

        $tahunSekarang = Carbon::now()->year;
        $penggunaanIzin = $this->getDetailIzinSantri($idSantri, $tahunSekarang);

        return response()->json([
            'success' => true,
            'santri' => $santri,
            'penggunaan_izin' => [
                'total_hari' => $penggunaanIzin['total_hari'],
                'total_izin' => $penggunaanIzin['total_izin'],
                'sisa_kuota' => $penggunaanIzin['sisa_kuota'],
                'over_limit' => $penggunaanIzin['over_limit'],
            ]
        ]);
    }

    /**
     * Helper: Get detail izin santri per tahun
     */
    private function getDetailIzinSantri($idSantri, $tahun)
    {
        $kepulanganList = Kepulangan::where('id_santri', $idSantri)
            ->where('status', 'Disetujui')
            ->whereYear('tanggal_pulang', $tahun)
            ->orderBy('tanggal_pulang', 'desc')
            ->get();

        $totalHari = $kepulanganList->sum('durasi_izin');
        $totalIzin = $kepulanganList->count();
        $sisaKuota = max(0, 12 - $totalHari);
        $overLimit = $totalHari > 12;

        $details = $kepulanganList->map(function($item) {
            return [
                'id' => $item->id_kepulangan,
                'tanggal' => $item->tanggal_pulang_formatted . ' - ' . $item->tanggal_kembali_formatted,
                'durasi' => $item->durasi_izin,
                'alasan' => $item->alasan,
            ];
        });

        return [
            'total_hari' => $totalHari,
            'total_izin' => $totalIzin,
            'sisa_kuota' => $sisaKuota,
            'over_limit' => $overLimit,
            'details' => $details,
        ];
    }

    /**
     * Helper: Get santri yang over limit
     */
    private function getOverLimitSantri()
    {
        $tahunSekarang = Carbon::now()->year;

        return Kepulangan::selectRaw('id_santri, SUM(durasi_izin) as total_hari')
            ->where('status', 'Disetujui')
            ->whereYear('tanggal_pulang', $tahunSekarang)
            ->groupBy('id_santri')
            ->having('total_hari', '>', 12)
            ->get();
    }
}