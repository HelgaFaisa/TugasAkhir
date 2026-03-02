<?php
// app/Http/Controllers/Api/ApiAbsensiKegiatanController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKegiatan;
use App\Models\Kegiatan;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiAbsensiKegiatanController extends Controller
{
    /**
     * ==========================================
     * 1. DASHBOARD HARI INI (Summary + Timeline)
     * ==========================================
     */
    public function today(Request $request)
    {
        try {
            $user = $request->user();
            $idSantri = $user->id_santri; // id_santri dari santri_accounts
            
            $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
            $selectedDate = Carbon::parse($tanggal);
            
            // Summary Hari Ini
            $summary = AbsensiKegiatan::where('id_santri', $idSantri)
                ->whereDate('tanggal', $selectedDate)
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status = "Hadir" THEN 1 ELSE 0 END) as hadir'),
                    DB::raw('SUM(CASE WHEN status = "Izin" THEN 1 ELSE 0 END) as izin'),
                    DB::raw('SUM(CASE WHEN status = "Sakit" THEN 1 ELSE 0 END) as sakit'),
                    DB::raw('SUM(CASE WHEN status = "Alpa" THEN 1 ELSE 0 END) as alpa')
                )
                ->first();
            
            $percentage = $summary->total > 0 
                ? round(($summary->hadir / $summary->total) * 100, 1) 
                : 0;
            
            // Timeline Absensi Hari Ini
            $timeline = AbsensiKegiatan::with(['kegiatan.kategori'])
                ->where('id_santri', $idSantri)
                ->whereDate('tanggal', $selectedDate)
                ->orderBy('waktu_absen')
                ->get()
                ->map(function($absensi) use ($selectedDate) {
                    $kegiatan = $absensi->kegiatan;
                    
                    // Calculate punctuality (jika RFID)
                    $punctuality = null;
                    if ($absensi->metode_absen === 'RFID' && $absensi->status === 'Hadir') {
                        $waktuMulai = Carbon::parse($selectedDate->format('Y-m-d') . ' ' . $kegiatan->waktu_mulai);
                        $waktuAbsen = Carbon::parse($absensi->waktu_absen);
                        $diffMinutes = $waktuAbsen->diffInMinutes($waktuMulai, false);
                        
                        if ($diffMinutes <= 0) {
                            $punctuality = 'Tepat Waktu';
                        } else {
                            $punctuality = 'Telat ' . abs($diffMinutes) . ' menit';
                        }
                    }
                    
                    return [
                        'absensi_id' => $absensi->absensi_id,
                        'kegiatan_id' => $kegiatan->kegiatan_id,
                        'nama_kegiatan' => $kegiatan->nama_kegiatan,
                        'kategori' => [
                            'nama' => $kegiatan->kategori->nama_kategori,
                            'icon' => $kegiatan->kategori->icon ?? 'fa-calendar',
                            'warna' => $kegiatan->kategori->warna ?? '#6FBAA5',
                        ],
                        'waktu_mulai' => date('H:i', strtotime($kegiatan->waktu_mulai)),
                        'waktu_selesai' => date('H:i', strtotime($kegiatan->waktu_selesai)),
                        'status' => $absensi->status,
                        'waktu_absen' => $absensi->waktu_absen ? date('H:i', strtotime($absensi->waktu_absen)) : null,
                        'metode_absen' => $absensi->metode_absen,
                        'punctuality' => $punctuality,
                        'keterangan' => $absensi->keterangan,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'tanggal' => $selectedDate->locale('id')->isoFormat('dddd, D MMMM YYYY'),
                    'tanggal_raw' => $selectedDate->format('Y-m-d'),
                    'summary' => [
                        'total' => $summary->total,
                        'hadir' => $summary->hadir,
                        'izin' => $summary->izin,
                        'sakit' => $summary->sakit,
                        'alpa' => $summary->alpa,
                        'percentage' => $percentage,
                    ],
                    'timeline' => $timeline,
                ],
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * ==========================================
     * 2. SUMMARY MINGGU INI
     * ==========================================
     */
    public function week(Request $request)
    {
        try {
            $user = $request->user();
            $idSantri = $user->id_santri;
            
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
            
            // Summary
            $summary = AbsensiKegiatan::where('id_santri', $idSantri)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status = "Hadir" THEN 1 ELSE 0 END) as hadir'),
                    DB::raw('SUM(CASE WHEN status = "Izin" THEN 1 ELSE 0 END) as izin'),
                    DB::raw('SUM(CASE WHEN status = "Sakit" THEN 1 ELSE 0 END) as sakit'),
                    DB::raw('SUM(CASE WHEN status = "Alpa" THEN 1 ELSE 0 END) as alpa')
                )
                ->first();
            
            $percentage = $summary->total > 0 
                ? round(($summary->hadir / $summary->total) * 100, 1) 
                : 0;
            
            // Trend 7 hari
            $trend = [];
            for ($i = 0; $i < 7; $i++) {
                $date = $startDate->copy()->addDays($i);
                
                $dayData = AbsensiKegiatan::where('id_santri', $idSantri)
                    ->whereDate('tanggal', $date)
                    ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = "Hadir" THEN 1 ELSE 0 END) as hadir')
                    ->first();
                
                $trend[] = [
                    'date' => $date->format('Y-m-d'),
                    'day_name' => $date->locale('id')->isoFormat('ddd'),
                    'percentage' => $dayData->total > 0 
                        ? round(($dayData->hadir / $dayData->total) * 100, 1) 
                        : 0,
                ];
            }
            
            // Breakdown per kategori
            $perKategori = AbsensiKegiatan::where('id_santri', $idSantri)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->join('kegiatans', 'absensi_kegiatans.kegiatan_id', '=', 'kegiatans.kegiatan_id')
                ->join('kategori_kegiatans', 'kegiatans.kategori_id', '=', 'kategori_kegiatans.kategori_id')
                ->select(
                    'kategori_kegiatans.nama_kategori',
                    'kategori_kegiatans.warna',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN absensi_kegiatans.status = "Hadir" THEN 1 ELSE 0 END) as hadir')
                )
                ->groupBy('kategori_kegiatans.kategori_id', 'kategori_kegiatans.nama_kategori', 'kategori_kegiatans.warna')
                ->get()
                ->map(function($item) {
                    return [
                        'nama_kategori' => $item->nama_kategori,
                        'warna' => $item->warna ?? '#6FBAA5',
                        'total' => $item->total,
                        'hadir' => $item->hadir,
                        'percentage' => $item->total > 0 
                            ? round(($item->hadir / $item->total) * 100, 1) 
                            : 0,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'periode' => $startDate->locale('id')->isoFormat('D MMM') . ' - ' . $endDate->locale('id')->isoFormat('D MMM Y'),
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'summary' => [
                        'total' => $summary->total,
                        'hadir' => $summary->hadir,
                        'izin' => $summary->izin,
                        'sakit' => $summary->sakit,
                        'alpa' => $summary->alpa,
                        'percentage' => $percentage,
                    ],
                    'trend' => $trend,
                    'per_kategori' => $perKategori,
                ],
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * ==========================================
     * 3. RIWAYAT BULAN (dengan Pagination)
     * ==========================================
     */
    public function month(Request $request)
    {
        try {
            $user = $request->user();
            $idSantri = $user->id_santri;
            
            $bulan = $request->get('bulan', now()->format('Y-m'));
            $date = Carbon::parse($bulan . '-01');
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            
            // Summary
            $summary = AbsensiKegiatan::where('id_santri', $idSantri)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status = "Hadir" THEN 1 ELSE 0 END) as hadir'),
                    DB::raw('SUM(CASE WHEN status = "Izin" THEN 1 ELSE 0 END) as izin'),
                    DB::raw('SUM(CASE WHEN status = "Sakit" THEN 1 ELSE 0 END) as sakit'),
                    DB::raw('SUM(CASE WHEN status = "Alpa" THEN 1 ELSE 0 END) as alpa')
                )
                ->first();
            
            $percentage = $summary->total > 0 
                ? round(($summary->hadir / $summary->total) * 100, 1) 
                : 0;
            
            // Riwayat per hari (grouped)
            $riwayat = AbsensiKegiatan::with(['kegiatan.kategori'])
                ->where('id_santri', $idSantri)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->orderByDesc('tanggal')
                ->orderBy('waktu_absen')
                ->get()
                ->groupBy(function($item) {
                    return Carbon::parse($item->tanggal)->format('Y-m-d');
                })
                ->map(function($items, $date) {
                    $hadir = $items->where('status', 'Hadir')->count();
                    $total = $items->count();
                    
                    return [
                        'tanggal' => Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM Y'),
                        'tanggal_raw' => $date,
                        'total' => $total,
                        'hadir' => $hadir,
                        'percentage' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
                        'items' => $items->map(function($absensi) {
                            return [
                                'kegiatan' => $absensi->kegiatan->nama_kegiatan,
                                'kategori' => $absensi->kegiatan->kategori->nama_kategori,
                                'status' => $absensi->status,
                                'waktu_absen' => $absensi->waktu_absen ? date('H:i', strtotime($absensi->waktu_absen)) : null,
                            ];
                        })->values(),
                    ];
                })
                ->values();
            
            // Heatmap Calendar (30 hari)
            $heatmap = $this->generateHeatmap($idSantri, $startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'periode' => $date->locale('id')->isoFormat('MMMM YYYY'),
                    'bulan_raw' => $date->format('Y-m'),
                    'summary' => [
                        'total' => $summary->total,
                        'hadir' => $summary->hadir,
                        'izin' => $summary->izin,
                        'sakit' => $summary->sakit,
                        'alpa' => $summary->alpa,
                        'percentage' => $percentage,
                    ],
                    'heatmap' => $heatmap,
                    'riwayat' => $riwayat,
                ],
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * ==========================================
     * HELPER: Generate Heatmap Data
     * ==========================================
     */
    private function generateHeatmap($idSantri, $startDate, $endDate)
    {
        $heatmap = [];
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            $dayData = AbsensiKegiatan::where('id_santri', $idSantri)
                ->whereDate('tanggal', $current)
                ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = "Hadir" THEN 1 ELSE 0 END) as hadir')
                ->first();
            
            $percentage = $dayData->total > 0 
                ? round(($dayData->hadir / $dayData->total) * 100, 1) 
                : 0;
            
            $level = $this->getHeatmapLevel($percentage);
            
            $heatmap[] = [
                'date' => $current->format('Y-m-d'),
                'day' => $current->format('j'),
                'day_name' => $current->locale('id')->isoFormat('dd'),
                'percentage' => $percentage,
                'level' => $level,
                'is_today' => $current->isToday(),
            ];
            
            $current->addDay();
        }
        
        return $heatmap;
    }
    
    /**
     * Get Heatmap Level (0-4)
     */
    private function getHeatmapLevel($percentage)
    {
        if ($percentage >= 90) return 4; // Dark green
        if ($percentage >= 80) return 3; // Green
        if ($percentage >= 70) return 2; // Yellow
        if ($percentage > 0) return 1;  // Red
        return 0; // No data
    }
}