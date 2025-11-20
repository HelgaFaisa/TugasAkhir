<?php
// app/Models/PembayaranSpp.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PembayaranSpp extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_spp';

    protected $fillable = [
        'id_pembayaran',
        'id_santri',
        'bulan',
        'tahun',
        'nominal',
        'status',
        'tanggal_bayar',
        'batas_bayar',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'batas_bayar' => 'date',
        'nominal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method untuk auto-generate ID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_pembayaran)) {
                $last = PembayaranSpp::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_pembayaran, 3)) + 1 : 1;
                $model->id_pembayaran = 'SPP' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relasi: Pembayaran SPP milik satu Santri
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }

    /**
     * Accessor: Nama bulan dalam bahasa Indonesia
     */
    public function getBulanNamaAttribute()
    {
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $bulanIndo[$this->bulan] ?? '-';
    }

    /**
     * Accessor: Periode lengkap (Januari 2024)
     */
    public function getPeriodeLengkapAttribute()
    {
        return $this->bulan_nama . ' ' . $this->tahun;
    }

    /**
     * Accessor: Status Badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        if ($this->status === 'Lunas') {
            return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Lunas</span>';
        }
        
        // Cek apakah telat
        if ($this->isTelat()) {
            return '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Belum Lunas (Telat)</span>';
        }
        
        return '<span class="badge badge-warning"><i class="fas fa-clock"></i> Belum Lunas</span>';
    }

    /**
     * Cek apakah pembayaran sudah telat
     */
    public function isTelat()
    {
        if ($this->status === 'Lunas') {
            return false;
        }
        
        return Carbon::now()->isAfter($this->batas_bayar);
    }

    /**
     * Accessor: Nominal format Rupiah
     */
    public function getNominalFormatAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    /**
     * Scope: Filter pembayaran belum lunas
     */
    public function scopeBelumLunas($query)
    {
        return $query->where('status', 'Belum Lunas');
    }

    /**
     * Scope: Filter pembayaran lunas
     */
    public function scopeLunas($query)
    {
        return $query->where('status', 'Lunas');
    }

    /**
     * Scope: Filter pembayaran telat
     */
    public function scopeTelat($query)
    {
        return $query->where('status', 'Belum Lunas')
                     ->where('batas_bayar', '<', Carbon::now());
    }

    /**
     * Scope: Filter by tahun
     */
    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    /**
     * Scope: Filter by bulan
     */
    public function scopeBulan($query, $bulan)
    {
        return $query->where('bulan', $bulan);
    }

    /**
     * Scope: Search
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('santri', function($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
              ->orWhere('id_santri', 'like', "%{$search}%")
              ->orWhere('nis', 'like', "%{$search}%");
        })->orWhere('id_pembayaran', 'like', "%{$search}%");
    }
}