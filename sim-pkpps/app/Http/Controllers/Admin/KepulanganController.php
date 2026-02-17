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
     * PERBAIKAN: Hapus validasi minimal karakter
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'tanggal_pulang' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after:tanggal_pulang',
            'alasan' => 'required|string|max:500',
        ], [
            'id_santri.required' => 'Santri wajib dipilih.',
            'id_santri.exists' => 'Santri tidak ditemukan.',
            'tanggal_pulang.required' => 'Tanggal pulang wajib diisi.',
            'tanggal_pulang.after_or_equal' => 'Tanggal pulang tidak boleh kurang dari hari ini.',
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_kembali.after' => 'Tanggal kembali harus setelah tanggal pulang.',
            'alasan.required' => 'Alasan kepulangan wajib diisi.',
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
     * PERBAIKAN: Hapus validasi minimal karakter
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
            'alasan' => 'required|string|max:500',
        ], [
            'tanggal_pulang.required' => 'Tanggal pulang wajib diisi.',
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_kembali.after' => 'Tanggal kembali harus setelah tanggal pulang.',
            'alasan.required' => 'Alasan kepulangan wajib diisi.',
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
     * PERBAIKAN: Bisa hapus data Selesai juga (untuk data lama)
     */
    public function destroy($id_kepulangan)
    {
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)->firstOrFail();
        
        // PERBAIKAN: Bisa hapus Menunggu, Ditolak, atau Selesai
        if (!in_array($kepulangan->status, ['Menunggu', 'Ditolak', 'Selesai'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya izin dengan status "Menunggu", "Ditolak", atau "Selesai" yang bisa dihapus.'
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
            'alasan_penolakan' => 'required|string',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
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
     * Complete kepulangan dengan input tanggal kembali aktual
     */
    public function complete(Request $request, $id_kepulangan)
    {
        $kepulangan = Kepulangan::where('id_kepulangan', $id_kepulangan)->firstOrFail();
        
        if ($kepulangan->status !== 'Disetujui') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya izin yang disetujui yang bisa diselesaikan.'
            ], 400);
        }

        // Validasi tanggal kembali aktual
        $validated = $request->validate([
            'tanggal_kembali_aktual' => 'required|date',
        ], [
            'tanggal_kembali_aktual.required' => 'Tanggal kembali aktual wajib diisi.',
            'tanggal_kembali_aktual.date' => 'Format tanggal tidak valid.',
        ]);

        // Validasi manual: tanggal kembali tidak boleh sebelum tanggal pulang
        $tanggalKembaliAktual = Carbon::parse($validated['tanggal_kembali_aktual']);
        if ($tanggalKembaliAktual->lt($kepulangan->tanggal_pulang)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal kembali aktual tidak boleh sebelum tanggal pulang (' . $kepulangan->tanggal_pulang->format('d M Y') . ').'
            ], 400);
        }

        // Simpan durasi rencana untuk perbandingan
        $durasiRencana = $kepulangan->durasi_izin;
        $tanggalKembaliRencana = $kepulangan->tanggal_kembali->format('Y-m-d');

        // Update tanggal_kembali dengan tanggal aktual
        // Durasi_izin akan otomatis recalculate di model (via updating event)
        $kepulangan->update([
            'tanggal_kembali' => $validated['tanggal_kembali_aktual'],
            'status' => 'Selesai'
        ]);

        // Refresh untuk mendapat durasi yang sudah dihitung ulang
        $kepulangan->refresh();
        $durasiAktual = $kepulangan->durasi_izin;

        // Buat pesan informatif
        $message = 'Kepulangan santri berhasil diselesaikan.';
        
        if ($durasiAktual < $durasiRencana) {
            $selisih = $durasiRencana - $durasiAktual;
            $message .= " Santri pulang {$selisih} hari lebih cepat dari rencana (Rencana: {$durasiRencana} hari, Aktual: {$durasiAktual} hari). Kuota telah disesuaikan.";
        } elseif ($durasiAktual > $durasiRencana) {
            $selisih = $durasiAktual - $durasiRencana;
            $message .= " Santri pulang {$selisih} hari lebih lambat dari rencana (Rencana: {$durasiRencana} hari, Aktual: {$durasiAktual} hari). Kuota telah disesuaikan.";
        } else {
            $message .= " Santri pulang sesuai rencana ({$durasiAktual} hari).";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'durasi_rencana' => $durasiRencana,
                'durasi_aktual' => $durasiAktual,
                'tanggal_kembali_rencana' => $tanggalKembaliRencana,
                'tanggal_kembali_aktual' => $validated['tanggal_kembali_aktual'],
            ]
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
     * PERBAIKAN: Return JSON yang benar, tidak ada HTML error
     */
    public function getSantriData($idSantri)
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
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

    /**
     * ========================================
     * PENGAJUAN DARI MOBILE
     * ========================================
     */

    /**
     * Tampilkan daftar pengajuan kepulangan dari mobile
     */
    public function pengajuan(Request $request)
    {
        $query = \App\Models\PengajuanKepulangan::with('santri');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id_pengajuan', 'like', "%{$search}%")
                  ->orWhere('alasan', 'like', "%{$search}%")
                  ->orWhereHas('santri', function($q2) use ($search) {
                      $q2->where('nama_lengkap', 'like', "%{$search}%")
                         ->orWhere('id_santri', 'like', "%{$search}%");
                  });
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Get data dengan pagination
        $pengajuan = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total_data' => \App\Models\PengajuanKepulangan::count(),
            'menunggu' => \App\Models\PengajuanKepulangan::where('status', 'Menunggu')->count(),
            'disetujui' => \App\Models\PengajuanKepulangan::where('status', 'Disetujui')->count(),
            'ditolak' => \App\Models\PengajuanKepulangan::where('status', 'Ditolak')->count(),
        ];

        return view('admin.kepulangan.pengajuan', compact('pengajuan', 'stats'));
    }

    /**
     * Approve pengajuan kepulangan
     */
    public function approvePengajuan(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'catatan_review' => 'nullable|string|max:500',
            ]);

            $pengajuan = \App\Models\PengajuanKepulangan::findOrFail($id);

            // Cegah review ulang
            if ($pengajuan->status !== 'Menunggu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan sudah direview sebelumnya'
                ], 400);
            }

            // Simpan ID pengajuan untuk catatan sebelum dihapus
            $id_pengajuan = $pengajuan->id_pengajuan;

            // Pindahkan ke tabel kepulangan
            $kepulangan = Kepulangan::create([
                'id_santri' => $pengajuan->id_santri,
                'tanggal_pulang' => $pengajuan->tanggal_pulang,
                'tanggal_kembali' => $pengajuan->tanggal_kembali,
                'durasi_izin' => $pengajuan->durasi_izin,
                'alasan' => $pengajuan->alasan,
                'status' => 'Disetujui',
                'catatan' => 'Disetujui dari pengajuan mobile: ' . $id_pengajuan . ($validated['catatan_review'] ? ' - ' . $validated['catatan_review'] : ''),
                'approved_by' => Auth::user()->name,
                'approved_at' => now(),
            ]);

            // Hapus dari tabel pengajuan setelah dipindahkan
            $pengajuan->delete();

            // TODO: Kirim notifikasi FCM ke mobile
            // $this->sendNotification($pengajuan->id_santri, 'approved');

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil disetujui dan ditambahkan ke data kepulangan',
                'kepulangan_id' => $kepulangan->id_kepulangan,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject pengajuan kepulangan
     */
    public function rejectPengajuan(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'catatan_review' => 'required|string|max:500',
            ], [
                'catatan_review.required' => 'Catatan penolakan wajib diisi',
            ]);

            $pengajuan = \App\Models\PengajuanKepulangan::findOrFail($id);

            // Cegah review ulang
            if ($pengajuan->status !== 'Menunggu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan sudah direview sebelumnya'
                ], 400);
            }

            // Simpan data untuk notifikasi sebelum dihapus
            $id_santri = $pengajuan->id_santri;
            $catatan = $validated['catatan_review'];

            // Hapus pengajuan yang ditolak
            $pengajuan->delete();

            // TODO: Kirim notifikasi FCM ke mobile
            // $this->sendNotification($id_santri, 'rejected', $catatan);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil ditolak dan dihapus dari daftar'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}