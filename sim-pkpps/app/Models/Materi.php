<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $table = 'materi';

    /**
     * Field yang boleh diisi massal
     */
    protected $fillable = [
        'id_materi',
        'kategori',
        'kelas',
        'nama_kitab',
        'halaman_mulai',
        'halaman_akhir',
        'total_halaman',
        'deskripsi',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'halaman_mulai' => 'integer',
        'halaman_akhir' => 'integer',
        'total_halaman' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Generator ID Kustom (M001, M002, ...)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_materi)) {
                $last = Materi::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_materi, 1)) + 1 : 1;
                $model->id_materi = 'M' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }

            // Auto-calculate total_halaman
            if ($model->halaman_mulai && $model->halaman_akhir) {
                $model->total_halaman = $model->halaman_akhir - $model->halaman_mulai + 1;
            }
        });

        static::updating(function ($model) {
            // Auto-calculate total_halaman saat update
            if ($model->halaman_mulai && $model->halaman_akhir) {
                $model->total_halaman = $model->halaman_akhir - $model->halaman_mulai + 1;
            }
        });
    }

    /**
     * Scope untuk filter berdasarkan kategori
     */
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope untuk filter berdasarkan kelas
     */
    public function scopeKelas($query, $kelas)
    {
        return $query->where('kelas', $kelas);
    }

    /**
     * Scope untuk search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_kitab', 'like', "%{$search}%")
              ->orWhere('id_materi', 'like', "%{$search}%")
              ->orWhere('deskripsi', 'like', "%{$search}%");
        });
    }

    /**
     * Accessor untuk badge kategori
     */
    public function getKategoriBadgeAttribute()
    {
        $badges = [
            'Al-Qur\'an' => '<span class="badge badge-primary"><i class="fas fa-book-quran"></i> Al-Qur\'an</span>',
            'Hadist' => '<span class="badge badge-success"><i class="fas fa-scroll"></i> Hadist</span>',
            'Materi Tambahan' => '<span class="badge badge-info"><i class="fas fa-book"></i> Materi Tambahan</span>',
        ];

        return $badges[$this->kategori] ?? $this->kategori;
    }

    /**
     * Accessor untuk badge kelas
     */
    public function getKelasBadgeAttribute()
    {
        $badges = [
            'Lambatan' => '<span class="badge badge-secondary">Lambatan</span>',
            'Cepatan' => '<span class="badge badge-warning">Cepatan</span>',
            'PB' => '<span class="badge badge-danger">PB</span>',
        ];

        return $badges[$this->kelas] ?? $this->kelas;
    }

    /**
     * Relasi: Materi memiliki banyak capaian
     */
    public function capaian()
    {
        return $this->hasMany(Capaian::class, 'id_materi', 'id_materi');
    }

    /**
     * Get jumlah santri yang sudah ada capaian
     */
    public function getJumlahSantriAttribute()
    {
        return $this->capaian()->distinct('id_santri')->count('id_santri');
    }
}