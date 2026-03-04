<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MesinSantriMapping extends Model
{
    protected $fillable = [
        'id_mesin', 'id_santri', 'nama_mesin', 'dept_mesin', 'is_active', 'catatan',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }
}