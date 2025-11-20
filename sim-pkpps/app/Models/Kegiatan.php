<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kegiatan_id',
        'kategori_id',
        'nama_kegiatan',
        'hari',
        'waktu_mulai',
        'waktu_selesai',
        'materi',
        'keterangan',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime:H:i',
        'waktu_selesai' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Auto-generate kegiatan_id (KG001, KG002...)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kegiatan_id)) {
                $last = self::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->kegiatan_id, 2)) + 1 : 1;
                $model->kegiatan_id = 'KG' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relasi ke Kategori
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriKegiatan::class, 'kategori_id', 'kategori_id');
    }

    /**
     * Relasi ke Absensi (akan dibuat di tahap selanjutnya)
     */
    public function absensis()
    {
        return $this->hasMany(AbsensiKegiatan::class, 'kegiatan_id', 'kegiatan_id');
    }

    /**
     * Scope: Filter berdasarkan hari
     */
    public function scopeHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    /**
     * Scope: Search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_kegiatan', 'like', "%{$search}%")
              ->orWhere('kegiatan_id', 'like', "%{$search}%")
              ->orWhere('materi', 'like', "%{$search}%");
        });
    }

    /**
     * Accessor: Waktu Lengkap
     */
    public function getWaktuLengkapAttribute()
    {
        return date('H:i', strtotime($this->waktu_mulai)) . ' - ' . 
               date('H:i', strtotime($this->waktu_selesai));
    }
}