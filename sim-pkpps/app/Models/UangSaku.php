<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UangSaku extends Model
{
    use HasFactory;

    protected $table = 'uang_saku';

    protected $fillable = [
        'id_uang_saku',
        'id_santri',
        'jenis_transaksi',
        'nominal',
        'keterangan',
        'tanggal_transaksi',
        'saldo_sebelum',
        'saldo_sesudah',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'nominal' => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Auto-generate ID kustom saat create
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_uang_saku)) {
                $last = UangSaku::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_uang_saku, 2)) + 1 : 1;
                $model->id_uang_saku = 'SK' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
            
            // Hitung saldo otomatis
            $saldoTerakhir = UangSaku::where('id_santri', $model->id_santri)
                ->orderBy('tanggal_transaksi', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();
            
            $model->saldo_sebelum = $saldoTerakhir ? $saldoTerakhir->saldo_sesudah : 0;
            
            if ($model->jenis_transaksi === 'pemasukan') {
                $model->saldo_sesudah = $model->saldo_sebelum + $model->nominal;
            } else {
                $model->saldo_sesudah = $model->saldo_sebelum - $model->nominal;
            }
        });

        static::updating(function ($model) {
            // Recalculate saldo when updating
            if ($model->isDirty(['nominal', 'jenis_transaksi'])) {
                $saldoTerakhir = UangSaku::where('id_santri', $model->id_santri)
                    ->where('id', '<', $model->id)
                    ->orderBy('tanggal_transaksi', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                $model->saldo_sebelum = $saldoTerakhir ? $saldoTerakhir->saldo_sesudah : 0;
                
                if ($model->jenis_transaksi === 'pemasukan') {
                    $model->saldo_sesudah = $model->saldo_sebelum + $model->nominal;
                } else {
                    $model->saldo_sesudah = $model->saldo_sebelum - $model->nominal;
                }
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
     * Scope untuk filter berdasarkan santri
     */
    public function scopeBySantri($query, $idSantri)
    {
        return $query->where('id_santri', $idSantri);
    }

    /**
     * Scope untuk filter berdasarkan jenis transaksi
     */
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_transaksi', $jenis);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('tanggal_transaksi', [$start, $end]);
    }

    /**
     * Scope untuk search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('id_uang_saku', 'like', "%{$search}%")
              ->orWhere('keterangan', 'like', "%{$search}%")
              ->orWhereHas('santri', function($sq) use ($search) {
                  $sq->where('nama_lengkap', 'like', "%{$search}%")
                     ->orWhere('id_santri', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Accessor untuk format nominal
     */
    public function getNominalFormatAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    /**
     * Accessor untuk format saldo
     */
    public function getSaldoSesudahFormatAttribute()
    {
        return 'Rp ' . number_format($this->saldo_sesudah, 0, ',', '.');
    }
}