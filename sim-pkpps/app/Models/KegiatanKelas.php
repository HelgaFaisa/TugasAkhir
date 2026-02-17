<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model KegiatanKelas (Pivot Model)
 * 
 * Mengelola relasi many-to-many antara Kegiatan dan Kelas
 * 
 * @property int $id
 * @property string $kegiatan_id - Foreign key ke kegiatans
 * @property int $id_kelas - Foreign key ke kelas
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class KegiatanKelas extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kegiatan_kelas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'kegiatan_id',
        'id_kelas',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi: KegiatanKelas belongs to Kegiatan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id', 'kegiatan_id');
    }

    /**
     * Relasi: KegiatanKelas belongs to Kelas
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }

    /**
     * Scope: Filter by kegiatan
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $kegiatan_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByKegiatan($query, $kegiatan_id)
    {
        return $query->where('kegiatan_id', $kegiatan_id);
    }

    /**
     * Scope: Filter by kelas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id_kelas
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByKelas($query, $id_kelas)
    {
        return $query->where('id_kelas', $id_kelas);
    }

    /**
     * Accessor: Nama kegiatan
     *
     * @return string
     */
    public function getNamaKegiatanAttribute()
    {
        return $this->kegiatan ? $this->kegiatan->nama_kegiatan : '-';
    }

    /**
     * Accessor: Nama kelas
     *
     * @return string
     */
    public function getNamaKelasAttribute()
    {
        return $this->kelas ? $this->kelas->nama_kelas : '-';
    }
}
