<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model KelompokKelas
 * 
 * Mengelola kategori/kelompok kelas (Pondok, Sekolah Formal, Umum)
 * 
 * @property int $id
 * @property string $id_kelompok - Kode unik kelompok (KEL001, KEL002, dst)
 * @property string $nama_kelompok - Nama kelompok kelas
 * @property string|null $deskripsi - Deskripsi kelompok
 * @property int $urutan - Urutan tampilan
 * @property bool $is_active - Status aktif
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class KelompokKelas extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kelompok_kelas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id_kelompok',
        'nama_kelompok',
        'deskripsi',
        'urutan',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method untuk auto-generate id_kelompok
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_kelompok)) {
                $last = self::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_kelompok, 3)) + 1 : 1;
                $model->id_kelompok = 'KEL' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relasi: Kelompok memiliki banyak kelas (One to Many)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_kelompok', 'id_kelompok');
    }

    /**
     * Scope: Filter kelompok yang aktif
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by urutan
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan', 'asc');
    }

    /**
     * Accessor: Total kelas dalam kelompok
     *
     * @return int
     */
    public function getTotalKelasAttribute()
    {
        return $this->kelas()->count();
    }

    /**
     * Accessor: Total kelas aktif dalam kelompok
     *
     * @return int
     */
    public function getTotalKelasAktifAttribute()
    {
        return $this->kelas()->where('is_active', true)->count();
    }
}
