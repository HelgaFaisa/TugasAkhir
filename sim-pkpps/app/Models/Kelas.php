<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Kelas
 * 
 * Mengelola detail kelas per kelompok (PB, Lambatan, SD 1-6, dst)
 * 
 * @property int $id
 * @property string $kode_kelas - Kode unik kelas (KLS001, KLS002, dst)
 * @property string $nama_kelas - Nama kelas (PB, Lambatan, SD 1, dst)
 * @property string $id_kelompok - Foreign key ke kelompok_kelas
 * @property int $urutan - Urutan tampilan dalam kelompok
 * @property bool $is_active - Status aktif
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Kelas extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kelas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'kode_kelas',
        'nama_kelas',
        'id_kelompok',
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
     * Boot method untuk auto-generate kode_kelas
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_kelas)) {
                $last = self::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->kode_kelas, 3)) + 1 : 1;
                $model->kode_kelas = 'KLS' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relasi: Kelas belongs to Kelompok (Many to One)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kelompok()
    {
        return $this->belongsTo(KelompokKelas::class, 'id_kelompok', 'id_kelompok');
    }

    /**
     * Relasi: Kelas memiliki banyak santri (Many to Many through santri_kelas)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function santris()
    {
        return $this->belongsToMany(Santri::class, 'santri_kelas', 'id_kelas', 'id_santri')
                    ->withPivot('tahun_ajaran', 'is_primary')
                    ->withTimestamps();
    }

    /**
     * Relasi: Kelas memiliki banyak kegiatan (Many to Many through kegiatan_kelas)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function kegiatans()
    {
        return $this->belongsToMany(Kegiatan::class, 'kegiatan_kelas', 'id_kelas', 'kegiatan_id', 'id', 'kegiatan_id')
                    ->withTimestamps();
    }

    /**
     * Relasi: Kelas memiliki banyak record santri_kelas (One to Many)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function santriKelas()
    {
        return $this->hasMany(SantriKelas::class, 'id_kelas', 'id');
    }

    /**
     * Scope: Filter kelas yang aktif
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
     * Scope: Filter by kelompok
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $id_kelompok
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByKelompok($query, $id_kelompok)
    {
        return $query->where('id_kelompok', $id_kelompok);
    }

    /**
     * Accessor: Total santri dalam kelas
     *
     * @return int
     */
    public function getTotalSantriAttribute()
    {
        return $this->santris()->count();
    }

    /**
     * Accessor: Total kegiatan untuk kelas ini
     *
     * @return int
     */
    public function getTotalKegiatanAttribute()
    {
        return $this->kegiatans()->count();
    }

    /**
     * Accessor: Nama kelas lengkap dengan kelompok
     *
     * @return string
     */
    public function getNamaLengkapAttribute()
    {
        return $this->kelompok->nama_kelompok . ' - ' . $this->nama_kelas;
    }
}
