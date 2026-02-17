<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model SantriKelas (Pivot Model)
 * 
 * Mengelola relasi many-to-many antara Santri dan Kelas
 * 
 * @property int $id
 * @property string $id_santri - Foreign key ke santris
 * @property int $id_kelas - Foreign key ke kelas
 * @property string $tahun_ajaran - Tahun ajaran (2024/2025)
 * @property bool $is_primary - Kelas utama santri
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class SantriKelas extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'santri_kelas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id_santri',
        'id_kelas',
        'tahun_ajaran',
        'is_primary',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method untuk set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Auto-set tahun ajaran jika belum ada
            if (empty($model->tahun_ajaran)) {
                $model->tahun_ajaran = self::getCurrentAcademicYear();
            }
        });
    }

    /**
     * Relasi: SantriKelas belongs to Santri
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }

    /**
     * Relasi: SantriKelas belongs to Kelas
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }

    /**
     * Scope: Filter kelas primary santri
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope: Filter by tahun ajaran
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tahun
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTahunAjaran($query, $tahun)
    {
        return $query->where('tahun_ajaran', $tahun);
    }

    /**
     * Helper: Get current academic year
     * Format: 2024/2025
     *
     * @return string
     */
    public static function getCurrentAcademicYear()
    {
        $currentMonth = date('n'); // 1-12
        $currentYear = date('Y');

        // Jika bulan Juli (7) - Desember (12), tahun ajaran dimulai tahun ini
        // Jika bulan Januari (1) - Juni (6), tahun ajaran dimulai tahun lalu
        if ($currentMonth >= 7) {
            $startYear = $currentYear;
            $endYear = $currentYear + 1;
        } else {
            $startYear = $currentYear - 1;
            $endYear = $currentYear;
        }

        return $startYear . '/' . $endYear;
    }

    /**
     * Accessor: Nama kelas lengkap
     *
     * @return string
     */
    public function getNamaKelasAttribute()
    {
        return $this->kelas ? $this->kelas->nama_kelas : '-';
    }

    /**
     * Accessor: Nama santri
     *
     * @return string
     */
    public function getNamaSantriAttribute()
    {
        return $this->santri ? $this->santri->nama_lengkap : '-';
    }
}
