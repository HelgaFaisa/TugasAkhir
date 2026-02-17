<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPelanggaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_kategori',
        'id_klasifikasi',
        'nama_pelanggaran',
        'poin',
        'kafaroh',
        'is_active',
    ];

    protected $casts = [
        'poin' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_kategori)) {
                $last = KategoriPelanggaran::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_kategori, 2)) + 1 : 1;
                $model->id_kategori = 'KP' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relasi: Pelanggaran belongsTo Klasifikasi
    public function klasifikasi()
    {
        return $this->belongsTo(KlasifikasiPelanggaran::class, 'id_klasifikasi', 'id_klasifikasi');
    }

    // Relasi: Pelanggaran hasMany Riwayat
    public function riwayatPelanggaran()
    {
        return $this->hasMany(RiwayatPelanggaran::class, 'id_kategori', 'id_kategori');
    }

    // Scope: Hanya yang aktif
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: Filter by klasifikasi
    public function scopeByKlasifikasi($query, $idKlasifikasi)
    {
        return $query->where('id_klasifikasi', $idKlasifikasi);
    }

    // Accessor: Nama dengan klasifikasi
    public function getNamaLengkapAttribute()
    {
        $klasifikasi = $this->klasifikasi ? $this->klasifikasi->nama_klasifikasi : 'Tanpa Klasifikasi';
        return "[{$klasifikasi}] {$this->nama_pelanggaran}";
    }
}