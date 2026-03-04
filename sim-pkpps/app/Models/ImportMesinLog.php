<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportMesinLog extends Model
{
    protected $fillable = [
        'user_id', 'jumlah_scan', 'berhasil',
        'konflik_selesai', 'dilewati', 'no_santri',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}