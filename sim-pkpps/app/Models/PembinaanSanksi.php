<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembinaanSanksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_pembinaan',
        'judul',
        'konten',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_pembinaan)) {
                $last = PembinaanSanksi::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_pembinaan, 2)) + 1 : 1;
                $model->id_pembinaan = 'PS' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByUrutan($query)
    {
        return $query->orderBy('urutan', 'asc')->orderBy('created_at', 'asc');
    }
}