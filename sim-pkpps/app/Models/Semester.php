<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $table = 'semester';

    protected $fillable = [
        'id_semester',
        'nama_semester',
        'tahun_ajaran',
        'periode',
        'tanggal_mulai',
        'tanggal_akhir',
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
        'is_active' => 'boolean',
        'periode' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Generator ID Kustom (SEM001, SEM002, ...)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_semester)) {
                $last = Semester::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_semester, 3)) + 1 : 1;
                $model->id_semester = 'SEM' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }

            // Auto-generate nama_semester jika kosong
            if (empty($model->nama_semester)) {
                $model->nama_semester = "Semester {$model->periode} {$model->tahun_ajaran}";
            }
        });

        // Pastikan hanya 1 semester yang aktif
        static::saving(function ($model) {
            if ($model->is_active) {
                Semester::where('id', '!=', $model->id)->update(['is_active' => 0]);
            }
        });
    }

    /**
     * Relasi: Semester memiliki banyak capaian
     */
    public function capaian()
    {
        return $this->hasMany(Capaian::class, 'id_semester', 'id_semester');
    }

    /**
     * Scope: Semester aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope: Tahun ajaran tertentu
     */
    public function scopeTahunAjaran($query, $tahun)
    {
        return $query->where('tahun_ajaran', $tahun);
    }

    /**
     * Accessor: Badge status aktif
     */
    public function getStatusBadgeAttribute()
    {
        if ($this->is_active) {
            return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Aktif</span>';
        }
        return '<span class="badge badge-secondary">Tidak Aktif</span>';
    }
}