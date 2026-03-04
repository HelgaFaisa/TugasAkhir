<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PembayaranSpp;
use App\Models\Santri;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ApiSppController extends Controller
{
    /**
     * Get status SPP bulan berjalan
     */
    public function statusBulanIni(Request $request)
    {
        try {
            $idSantri = $request->user()->id_santri;
            $bulanIni = date('n');
            $tahunIni = date('Y');

            $spp = PembayaranSpp::where('id_santri', $idSantri)
                ->where('bulan', $bulanIni)
                ->where('tahun', $tahunIni)
                ->first();

            if (!$spp) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'ada_tagihan' => false,
                        'status'      => 'Belum Ada Tagihan',
                        'periode'     => $this->getNamaBulan($bulanIni) . ' ' . $tahunIni,
                    ]
                ]);
            }

            // ── TAMBAHAN: data cicilan ──────────────────────────────
            $isCicilan       = $spp->isCicilan();
            $nominalTerbayar = (int) $spp->nominal_terbayar;   // accessor dari Model
            $nominalSisa     = (int) $spp->nominal_sisa;        // accessor dari Model
            $porsentase      = $spp->porsentase_cicilan;        // accessor dari Model
            // ───────────────────────────────────────────────────────

            return response()->json([
                'success' => true,
                'data' => [
                    'ada_tagihan'             => true,
                    'id_pembayaran'           => $spp->id_pembayaran,
                    'periode'                 => $this->getNamaBulan($spp->bulan) . ' ' . $spp->tahun,
                    'nominal'                 => (int) $spp->nominal,
                    'status'                  => $spp->status,
                    'tanggal_bayar'           => $spp->tanggal_bayar?->format('Y-m-d'),
                    'tanggal_bayar_formatted' => $spp->tanggal_bayar?->format('d M Y'),
                    'batas_bayar'             => $spp->batas_bayar->format('Y-m-d'),
                    'batas_bayar_formatted'   => $spp->batas_bayar->format('d M Y'),
                    'is_telat'                => $spp->isTelat(),
                    // ── field baru ──
                    'is_cicilan'              => $isCicilan,
                    'nominal_terbayar'        => $nominalTerbayar,
                    'nominal_sisa'            => $nominalSisa,
                    'porsentase_cicilan'      => $porsentase,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil status SPP: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get info tunggakan
     */
    public function tunggakan(Request $request)
    {
        try {
            $idSantri = $request->user()->id_santri;

            $tunggakanList = PembayaranSpp::where('id_santri', $idSantri)
                ->where('status', 'Belum Lunas')
                ->orderBy('tahun', 'asc')
                ->orderBy('bulan', 'asc')
                ->get();

            $totalTunggakan = $tunggakanList->sum('nominal');
            $jumlahBulan    = $tunggakanList->count();
            $adaTelat       = $tunggakanList->filter(fn($spp) => $spp->isTelat())->count() > 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'ada_tunggakan'   => $jumlahBulan > 0,
                    'total_tunggakan' => (int) $totalTunggakan,
                    'jumlah_bulan'    => $jumlahBulan,
                    'ada_telat'       => $adaTelat,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil tunggakan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get riwayat pembayaran SPP
     *
     * Query param ?status= bisa berisi:
     *   semua | Lunas | Belum Lunas | Cicilan
     */
    public function riwayat(Request $request)
    {
        try {
            $idSantri = $request->user()->id_santri;

            $query = PembayaranSpp::where('id_santri', $idSantri)
                ->select([
                    'id', 'id_pembayaran', 'bulan', 'tahun',
                    'nominal', 'status', 'tanggal_bayar',
                    'batas_bayar', 'keterangan',
                ])
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc');

            // ── REVISI: filter status termasuk "Cicilan" ───────────
            if ($request->filled('status') && $request->status !== 'semua') {
                if ($request->status === 'Cicilan') {
                    // Cicilan = Belum Lunas + keterangan JSON punya field "terbayar" > 0
                    $query->where('status', 'Belum Lunas')
                          ->where(function ($q) {
                              // JSON valid & mengandung "terbayar"
                              $q->whereRaw("JSON_VALID(keterangan) = 1")
                                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(keterangan, '$.terbayar')) > 0");
                          });
                } else {
                    $query->where('status', $request->status);
                }
            }
            // ───────────────────────────────────────────────────────

            $riwayat = $query->paginate(20);

            $data = $riwayat->map(function ($item) {
                // ── TAMBAHAN: data cicilan per item ─────────────────
                $isCicilan       = $item->isCicilan();
                $nominalTerbayar = (int) $item->nominal_terbayar;
                $nominalSisa     = (int) $item->nominal_sisa;
                $porsentase      = $item->porsentase_cicilan;
                // ────────────────────────────────────────────────────

                return [
                    'id'                      => $item->id,
                    'id_pembayaran'           => $item->id_pembayaran,
                    'periode'                 => $this->getNamaBulan($item->bulan) . ' ' . $item->tahun,
                    'bulan'                   => $item->bulan,
                    'tahun'                   => $item->tahun,
                    'bulan_nama'              => $this->getNamaBulan($item->bulan),
                    'nominal'                 => (int) $item->nominal,
                    'status'                  => $item->status,
                    'tanggal_bayar'           => $item->tanggal_bayar?->format('Y-m-d'),
                    'tanggal_bayar_formatted' => $item->tanggal_bayar?->format('d M Y'),
                    'batas_bayar'             => $item->batas_bayar->format('Y-m-d'),
                    'batas_bayar_formatted'   => $item->batas_bayar->format('d M Y'),
                    'is_telat'                => $item->isTelat(),
                    'keterangan'              => $item->keterangan,
                    // ── field baru ──
                    'is_cicilan'              => $isCicilan,
                    'nominal_terbayar'        => $nominalTerbayar,
                    'nominal_sisa'            => $nominalSisa,
                    'porsentase_cicilan'      => $porsentase,
                ];
            });

            return response()->json([
                'success'    => true,
                'data'       => $data,
                'pagination' => [
                    'current_page' => $riwayat->currentPage(),
                    'last_page'    => $riwayat->lastPage(),
                    'total'        => $riwayat->total(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statistik pembayaran SPP
     */
    public function statistik(Request $request)
    {
        try {
            $idSantri = $request->user()->id_santri;

            $semuaBelumLunas = PembayaranSpp::where('id_santri', $idSantri)
                ->where('status', 'Belum Lunas')
                ->get();

            $totalLunas = PembayaranSpp::where('id_santri', $idSantri)
                ->where('status', 'Lunas')
                ->count();

            // ── TAMBAHAN: pisahkan cicilan dari belum lunas ─────────
            $totalCicilan    = $semuaBelumLunas->filter(fn($s) => $s->isCicilan())->count();
            $totalBelumLunas = $semuaBelumLunas->filter(fn($s) => !$s->isCicilan())->count();
            // ────────────────────────────────────────────────────────

            $totalNominalLunas = PembayaranSpp::where('id_santri', $idSantri)
                ->where('status', 'Lunas')
                ->sum('nominal');

            $totalNominalBelumLunas = PembayaranSpp::where('id_santri', $idSantri)
                ->where('status', 'Belum Lunas')
                ->sum('nominal');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_lunas'               => $totalLunas,
                    'total_cicilan'             => $totalCicilan,       // ← baru
                    'total_belum_lunas'         => $totalBelumLunas,    // ← sekarang exclude cicilan
                    'total_nominal_lunas'       => (int) $totalNominalLunas,
                    'total_nominal_belum_lunas' => (int) $totalNominalBelumLunas,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper: Get nama bulan
     */
    private function getNamaBulan($bulan)
    {
        $namaBulan = [
            1 => 'Januari',  2 => 'Februari', 3 => 'Maret',
            4 => 'April',    5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',     8 => 'Agustus',   9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $namaBulan[$bulan] ?? '';
    }
}