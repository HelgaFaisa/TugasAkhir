<?php
// Models/KategoriKegiatan
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriKegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_id',
        'nama_kategori',
        'keterangan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Auto-generate kategori_id (KT001, KT002...)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kategori_id)) {
                $last = self::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->kategori_id, 2)) + 1 : 1;
                $model->kategori_id = 'KT' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relasi ke Kegiatan (One to Many)
     */
    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class, 'kategori_id', 'kategori_id');
    }

    /**
     * Accessor: Total Kegiatan
     */
    public function getTotalKegiatanAttribute()
    {
        return $this->kegiatans()->count();
    }

    /**
     * Scope: Search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_kategori', 'like', "%{$search}%")
              ->orWhere('kategori_id', 'like', "%{$search}%")
              ->orWhere('keterangan', 'like', "%{$search}%");
        });
    }
}