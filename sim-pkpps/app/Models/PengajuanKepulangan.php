<?php
// app/Models/PengajuanKepulangan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PengajuanKepulangan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_kepulangan';

    protected $fillable = [
        'id_pengajuan',
        'id_santri',
        'tanggal_pulang',
        'tanggal_kembali',
        'durasi_izin',
        'alasan',
        'status',
        'catatan_review',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'tanggal_pulang' => 'date',
        'tanggal_kembali' => 'date',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Boot method - Auto generate ID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate ID Pengajuan (PGJ001, PGJ002, ...)
            if (empty($model->id_pengajuan)) {
                $last = PengajuanKepulangan::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_pengajuan, 3)) + 1 : 1;
                $model->id_pengajuan = 'PGJ' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }

            // Hitung durasi_izin otomatis jika belum diset
            if (empty($model->durasi_izin) && $model->tanggal_pulang && $model->tanggal_kembali) {
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
     * Relasi ke User (reviewer/admin)
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope: Filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by santri
     */
    public function scopeSantri($query, $idSantri)
    {
        return $query->where('id_santri', $idSantri);
    }

    /**
     * Accessor: Format tanggal
     */
    public function getTanggalPulangFormattedAttribute()
    {
        return $this->tanggal_pulang ? $this->tanggal_pulang->format('d F Y') : '-';
    }

    public function getTanggalKembaliFormattedAttribute()
    {
        return $this->tanggal_kembali ? $this->tanggal_kembali->format('d F Y') : '-';
    }

    public function getReviewedAtFormattedAttribute()
    {
        return $this->reviewed_at ? $this->reviewed_at->format('d F Y H:i') : '-';
    }

    /**
     * Accessor: Status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Menunggu' => 'badge-warning',
            'Disetujui' => 'badge-success',
            'Ditolak' => 'badge-danger',
        ];
        return $badges[$this->status] ?? 'badge-secondary';
    }
}