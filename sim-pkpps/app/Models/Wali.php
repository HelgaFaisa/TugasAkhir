<?php
// app/Models/Wali.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wali extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Generator ID Kustom (WS001, WS002, ...)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_wali)) {
                $last = Wali::orderBy('id', 'desc')->first();
                // Ambil nomor urut terakhir, jika ada, tambahkan 1. Jika tidak, mulai dari 1.
                $num = $last ? intval(substr($last->id_wali, 2)) + 1 : 1;
                // Format ID: 'WS' + nomor urut 3 digit (dengan padding 0)
                $model->id_wali = 'WS' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}