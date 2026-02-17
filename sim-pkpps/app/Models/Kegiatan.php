<?php
// Models/Kegiatan
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kegiatan_id',
        'kategori_id',
        'nama_kegiatan',
        'hari',
        'waktu_mulai',
        'waktu_selesai',
        'materi',
        'keterangan',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime:H:i',
        'waktu_selesai' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Auto-generate kegiatan_id (KG001, KG002...)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kegiatan_id)) {
                $last = self::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->kegiatan_id, 2)) + 1 : 1;
                $model->kegiatan_id = 'KG' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relasi ke Kategori
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriKegiatan::class, 'kategori_id', 'kategori_id');
    }

    /**
     * Relasi ke Absensi (akan dibuat di tahap selanjutnya)
     */
    public function absensis()
    {
        return $this->hasMany(AbsensiKegiatan::class, 'kegiatan_id', 'kegiatan_id');
    }

    // ==========================================
    // RELASI SISTEM KELAS BARU
    // ==========================================

    /**
     * Relasi: Kegiatan belongs to many Kelas (many-to-many through kegiatan_kelas)
     */
    public function kelasKegiatan()
    {
        return $this->belongsToMany(Kelas::class, 'kegiatan_kelas', 'kegiatan_id', 'id_kelas', 'kegiatan_id', 'id')
                    ->withTimestamps();
    }

    /**
     * Relasi: Kegiatan memiliki banyak record kegiatan_kelas (hasMany)
     */
    public function kegiatanKelasPivot()
    {
        return $this->hasMany(KegiatanKelas::class, 'kegiatan_id', 'kegiatan_id');
    }

    /**
     * Scope: Filter berdasarkan hari
     */
    public function scopeHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    /**
     * Scope: Search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_kegiatan', 'like', "%{$search}%")
              ->orWhere('kegiatan_id', 'like', "%{$search}%")
              ->orWhere('materi', 'like', "%{$search}%");
        });
    }

    /**
     * Accessor: Waktu Lengkap
     */
    public function getWaktuLengkapAttribute()
    {
        return date('H:i', strtotime($this->waktu_mulai)) . ' - ' . 
               date('H:i', strtotime($this->waktu_selesai));
    }

    // ==========================================
    // HELPER METHODS SISTEM KELAS BARU
    // ==========================================

    /**
     * Check apakah kegiatan untuk semua kelas (umum)
     * Kegiatan dianggap umum jika tidak ada relasi ke kegiatan_kelas
     *
     * @return bool
     */
    public function isForAllClasses()
    {
        return $this->kegiatanKelasPivot()->count() === 0;
    }

    /**
     * Check apakah kegiatan untuk kelas tertentu
     * Return true jika kegiatan umum ATAU ada relasi ke kelas tersebut
     *
     * @param int $id_kelas
     * @return bool
     */
    public function isForKelas($id_kelas)
    {
        // Jika kegiatan umum (tidak ada relasi kelas), semua kelas bisa
        if ($this->isForAllClasses()) {
            return true;
        }
        
        // Cek apakah ada relasi ke kelas tertentu
        return $this->kelasKegiatan()->where('kelas.id', $id_kelas)->exists();
    }

    /**
     * Get santri yang eligible untuk kegiatan ini
     * - Jika umum: return all active santri
     * - Jik a specific: return santri yang kelasnya match
     *
     * @param string|null $tahun_ajaran - Filter by tahun ajaran
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getEligibleSantris($tahun_ajaran = null)
    {
        if ($tahun_ajaran === null) {
            $tahun_ajaran = SantriKelas::getCurrentAcademicYear();
        }
        
        // Jika kegiatan umum, return semua santri aktif
        if ($this->isForAllClasses()) {
            return Santri::where('status', 'Aktif');
        }
        
        // Jika specific, return santri yang kelasnya match
        $kelasIds = $this->kelasKegiatan()->pluck('kelas.id');
        
        return Santri::where('status', 'Aktif')
                     ->whereHas('kelasSantri', function($q) use ($kelasIds, $tahun_ajaran) {
                         $q->whereIn('id_kelas', $kelasIds)
                           ->where('tahun_ajaran', $tahun_ajaran);
                     });
    }

    /**
     * Assign kegiatan ke kelas-kelas tertentu
     * Akan replace semua relasi kelas existing
     *
     * @param array $kelas_ids - Array of kelas IDs
     * @return void
     */
    public function assignKelas(array $kelas_ids)
    {
        // Delete existing relations
        $this->kegiatanKelasPivot()->delete();
        
        // Create new relations
        if (!empty($kelas_ids)) {
            $data = [];
            foreach ($kelas_ids as $id_kelas) {
                $data[] = [
                    'kegiatan_id' => $this->kegiatan_id,
                    'id_kelas' => $id_kelas,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            KegiatanKelas::insert($data);
        }
    }
}