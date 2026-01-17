<?php
// app/Models/AbsensiKegiatan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AbsensiKegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'absensi_id',
        'kegiatan_id',
        'id_santri',
        'tanggal',
        'status',
        'metode_absen',
        'waktu_absen',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_absen' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Auto-generate absensi_id (A001, A002...)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->absensi_id)) {
                $last = self::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->absensi_id, 1)) + 1 : 1;
                $model->absensi_id = 'A' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relasi ke Santri
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }

    /**
     * Relasi ke Kegiatan
     */
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id', 'kegiatan_id');
    }

    /**
     * Scope: Filter berdasarkan tanggal
     */
    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    /**
     * Scope: Filter berdasarkan kegiatan
     */
    public function scopeKegiatan($query, $kegiatan_id)
    {
        return $query->where('kegiatan_id', $kegiatan_id);
    }

    /**
     * Accessor: Status Badge (HTML - untuk admin)
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Hadir' => '<span class="badge badge-success"><i class="fas fa-check"></i> Hadir</span>',
            'Izin' => '<span class="badge badge-warning"><i class="fas fa-info-circle"></i> Izin</span>',
            'Sakit' => '<span class="badge badge-info"><i class="fas fa-heartbeat"></i> Sakit</span>',
            'Alpa' => '<span class="badge badge-danger"><i class="fas fa-times"></i> Alpa</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }

    // ============================================
    // ✅ TAMBAHKAN METHOD-METHOD BARU DI BAWAH INI
    // ============================================

    /**
     * Accessor: Tanggal Formatted (untuk view santri)
     */
    public function getTanggalFormattedAttribute()
    {
        return Carbon::parse($this->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY');
    }

    /**
     * Accessor: Waktu Absen Formatted (untuk view santri)
     */
    public function getWaktuAbsenFormattedAttribute()
    {
        return $this->waktu_absen ? Carbon::parse($this->waktu_absen)->format('H:i') : '-';
    }

    /**
     * Accessor: Status Badge Class (CSS class only - untuk view santri)
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'Hadir' => 'badge-success',
            'Izin' => 'badge-info',
            'Sakit' => 'badge-warning',
            'Alpa' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('tanggal', [$start, $end]);
    }

    /**
     * Scope: Filter by month
     */
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('tanggal', $month)
                     ->whereYear('tanggal', $year);
    }
}