<?php
// app/Models/PembayaranSpp.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PembayaranSpp extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_spp';

    protected $fillable = [
        'id_pembayaran',
        'id_santri',
        'bulan',
        'tahun',
        'nominal',
        'status',
        'tanggal_bayar',
        'batas_bayar',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'batas_bayar'   => 'date',
        'nominal'       => 'decimal:2',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    // ══════════════════════════════════════════════════════
    // BOOT
    // ══════════════════════════════════════════════════════

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_pembayaran)) {
                $last = PembayaranSpp::orderBy('id', 'desc')->first();
                $num  = $last ? intval(substr($last->id_pembayaran, 3)) + 1 : 1;
                $model->id_pembayaran = 'SPP' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // ══════════════════════════════════════════════════════
    // RELASI
    // ══════════════════════════════════════════════════════

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }

    // ══════════════════════════════════════════════════════
    // CICILAN HELPERS
    //
    // Status di DB tetap "Belum Lunas" (tidak ubah enum).
    // Cicilan dideteksi dari keterangan berformat JSON:
    //   {"terbayar": 150000, "catatan": "Cicilan ke-1"}
    //
    // Keterangan teks biasa (non-JSON) tetap terbaca normal.
    // ══════════════════════════════════════════════════════

    /**
     * Cek apakah record ini berstatus cicilan
     * (status Belum Lunas + ada data terbayar di keterangan).
     */
    public function isCicilan(): bool
    {
        if ($this->status !== 'Belum Lunas') return false;
        $data = $this->getCicilanData();
        return $data !== null && ($data['terbayar'] ?? 0) > 0;
    }

    /**
     * Ambil array cicilan dari keterangan, atau null jika bukan JSON cicilan.
     */
    public function getCicilanData(): ?array
    {
        if (!$this->keterangan) return null;
        $decoded = json_decode($this->keterangan, true);
        if (json_last_error() !== JSON_ERROR_NONE) return null;
        if (!array_key_exists('terbayar', $decoded)) return null;
        return $decoded;
    }

    /**
     * Nominal yang sudah dibayar.
     */
    public function getNominalTerbayarAttribute(): float
    {
        if ($this->status === 'Lunas') return (float) $this->nominal;
        $data = $this->getCicilanData();
        return $data ? (float) ($data['terbayar'] ?? 0) : 0;
    }

    /**
     * Sisa yang belum dibayar.
     */
    public function getNominalSisaAttribute(): float
    {
        return max(0, (float) $this->nominal - $this->nominal_terbayar);
    }

    /**
     * Persentase cicilan (0–100).
     */
    public function getPorsentaseCicilanAttribute(): int
    {
        if (!$this->nominal || (float) $this->nominal == 0) return 0;
        return (int) min(100, round(($this->nominal_terbayar / (float) $this->nominal) * 100));
    }

    /**
     * Simpan progres cicilan ke keterangan (JSON).
     * Status DB tidak diubah — tetap "Belum Lunas".
     */
    public function setCicilan(float $terbayar, ?string $catatan = null): void
    {
        // Jika keterangan sebelumnya teks biasa, pindahkan sebagai catatan
        if ($this->keterangan && !$this->getCicilanData()) {
            $catatan = $catatan ?? $this->keterangan;
        }

        $data = ['terbayar' => $terbayar];
        if ($catatan) $data['catatan'] = $catatan;

        $this->keterangan = json_encode($data);
    }

    /**
     * Baca catatan teks (dari JSON atau teks biasa).
     */
    public function getCatatanTeksAttribute(): ?string
    {
        if (!$this->keterangan) return null;
        $data = $this->getCicilanData();
        if ($data) return $data['catatan'] ?? null;
        return $this->keterangan;
    }

    // ══════════════════════════════════════════════════════
    // ACCESSORS
    // ══════════════════════════════════════════════════════

    public function getBulanNamaAttribute(): string
    {
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',   9 => 'September',
            10 => 'Oktober',11 => 'November', 12 => 'Desember'
        ];
        return $bulanIndo[$this->bulan] ?? '-';
    }

    public function getPeriodeLengkapAttribute(): string
    {
        return $this->bulan_nama . ' ' . $this->tahun;
    }

    public function getNominalFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    public function getNominalTerbayarFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal_terbayar, 0, ',', '.');
    }

    public function getNominalSisaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal_sisa, 0, ',', '.');
    }

    /**
     * Status Badge HTML — mengenali cicilan dari keterangan JSON,
     * bukan dari nilai kolom status.
     */
    public function getStatusBadgeAttribute(): string
    {
        if ($this->status === 'Lunas') {
            return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Lunas</span>';
        }

        if ($this->isCicilan()) {
            return '<span class="badge badge-cicilan"><i class="fas fa-coins"></i> Cicilan ' . $this->porsentase_cicilan . '%</span>';
        }

        if ($this->isTelat()) {
            return '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Belum Lunas (Telat)</span>';
        }

        return '<span class="badge badge-warning"><i class="fas fa-clock"></i> Belum Lunas</span>';
    }

    // ══════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════

    public function isTelat(): bool
    {
        if ($this->status === 'Lunas') return false;
        return Carbon::now()->isAfter($this->batas_bayar);
    }

    // ══════════════════════════════════════════════════════
    // SCOPES
    // ══════════════════════════════════════════════════════

    public function scopeBelumLunas($query)
    {
        return $query->where('status', 'Belum Lunas');
    }

    public function scopeLunas($query)
    {
        return $query->where('status', 'Lunas');
    }

    public function scopeTelat($query)
    {
        return $query->where('status', 'Belum Lunas')
                     ->where('batas_bayar', '<', Carbon::now());
    }

    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    public function scopeBulan($query, $bulan)
    {
        return $query->where('bulan', $bulan);
    }

    public function scopeSearch($query, $search)
    {
        return $query->whereHas('santri', function ($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
              ->orWhere('id_santri', 'like', "%{$search}%")
              ->orWhere('nis', 'like', "%{$search}%");
        })->orWhere('id_pembayaran', 'like', "%{$search}%");
    }
}