<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlasifikasiPelanggaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_klasifikasi',
        'nama_klasifikasi',
        'deskripsi',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Auto-generate ID
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_klasifikasi)) {
                $last = KlasifikasiPelanggaran::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_klasifikasi, 2)) + 1 : 1;
                $model->id_klasifikasi = 'KL' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relasi: Klasifikasi memiliki banyak pelanggaran
    public function pelanggarans()
    {
        return $this->hasMany(KategoriPelanggaran::class, 'id_klasifikasi', 'id_klasifikasi');
    }

    // Scope: Hanya yang aktif
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: Urut berdasarkan urutan
    public function scopeByUrutan($query)
    {
        return $query->orderBy('urutan', 'asc')->orderBy('nama_klasifikasi', 'asc');
    }
}