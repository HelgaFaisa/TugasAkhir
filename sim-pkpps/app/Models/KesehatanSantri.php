<?php
// app/Models/KesehatanSantri.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KesehatanSantri extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_kesehatan',
        'id_santri',
        'tanggal_masuk',
        'tanggal_keluar',
        'keluhan',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method untuk auto-generate ID Kesehatan
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_kesehatan)) {
                // Ambil data terakhir
                $last = KesehatanSantri::orderBy('id', 'desc')->first();
                
                // Generate nomor urut
                $num = $last ? intval(substr($last->id_kesehatan, 1)) + 1 : 1;
                
                // Format: K001, K002, dst
                $model->id_kesehatan = 'K' . str_pad($num, 3, '0', STR_PAD_LEFT);
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
     * Accessor: Format tanggal masuk Indonesia
     */
    public function getTanggalMasukFormattedAttribute()
    {
        return $this->tanggal_masuk ? 
               Carbon::parse($this->tanggal_masuk)->locale('id')->isoFormat('D MMM Y') : 
               '-';
    }

    /**
     * Accessor: Format tanggal keluar Indonesia
     */
    public function getTanggalKeluarFormattedAttribute()
    {
        return $this->tanggal_keluar ? 
               Carbon::parse($this->tanggal_keluar)->locale('id')->isoFormat('D MMM Y') : 
               null;
    }

    /**
     * Accessor: Hitung lama dirawat
     */
    public function getLamaDirawatAttribute()
    {
        $tanggalMasuk = Carbon::parse($this->tanggal_masuk);
        $tanggalKeluar = $this->tanggal_keluar ? 
                        Carbon::parse($this->tanggal_keluar) : 
                        Carbon::now();
        
        return $tanggalMasuk->diffInDays($tanggalKeluar);
    }

    /**
     * Accessor: Warna badge status
     */
    public function getStatusBadgeColorAttribute()
    {
        return [
            'dirawat' => 'danger',
            'sembuh' => 'success',
            'izin' => 'warning',
        ][$this->status] ?? 'secondary';
    }

    /**
     * Scope: Filter santri yang sedang dirawat
     */
    public function scopeDirawat($query)
    {
        return $query->where('status', 'dirawat');
    }

    /**
     * Scope: Filter santri yang sudah sembuh
     */
    public function scopeSembuh($query)
    {
        return $query->where('status', 'sembuh');
    }

    /**
     * Scope: Filter santri yang izin
     */
    public function scopeIzin($query)
    {
        return $query->where('status', 'izin');
    }

    /**
     * Scope: Filter berdasarkan bulan dan tahun
     */
    public function scopeByMonthYear($query, $month = null, $year = null)
    {
        if ($month) {
            $query->whereMonth('tanggal_masuk', $month);
        }
        if ($year) {
            $query->whereYear('tanggal_masuk', $year);
        }
        return $query;
    }

    /**
     * Scope: Search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('id_kesehatan', 'like', "%{$search}%")
              ->orWhere('id_santri', 'like', "%{$search}%")
              ->orWhere('keluhan', 'like', "%{$search}%")
              ->orWhereHas('santri', function($query) use ($search) {
                  $query->where('nama_lengkap', 'like', "%{$search}%");
              });
        });
    }
}