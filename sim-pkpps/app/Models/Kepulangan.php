<?php
// app/Models/Kepulangan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Kepulangan extends Model
{
    use HasFactory;

    protected $table = 'kepulangan';

    protected $fillable = [
        'id_kepulangan',
        'id_santri',
        'tanggal_izin',
        'tanggal_pulang',
        'tanggal_kembali',
        'durasi_izin',
        'alasan',
        'status',
        'approved_by',
        'approved_at',
        'catatan',
    ];

    protected $casts = [
        'tanggal_izin' => 'date',
        'tanggal_pulang' => 'date',
        'tanggal_kembali' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Boot method - Auto generate ID & calculate durasi
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate ID Kepulangan
            if (empty($model->id_kepulangan)) {
                $last = Kepulangan::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_kepulangan, 2)) + 1 : 1;
                $model->id_kepulangan = 'KP' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }

            // Hitung durasi_izin otomatis
            if ($model->tanggal_pulang && $model->tanggal_kembali) {
                $model->durasi_izin = $model->hitungDurasiIzin(
                    $model->tanggal_pulang,
                    $model->tanggal_kembali
                );
            }

            // Set tanggal_izin jika kosong
            if (empty($model->tanggal_izin)) {
                $model->tanggal_izin = now();
            }
        });

        static::updating(function ($model) {
            // Recalculate durasi_izin saat update tanggal
            if ($model->isDirty(['tanggal_pulang', 'tanggal_kembali'])) {
                $model->durasi_izin = $model->hitungDurasiIzin(
                    $model->tanggal_pulang,
                    $model->tanggal_kembali
                );
            }
        });
    }

    /**
     * Method untuk menghitung durasi izin dalam hari
     * Formula: (tanggal_kembali - tanggal_pulang) + 1
     */
    private function hitungDurasiIzin($tanggalPulang, $tanggalKembali)
    {
        $pulang = Carbon::parse($tanggalPulang);
        $kembali = Carbon::parse($tanggalKembali);
        
        // +1 karena hari pertama juga dihitung
        return $pulang->diffInDays($kembali) + 1;
    }

    /**
     * Relasi ke Santri
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }

    /**
     * Accessor: Format tanggal
     */
    public function getTanggalIzinFormattedAttribute()
    {
        return $this->tanggal_izin ? $this->tanggal_izin->format('d F Y') : '-';
    }

    public function getTanggalPulangFormattedAttribute()
    {
        return $this->tanggal_pulang ? $this->tanggal_pulang->format('d F Y') : '-';
    }

    public function getTanggalKembaliFormattedAttribute()
    {
        return $this->tanggal_kembali ? $this->tanggal_kembali->format('d F Y') : '-';
    }

    public function getApprovedAtFormattedAttribute()
    {
        return $this->approved_at ? $this->approved_at->format('d F Y H:i') : '-';
    }

    /**
     * Accessor: Status badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Menunggu' => 'badge-warning',
            'Disetujui' => 'badge-success',
            'Ditolak' => 'badge-danger',
            'Selesai' => 'badge-secondary',
        ];
        return $badges[$this->status] ?? 'badge-secondary';
    }

    /**
     * Accessor: Apakah sedang dalam periode izin
     */
    public function getIsAktifAttribute()
    {
        $today = Carbon::today();
        return $this->status === 'Disetujui' 
            && $today->between($this->tanggal_pulang, $this->tanggal_kembali);
    }

    /**
     * Accessor: Apakah terlambat kembali
     */
    public function getIsTerlambatAttribute()
    {
        if ($this->status !== 'Disetujui') {
            return false;
        }
        return Carbon::today()->greaterThan($this->tanggal_kembali);
    }

    /**
     * Scopes
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSantri($query, $idSantri)
    {
        return $query->where('id_santri', $idSantri);
    }

    public function scopeAktif($query)
    {
        $today = Carbon::today();
        return $query->where('status', 'Disetujui')
            ->whereDate('tanggal_pulang', '<=', $today)
            ->whereDate('tanggal_kembali', '>=', $today);
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status', 'Disetujui')
            ->whereDate('tanggal_kembali', '<', Carbon::today());
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('id_kepulangan', 'like', "%{$search}%")
              ->orWhere('alasan', 'like', "%{$search}%")
              ->orWhereHas('santri', function($sq) use ($search) {
                  $sq->where('nama_lengkap', 'like', "%{$search}%")
                     ->orWhere('id_santri', 'like', "%{$search}%")
                     ->orWhere('nis', 'like', "%{$search}%");
              });
        });
    }

    /**
     * ========================================
     * FITUR KUOTA TAHUNAN (DIPERBAIKI)
     * ========================================
     */

    /**
     * Get settings kepulangan (kuota, periode)
     */
    public static function getSettings()
    {
        $settings = DB::table('kepulangan_settings')->latest()->first();
        
        if (!$settings) {
            // Create default settings
            DB::table('kepulangan_settings')->insert([
                'kuota_maksimal' => 12,
                'periode_mulai' => now()->startOfYear()->format('Y-m-d'),
                'periode_akhir' => now()->endOfYear()->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $settings = DB::table('kepulangan_settings')->latest()->first();
        }
        
        return (object) [
            'id' => $settings->id,
            'kuota_maksimal' => $settings->kuota_maksimal,
            'periode_mulai' => Carbon::parse($settings->periode_mulai),
            'periode_akhir' => Carbon::parse($settings->periode_akhir),
            'terakhir_reset' => $settings->terakhir_reset ? Carbon::parse($settings->terakhir_reset) : null,
            'reset_by' => $settings->reset_by,
        ];
    }

    /**
     * Update settings kepulangan
     */
    public static function updateSettings($kuotaMaksimal, $periodeMulai, $periodeAkhir)
    {
        $existing = DB::table('kepulangan_settings')->latest()->first();
        
        if ($existing) {
            DB::table('kepulangan_settings')
                ->where('id', $existing->id)
                ->update([
                    'kuota_maksimal' => $kuotaMaksimal,
                    'periode_mulai' => $periodeMulai,
                    'periode_akhir' => $periodeAkhir,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('kepulangan_settings')->insert([
                'kuota_maksimal' => $kuotaMaksimal,
                'periode_mulai' => $periodeMulai,
                'periode_akhir' => $periodeAkhir,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * PERBAIKAN UTAMA: Get total hari izin santri dalam periode tertentu
     * HANYA menghitung yang Disetujui & Selesai
     * AKUMULASI durasi_izin (HARI), bukan COUNT jumlah pengajuan
     */
    public static function getTotalHariIzinSantri($idSantri, $periodeMulai = null, $periodeAkhir = null)
    {
        if (!$periodeMulai || !$periodeAkhir) {
            $settings = self::getSettings();
            $periodeMulai = $settings->periode_mulai;
            $periodeAkhir = $settings->periode_akhir;
        }

        // PERBAIKAN: SUM durasi_izin (hari), bukan COUNT
        return self::where('id_santri', $idSantri)
            ->whereIn('status', ['Disetujui', 'Selesai']) // Hanya yang approved/selesai
            ->whereBetween('tanggal_pulang', [$periodeMulai, $periodeAkhir])
            ->sum('durasi_izin'); // Akumulasi HARI
    }

    /**
     * PERBAIKAN: Get detail kuota santri
     * Status MELEBIHI tetap dihitung (tidak direset ke 0)
     */
    public static function getSisaKuotaSantri($idSantri)
    {
        $settings = self::getSettings();
        
        $totalTerpakai = self::getTotalHariIzinSantri(
            $idSantri,
            $settings->periode_mulai,
            $settings->periode_akhir
        );

        // PERBAIKAN: Bisa negatif jika over limit
        $sisaKuota = $settings->kuota_maksimal - $totalTerpakai;
        $persentase = $settings->kuota_maksimal > 0 ? 
            ($totalTerpakai / $settings->kuota_maksimal) * 100 : 0;

        // Tentukan status berdasarkan persentase
        $status = 'aman'; // 0-80%
        if ($persentase >= 80 && $persentase < 100) {
            $status = 'hampir_habis'; // 80-100%
        } elseif ($persentase >= 100) {
            $status = 'melebihi'; // >100%
        }

        // Warna badge
        $badgeColor = 'success'; // Hijau
        if ($persentase >= 80 && $persentase < 100) {
            $badgeColor = 'warning'; // Kuning
        } elseif ($persentase >= 100) {
            $badgeColor = 'danger'; // Merah
        }

        return [
            'kuota_maksimal' => $settings->kuota_maksimal,
            'total_terpakai' => $totalTerpakai, // Bisa > kuota_maksimal
            'sisa_kuota' => max(0, $sisaKuota), // Tampilkan 0 jika negatif (untuk UI)
            'sisa_kuota_real' => $sisaKuota, // Nilai asli (bisa negatif)
            'persentase' => round($persentase, 1),
            'status' => $status,
            'badge_color' => $badgeColor,
            'periode_mulai' => $settings->periode_mulai->format('d M Y'),
            'periode_akhir' => $settings->periode_akhir->format('d M Y'),
            'terakhir_reset' => $settings->terakhir_reset ? 
                $settings->terakhir_reset->format('d M Y') : '-',
        ];
    }

    /**
     * Check apakah santri over limit
     */
    public static function isOverLimit($idSantri)
    {
        $kuota = self::getSisaKuotaSantri($idSantri);
        return $kuota['status'] === 'melebihi';
    }

    /**
     * PERBAIKAN: Get list santri yang over limit
     * Return array: [id_santri => total_hari_terpakai]
     */
    public static function getSantriOverLimit()
    {
        $settings = self::getSettings();
        
        // Ambil semua santri aktif
        $santriIds = Santri::where('status', 'Aktif')->pluck('id_santri');
        $overLimitList = [];
        
        foreach ($santriIds as $idSantri) {
            $totalHari = self::getTotalHariIzinSantri(
                $idSantri,
                $settings->periode_mulai,
                $settings->periode_akhir
            );
            
            // PERBAIKAN: Tampilkan total hari sebenarnya (tidak reset ke 0)
            if ($totalHari > $settings->kuota_maksimal) {
                $overLimitList[$idSantri] = $totalHari;
            }
        }
        
        return $overLimitList;
    }

    /**
     * ========================================
     * FITUR RESET KUOTA
     * ========================================
     */

    /**
     * Reset kuota untuk satu santri
     */
    public static function resetKuotaSantri($idSantri, $resetBy, $catatan = null)
    {
        $settings = self::getSettings();
        
        // Hitung total hari sebelum reset
        $totalHariSebelumReset = self::getTotalHariIzinSantri(
            $idSantri,
            $settings->periode_mulai,
            $settings->periode_akhir
        );

        // Catat log reset
        DB::table('kepulangan_reset_logs')->insert([
            'id_santri' => $idSantri,
            'total_hari_sebelum_reset' => $totalHariSebelumReset,
            'periode_mulai' => $settings->periode_mulai,
            'periode_akhir' => $settings->periode_akhir,
            'kuota_tahunan' => $settings->kuota_maksimal,
            'jenis_reset' => 'individual',
            'reset_by' => $resetBy,
            'catatan' => $catatan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update semua kepulangan santri yang Disetujui menjadi Selesai
        self::where('id_santri', $idSantri)
            ->where('status', 'Disetujui')
            ->whereBetween('tanggal_pulang', [$settings->periode_mulai, $settings->periode_akhir])
            ->update(['status' => 'Selesai']);

        return [
            'success' => true,
            'total_hari_direset' => $totalHariSebelumReset,
        ];
    }

    /**
     * Reset kuota untuk semua santri
     */
    public static function resetKuotaSemuaSantri($resetBy, $catatan = null)
    {
        $settings = self::getSettings();
        
        // Get semua santri aktif
        $santriIds = Santri::where('status', 'Aktif')->pluck('id_santri');
        $totalSantri = $santriIds->count();
        $totalHariDireset = 0;

        foreach ($santriIds as $idSantri) {
            $totalHari = self::getTotalHariIzinSantri(
                $idSantri,
                $settings->periode_mulai,
                $settings->periode_akhir
            );
            
            $totalHariDireset += $totalHari;
        }

        // Catat log reset massal
        DB::table('kepulangan_reset_logs')->insert([
            'id_santri' => null, // Null untuk reset massal
            'total_hari_sebelum_reset' => $totalHariDireset,
            'periode_mulai' => $settings->periode_mulai,
            'periode_akhir' => $settings->periode_akhir,
            'kuota_tahunan' => $settings->kuota_maksimal,
            'jenis_reset' => 'massal',
            'reset_by' => $resetBy,
            'catatan' => $catatan ?? "Reset massal untuk {$totalSantri} santri",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update semua kepulangan yang Disetujui menjadi Selesai
        $jumlahDiupdate = self::whereIn('id_santri', $santriIds)
            ->where('status', 'Disetujui')
            ->whereBetween('tanggal_pulang', [$settings->periode_mulai, $settings->periode_akhir])
            ->update(['status' => 'Selesai']);

        // Update tanggal terakhir reset di settings
        DB::table('kepulangan_settings')
            ->where('id', $settings->id)
            ->update([
                'terakhir_reset' => now(),
                'reset_by' => $resetBy,
                'updated_at' => now(),
            ]);

        return [
            'success' => true,
            'total_santri' => $totalSantri,
            'total_hari_direset' => $totalHariDireset,
            'jumlah_izin_diupdate' => $jumlahDiupdate,
        ];
    }

    /**
     * Get history reset logs
     */
    public static function getResetLogs($limit = 20)
    {
        return DB::table('kepulangan_reset_logs')
            ->leftJoin('santris', 'kepulangan_reset_logs.id_santri', '=', 'santris.id_santri')
            ->select(
                'kepulangan_reset_logs.*',
                'santris.nama_lengkap'
            )
            ->orderBy('kepulangan_reset_logs.created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}