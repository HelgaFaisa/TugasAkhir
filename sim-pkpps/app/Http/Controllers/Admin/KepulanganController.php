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
            'over_limit_santri' => count(Kepulangan::getSantriOverLimit()),
        ];

        // Get unique years for filter
        $tahunList = Kepulangan::selectRaw('YEAR(tanggal_pulang) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Get santri yang over limit untuk highlight
        $santriOverLimit = Kepulangan::getSantriOverLimit();

        // Get settings untuk info periode
        $settings = Kepulangan::getSettings();

        return view('admin.kepulangan.index', compact(
            'kepulangan',
            'stats',
            'tahunList',
            'santriOverLimit',
            'settings'
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

        $settings = Kepulangan::getSettings();

        return view('admin.kepulangan.create', compact('santriList', 'settings'));
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

        // Create kepulangan (durasi_izin akan otomatis dihitung di model)
        $kepulangan = Kepulangan::create($validated);

        // Check apakah over limit
        $kuota = Kepulangan::getSisaKuotaSantri($validated['id_santri']);
        $message = 'Izin kepulangan berhasil diajukan.';
        
        if ($kuota['status'] === 'melebihi') {
            $message .= ' ⚠️ PERHATIAN: Santri ini sudah melebihi kuota ' . $kuota['kuota_maksimal'] . ' hari per tahun (Total: ' . $kuota['total_terpakai'] . ' hari).';
        } elseif ($kuota['status'] === 'hampir_habis') {
            $message .= ' ⚠️ Kuota hampir habis. Sisa: ' . $kuota['sisa_kuota'] . ' hari.';
        }

        return redirect()->route('admin.kepulangan.index')
            ->with('success', $message);
    }

    /**
     * Display the specified kepulangan
     */
    public function show($id_kepulangan)
    {
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)
            ->with('santri')
            ->firstOrFail();

        // Get detail kuota santri
        $kuotaSantri = Kepulangan::getSisaKuotaSantri($kepulangan->id_santri);

        // Get settings
        $settings = Kepulangan::getSettings();

        // Get detail izin tahun ini
        $detailIzin = $this->getDetailIzinSantri(
            $kepulangan->id_santri,
            $settings->periode_mulai,
            $settings->periode_akhir
        );

        // Get history kepulangan santri (exclude current)
        $history = Kepulangan::where('id_santri', $kepulangan->id_santri)
            ->where('id_kepulangan', '!=', $id_kepulangan)
            ->orderBy('tanggal_pulang', 'desc')
            ->limit(5)
            ->get();

        return view('admin.kepulangan.show', compact(
            'kepulangan',
            'kuotaSantri',
            'detailIzin',
            'history',
            'settings'
        ));
    }

    /**
     * Show the form for editing kepulangan
     */
    public function edit($id_kepulangan)
    {
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)->firstOrFail();
        
        if ($kepulangan->status !== 'Menunggu') {
            return redirect()->route('admin.kepulangan.index')
                ->with('error', 'Hanya izin dengan status "Menunggu" yang bisa diedit.');
        }

        $santriList = Santri::where('status', 'Aktif')
            ->orderBy('nama_lengkap')
            ->get();

        $settings = Kepulangan::getSettings();
        $kuotaSantri = Kepulangan::getSisaKuotaSantri($kepulangan->id_santri);

        return view('admin.kepulangan.edit', compact(
            'kepulangan', 
            'santriList', 
            'settings',
            'kuotaSantri'
        ));
    }

    /**
     * Update the specified kepulangan
     */
    public function update(Request $request, $id_kepulangan)
    {
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)->firstOrFail();
        
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

        // Update (durasi_izin akan otomatis dihitung ulang di model)
        $kepulangan->update($validated);

        // Check apakah over limit setelah update
        $kuota = Kepulangan::getSisaKuotaSantri($kepulangan->id_santri);
        $message = 'Data kepulangan berhasil diperbarui.';
        
        if ($kuota['status'] === 'melebihi') {
            $message .= ' ⚠️ PERHATIAN: Santri ini sudah melebihi kuota ' . $kuota['kuota_maksimal'] . ' hari.';
        }

        return redirect()->route('admin.kepulangan.show', $id_kepulangan)
            ->with('success', $message);
    }

    /**
     * Remove the specified kepulangan
     */
    public function destroy($id_kepulangan)
    {
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)->firstOrFail();
        
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
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)->firstOrFail();
        
        if ($kepulangan->status !== 'Menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Izin sudah diproses sebelumnya.'
            ], 400);
        }

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
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)->firstOrFail();
        
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
     * Complete kepulangan
     */
    public function complete($id_kepulangan)
    {
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)->firstOrFail();
        
        if ($kepulangan->status !== 'Disetujui') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya izin yang disetujui yang bisa diselesaikan.'
            ], 400);
        }

        $kepulangan->update(['status' => 'Selesai']);

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
     * API: Get santri data with penggunaan kuota
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

        $kuotaSantri = Kepulangan::getSisaKuotaSantri($idSantri);

        return response()->json([
            'success' => true,
            'santri' => $santri,
            'penggunaan_izin' => $kuotaSantri
        ]);
    }

    /**
     * ========================================
     * FITUR PENGATURAN KUOTA
     * ========================================
     */

    /**
     * Show settings page
     */
    public function settings()
    {
        $settings = Kepulangan::getSettings();
        $resetLogs = Kepulangan::getResetLogs(20);
        
        // Statistik periode saat ini
        $totalSantri = Santri::where('status', 'Aktif')->count();
        $santriOverLimit = Kepulangan::getSantriOverLimit();
        $totalIzinPeriodeIni = Kepulangan::whereBetween('tanggal_pulang', [
            $settings->periode_mulai,
            $settings->periode_akhir
        ])->whereIn('status', ['Disetujui', 'Selesai'])->count();

        return view('admin.kepulangan.settings', compact(
            'settings',
            'resetLogs',
            'totalSantri',
            'santriOverLimit',
            'totalIzinPeriodeIni'
        ));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'kuota_maksimal' => 'required|integer|min:1|max:365',
            'periode_mulai' => 'required|date',
            'periode_akhir' => 'required|date|after:periode_mulai',
        ], [
            'kuota_maksimal.required' => 'Kuota maksimal wajib diisi.',
            'kuota_maksimal.min' => 'Kuota minimal 1 hari.',
            'kuota_maksimal.max' => 'Kuota maksimal 365 hari.',
            'periode_mulai.required' => 'Periode mulai wajib diisi.',
            'periode_akhir.required' => 'Periode akhir wajib diisi.',
            'periode_akhir.after' => 'Periode akhir harus setelah periode mulai.',
        ]);

        Kepulangan::updateSettings(
            $validated['kuota_maksimal'],
            $validated['periode_mulai'],
            $validated['periode_akhir']
        );

        return redirect()->route('admin.kepulangan.settings')
            ->with('success', 'Pengaturan kuota berhasil diperbarui.');
    }

    /**
     * Reset kuota satu santri
     */
    public function resetKuotaSantri(Request $request, $idSantri)
    {
        $validated = $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        $santri = Santri::where('id_santri', $idSantri)->firstOrFail();

        $result = Kepulangan::resetKuotaSantri(
            $idSantri,
            Auth::user()->name,
            $validated['catatan'] ?? 'Reset kuota individual untuk ' . $santri->nama_lengkap
        );

        return response()->json([
            'success' => true,
            'message' => 'Kuota santri ' . $santri->nama_lengkap . ' berhasil direset. Total ' . $result['total_hari_direset'] . ' hari telah direset.'
        ]);
    }

    /**
     * Reset kuota semua santri
     */
    public function resetKuotaSemuaSantri(Request $request)
    {
        $validated = $request->validate([
            'catatan' => 'nullable|string|max:500',
            'konfirmasi' => 'required|accepted',
        ], [
            'konfirmasi.accepted' => 'Anda harus mencentang konfirmasi untuk melanjutkan reset massal.',
        ]);

        $result = Kepulangan::resetKuotaSemuaSantri(
            Auth::user()->name,
            $validated['catatan'] ?? 'Reset kuota tahunan massal'
        );

        return response()->json([
            'success' => true,
            'message' => 'Kuota berhasil direset untuk ' . $result['total_santri'] . ' santri. Total ' . $result['total_hari_direset'] . ' hari telah direset.',
            'data' => $result
        ]);
    }

    /**
     * Show list santri over limit
     */
    public function santriOverLimit()
    {
        $settings = Kepulangan::getSettings();
        $santriOverLimitIds = Kepulangan::getSantriOverLimit();
        
        $santriList = Santri::whereIn('id_santri', array_keys($santriOverLimitIds))
            ->with(['kepulangan' => function($query) use ($settings) {
                $query->whereBetween('tanggal_pulang', [
                    $settings->periode_mulai,
                    $settings->periode_akhir
                ])->whereIn('status', ['Disetujui', 'Selesai']);
            }])
            ->get()
            ->map(function($santri) use ($santriOverLimitIds) {
                $kuota = Kepulangan::getSisaKuotaSantri($santri->id_santri);
                $santri->total_hari_izin = $santriOverLimitIds[$santri->id_santri];
                $santri->kuota_info = $kuota;
                return $santri;
            })
            ->sortByDesc('total_hari_izin');

        return view('admin.kepulangan.over-limit', compact('santriList', 'settings'));
    }

    /**
     * Helper: Get detail izin santri
     */
    private function getDetailIzinSantri($idSantri, $periodeMulai, $periodeAkhir)
    {
        $kepulanganList = Kepulangan::where('id_santri', $idSantri)
            ->whereIn('status', ['Disetujui', 'Selesai'])
            ->whereBetween('tanggal_pulang', [$periodeMulai, $periodeAkhir])
            ->orderBy('tanggal_pulang', 'desc')
            ->get();

        $settings = Kepulangan::getSettings();
        $totalHari = $kepulanganList->sum('durasi_izin');
        $totalIzin = $kepulanganList->count();
        $sisaKuota = max(0, $settings->kuota_maksimal - $totalHari);
        $overLimit = $totalHari > $settings->kuota_maksimal;

        $details = $kepulanganList->map(function($item) {
            return [
                'id' => $item->id_kepulangan,
                'tanggal' => $item->tanggal_pulang_formatted . ' - ' . $item->tanggal_kembali_formatted,
                'durasi' => $item->durasi_izin,
                'alasan' => $item->alasan,
                'status' => $item->status,
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
}