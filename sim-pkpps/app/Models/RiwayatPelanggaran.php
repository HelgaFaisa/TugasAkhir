<?php
// app/Models/RiwayatPelanggaran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RiwayatPelanggaran extends Model
{
    use HasFactory;

    /**
     * Field yang boleh diisi massal (mass assignment)
     */
    protected $fillable = [
        'id_riwayat',
        'id_santri',
        'id_kategori',
        'tanggal',
        'poin',
        'keterangan',
    ];

    /**
     * Cast attributes ke tipe data tertentu
     */
    protected $casts = [
        'tanggal' => 'date',
        'poin' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Generator ID Kustom (P001, P002, ...)
     * Metode ini akan dijalankan setiap kali model baru dibuat (insert).
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Pastikan ID kustom belum terisi
            if (empty($model->id_riwayat)) {
                // Ambil data riwayat terakhir berdasarkan ID default
                $last = RiwayatPelanggaran::orderBy('id', 'desc')->first();
                
                // Tentukan nomor urut berikutnya
                // Jika ada data terakhir, ambil angka dari ID kustom (misal P001 -> 1) dan tambahkan 1
                $num = $last ? intval(substr($last->id_riwayat, 1)) + 1 : 1;
                
                // Format ID: 'P' + nomor urut 3 digit (dengan padding 0)
                $model->id_riwayat = 'P' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relasi: Riwayat belongsTo Santri
     * Setiap riwayat pelanggaran dimiliki oleh satu santri
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }

    /**
     * Relasi: Riwayat belongsTo Kategori
     * Setiap riwayat pelanggaran memiliki satu kategori
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriPelanggaran::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Accessor: Format tanggal Indonesia
     */
    public function getTanggalFormatAttribute()
    {
        return Carbon::parse($this->tanggal)->isoFormat('D MMMM YYYY');
    }

    /**
     * Accessor: Get nama santri (dengan fallback)
     */
    public function getNamaSantriAttribute()
    {
        return $this->santri ? $this->santri->nama_lengkap : 'Santri tidak ditemukan';
    }

    /**
     * Accessor: Get nama kategori (dengan fallback)
     */
    public function getNamaKategoriAttribute()
    {
        return $this->kategori ? $this->kategori->nama_pelanggaran : 'Kategori tidak ditemukan';
    }

    /**
     * Scope: Filter riwayat berdasarkan santri
     */
    public function scopeBySantri($query, $idSantri)
    {
        return $query->where('id_santri', $idSantri);
    }

    /**
     * Scope: Filter riwayat berdasarkan kategori
     */
    public function scopeByKategori($query, $idKategori)
    {
        return $query->where('id_kategori', $idKategori);
    }

    /**
     * Scope: Filter riwayat berdasarkan tanggal
     */
    public function scopeByTanggal($query, $tanggalMulai, $tanggalSelesai = null)
    {
        if ($tanggalSelesai) {
            return $query->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);
        }
        return $query->whereDate('tanggal', $tanggalMulai);
    }

    /**
     * Scope: Filter riwayat bulan ini
     */
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal', Carbon::now()->month)
                     ->whereYear('tanggal', Carbon::now()->year);
    }

    /**
     * Scope: Urutkan berdasarkan tanggal terbaru
     */
    public function scopeTerbaru($query)
    {
        return $query->orderBy('tanggal', 'desc')
                     ->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Search riwayat
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('id_riwayat', 'like', "%{$search}%")
              ->orWhere('keterangan', 'like', "%{$search}%")
              ->orWhereHas('santri', function($sq) use ($search) {
                  $sq->where('nama_lengkap', 'like', "%{$search}%");
              })
              ->orWhereHas('kategori', function($sq) use ($search) {
                  $sq->where('nama_pelanggaran', 'like', "%{$search}%");
              });
        });
    }
}