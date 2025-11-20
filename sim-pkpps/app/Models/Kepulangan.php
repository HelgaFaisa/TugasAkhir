<?php
// app/Models/Kepulangan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method untuk auto-generate ID Kepulangan
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_kepulangan)) {
                $last = Kepulangan::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_kepulangan, 2)) + 1 : 1;
                $model->id_kepulangan = 'KP' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }

            // Auto-calculate durasi_izin
            if ($model->tanggal_pulang && $model->tanggal_kembali) {
                $pulang = Carbon::parse($model->tanggal_pulang);
                $kembali = Carbon::parse($model->tanggal_kembali);
                $model->durasi_izin = $pulang->diffInDays($kembali) + 1;
            }

            // Set tanggal_izin ke hari ini jika tidak diisi
            if (empty($model->tanggal_izin)) {
                $model->tanggal_izin = now();
            }
        });

        static::updating(function ($model) {
            // Recalculate durasi_izin saat update
            if ($model->isDirty(['tanggal_pulang', 'tanggal_kembali'])) {
                $pulang = Carbon::parse($model->tanggal_pulang);
                $kembali = Carbon::parse($model->tanggal_kembali);
                $model->durasi_izin = $pulang->diffInDays($kembali) + 1;
            }
        });
    }

    /**
     * Relasi ke Santri
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }

    /**
     * Accessor: Format tanggal izin
     */
    public function getTanggalIzinFormattedAttribute()
    {
        return $this->tanggal_izin ? $this->tanggal_izin->format('d F Y') : '-';
    }

    /**
     * Accessor: Format tanggal pulang
     */
    public function getTanggalPulangFormattedAttribute()
    {
        return $this->tanggal_pulang ? $this->tanggal_pulang->format('d F Y') : '-';
    }

    /**
     * Accessor: Format tanggal kembali
     */
    public function getTanggalKembaliFormattedAttribute()
    {
        return $this->tanggal_kembali ? $this->tanggal_kembali->format('d F Y') : '-';
    }

    /**
     * Accessor: Format approved_at
     */
    public function getApprovedAtFormattedAttribute()
    {
        return $this->approved_at ? $this->approved_at->format('d F Y H:i') : '-';
    }

    /**
     * Accessor: Durasi izin calculated
     */
    public function getDurasiIzinCalculatedAttribute()
    {
        if ($this->tanggal_pulang && $this->tanggal_kembali) {
            $pulang = Carbon::parse($this->tanggal_pulang);
            $kembali = Carbon::parse($this->tanggal_kembali);
            return $pulang->diffInDays($kembali) + 1;
        }
        return $this->durasi_izin ?? 0;
    }

    /**
     * Accessor: Status badge HTML
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
     * Scope: Filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter berdasarkan santri
     */
    public function scopeSantri($query, $idSantri)
    {
        return $query->where('id_santri', $idSantri);
    }

    /**
     * Scope: Kepulangan yang sedang aktif
     */
    public function scopeAktif($query)
    {
        $today = Carbon::today();
        return $query->where('status', 'Disetujui')
            ->whereDate('tanggal_pulang', '<=', $today)
            ->whereDate('tanggal_kembali', '>=', $today);
    }

    /**
     * Scope: Kepulangan yang terlambat
     */
    public function scopeTerlambat($query)
    {
        return $query->where('status', 'Disetujui')
            ->whereDate('tanggal_kembali', '<', Carbon::today());
    }

    /**
     * Scope: Search kepulangan
     */
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
     * Static method: Get total hari izin per santri per tahun
     */
    public static function getTotalHariIzinSantri($idSantri, $tahun = null)
    {
        $tahun = $tahun ?? Carbon::now()->year;
        
        return self::where('id_santri', $idSantri)
            ->where('status', 'Disetujui')
            ->whereYear('tanggal_pulang', $tahun)
            ->sum('durasi_izin');
    }

    /**
     * Static method: Check apakah santri over limit (lebih dari 12 hari/tahun)
     */
    public static function isOverLimit($idSantri, $tahun = null)
    {
        $totalHari = self::getTotalHariIzinSantri($idSantri, $tahun);
        return $totalHari > 12;
    }

    /**
     * Static method: Get sisa kuota izin santri
     */
    public static function getSisaKuota($idSantri, $tahun = null)
    {
        $totalHari = self::getTotalHariIzinSantri($idSantri, $tahun);
        return max(0, 12 - $totalHari);
    }
}