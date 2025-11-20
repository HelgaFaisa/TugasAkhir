<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capaian extends Model
{
    use HasFactory;

    protected $table = 'capaian';

    protected $fillable = [
        'id_capaian',
        'id_santri',
        'id_materi',
        'id_semester',
        'halaman_selesai',
        'persentase',
        'catatan',
        'tanggal_input',
    ];

    protected $casts = [
        'persentase' => 'decimal:2',
        'tanggal_input' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Generator ID Kustom (CP001, CP002, ...)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_capaian)) {
                $last = Capaian::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_capaian, 2)) + 1 : 1;
                $model->id_capaian = 'CP' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }

            // Auto-calculate persentase
            $model->persentase = self::calculatePersentase($model->halaman_selesai, $model->id_materi);
        });

        static::updating(function ($model) {
            // Recalculate persentase saat update
            $model->persentase = self::calculatePersentase($model->halaman_selesai, $model->id_materi);
        });
    }

    /**
     * Relasi: Capaian belongs to Santri
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }

    /**
     * Relasi: Capaian belongs to Materi
     */
    public function materi()
    {
        return $this->belongsTo(Materi::class, 'id_materi', 'id_materi');
    }

    /**
     * Relasi: Capaian belongs to Semester
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester', 'id_semester');
    }

    /**
     * Parse halaman_selesai string menjadi array halaman
     * Input: "1-10,16-21,40"
     * Output: [1,2,3,...,10,16,17,...,21,40]
     */
    public static function parseHalamanSelesai($rangeString)
    {
        $pages = [];
        $ranges = explode(',', $rangeString);

        foreach ($ranges as $range) {
            $range = trim($range);
            
            if (strpos($range, '-') !== false) {
                // Range format: "1-10"
                list($start, $end) = explode('-', $range);
                $start = intval(trim($start));
                $end = intval(trim($end));
                
                for ($i = $start; $i <= $end; $i++) {
                    $pages[] = $i;
                }
            } else {
                // Single page: "40"
                $pages[] = intval($range);
            }
        }

        return array_unique($pages);
    }

    /**
     * Calculate persentase dari halaman_selesai
     */
    public static function calculatePersentase($halamanSelesai, $idMateri)
    {
        if (empty($halamanSelesai)) {
            return 0;
        }

        $materi = Materi::where('id_materi', $idMateri)->first();
        if (!$materi || $materi->total_halaman == 0) {
            return 0;
        }

        $pages = self::parseHalamanSelesai($halamanSelesai);
        $jumlahHalamanSelesai = count($pages);

        $persentase = ($jumlahHalamanSelesai / $materi->total_halaman) * 100;
        
        // Batasi max 100%
        return min($persentase, 100);
    }

    /**
     * Get array halaman yang sudah selesai
     */
    public function getPagesArrayAttribute()
    {
        return self::parseHalamanSelesai($this->halaman_selesai);
    }

    /**
     * Get jumlah halaman yang sudah selesai
     */
    public function getJumlahHalamanSelesaiAttribute()
    {
        return count($this->pages_array);
    }

    /**
     * Accessor: Badge persentase dengan warna
     */
    public function getPersentaseBadgeAttribute()
    {
        $persentase = $this->persentase;
        
        if ($persentase >= 100) {
            $class = 'badge-success';
            $icon = 'fa-check-circle';
        } elseif ($persentase >= 75) {
            $class = 'badge-primary';
            $icon = 'fa-battery-three-quarters';
        } elseif ($persentase >= 50) {
            $class = 'badge-warning';
            $icon = 'fa-battery-half';
        } elseif ($persentase >= 25) {
            $class = 'badge-danger';
            $icon = 'fa-battery-quarter';
        } else {
            $class = 'badge-secondary';
            $icon = 'fa-battery-empty';
        }

        return sprintf(
            '<span class="badge %s"><i class="fas %s"></i> %.2f%%</span>',
            $class,
            $icon,
            $persentase
        );
    }

    /**
     * Scope: Filter by santri
     */
    public function scopeBySantri($query, $idSantri)
    {
        return $query->where('id_santri', $idSantri);
    }

    /**
     * Scope: Filter by semester
     */
    public function scopeBySemester($query, $idSemester)
    {
        return $query->where('id_semester', $idSemester);
    }

    /**
     * Scope: Filter by kategori materi
     */
    public function scopeByKategori($query, $kategori)
    {
        return $query->whereHas('materi', function($q) use ($kategori) {
            $q->where('kategori', $kategori);
        });
    }
}