<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiKegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'absensi_id',
        'kegiatan_id',
        'id_santri',
        'tanggal',
        'status',
        'metode_absen',
        'waktu_absen',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_absen' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Auto-generate absensi_id (A001, A002...)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->absensi_id)) {
                $last = self::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->absensi_id, 1)) + 1 : 1;
                $model->absensi_id = 'A' . str_pad($num, 3, '0', STR_PAD_LEFT);
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
     * Relasi ke Kegiatan
     */
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id', 'kegiatan_id');
    }

    /**
     * Scope: Filter berdasarkan tanggal
     */
    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    /**
     * Scope: Filter berdasarkan kegiatan
     */
    public function scopeKegiatan($query, $kegiatan_id)
    {
        return $query->where('kegiatan_id', $kegiatan_id);
    }

    /**
     * Accessor: Status Badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Hadir' => '<span class="badge badge-success"><i class="fas fa-check"></i> Hadir</span>',
            'Izin' => '<span class="badge badge-warning"><i class="fas fa-info-circle"></i> Izin</span>',
            'Sakit' => '<span class="badge badge-info"><i class="fas fa-heartbeat"></i> Sakit</span>',
            'Alpa' => '<span class="badge badge-danger"><i class="fas fa-times"></i> Alpa</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }
}