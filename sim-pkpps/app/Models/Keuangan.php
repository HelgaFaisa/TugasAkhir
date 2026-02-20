<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keuangan extends Model
{
    use HasFactory;

    protected $table = 'keuangan';

    protected $fillable = [
        'id_keuangan', 'jenis', 'nominal', 'keterangan', 'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_keuangan)) {
                $last = static::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_keuangan, 3)) + 1 : 1;
                $model->id_keuangan = 'KEU' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // ── Scopes ──
    public function scopePemasukan($query)    { return $query->where('jenis', 'pemasukan'); }
    public function scopePengeluaran($query)  { return $query->where('jenis', 'pengeluaran'); }

    public function scopeBulan($query, $bulan, $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('id_keuangan', 'like', "%{$search}%")
              ->orWhere('keterangan', 'like', "%{$search}%");
        });
    }

    // ── Accessors ──
    public function getNominalFormatAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}
