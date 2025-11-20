<?php
// app/Models/KategoriPelanggaran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPelanggaran extends Model
{
    use HasFactory;

    /**
     * Field yang boleh diisi massal (mass assignment)
     */
    protected $fillable = [
        'id_kategori',
        'nama_pelanggaran',
        'poin',
    ];

    /**
     * Cast attributes ke tipe data tertentu
     */
    protected $casts = [
        'poin' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Generator ID Kustom (KP001, KP002, ...)
     * Metode ini akan dijalankan setiap kali model baru dibuat (insert).
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Pastikan ID kustom belum terisi
            if (empty($model->id_kategori)) {
                // Ambil data kategori terakhir berdasarkan ID default
                $last = KategoriPelanggaran::orderBy('id', 'desc')->first();
                
                // Tentukan nomor urut berikutnya
                // Jika ada data terakhir, ambil angka dari ID kustom (misal KP001 -> 1) dan tambahkan 1
                $num = $last ? intval(substr($last->id_kategori, 2)) + 1 : 1;
                
                // Format ID: 'KP' + nomor urut 3 digit (dengan padding 0)
                $model->id_kategori = 'KP' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relasi: Kategori memiliki banyak riwayat pelanggaran (hasMany).
     * Satu kategori bisa digunakan untuk banyak riwayat pelanggaran.
     */
    public function riwayatPelanggaran()
    {
        return $this->hasMany(RiwayatPelanggaran::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Accessor: Mendapatkan total penggunaan kategori
     */
    public function getTotalPenggunaanAttribute()
    {
        return $this->riwayatPelanggaran()->count();
    }

    /**
     * Accessor: Mendapatkan total poin terkumpul dari kategori ini
     */
    public function getTotalPoinTerkumpulAttribute()
    {
        return $this->riwayatPelanggaran()->sum('poin');
    }

    /**
     * Scope: Filter kategori berdasarkan rentang poin
     */
    public function scopePoinRendah($query)
    {
        return $query->where('poin', '<', 10);
    }

    public function scopePoinSedang($query)
    {
        return $query->whereBetween('poin', [10, 20]);
    }

    public function scopePoinTinggi($query)
    {
        return $query->where('poin', '>', 20);
    }

    /**
     * Scope: Search kategori berdasarkan nama
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_pelanggaran', 'like', "%{$search}%")
              ->orWhere('id_kategori', 'like', "%{$search}%");
        });
    }

    /**
     * Method: Cek apakah kategori masih digunakan
     */
    public function isUsed()
    {
        return $this->riwayatPelanggaran()->exists();
    }
}