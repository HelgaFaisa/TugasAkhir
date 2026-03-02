<?php
// app/Models/Santri.php - Model untuk Data Santri (DENGAN FOTO)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;

    /**
     * Field yang boleh diisi massal (mass assignment)
     */
    protected $fillable = [
        'id_santri',
        'nis',
        'nama_lengkap',
        'jenis_kelamin',
        'status',
        'alamat_santri',
        'daerah_asal',
        'nama_orang_tua',
        'nomor_hp_ortu',
        'rfid_uid',
        'foto',
    ];

    /**
     * Cast attributes ke tipe data tertentu
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Generator ID Kustom (S001, S002, ...)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_santri)) {
                $last = Santri::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_santri, 1)) + 1 : 1;
                $model->id_santri = 'S' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relasi: Santri memiliki banyak akun (santri_accounts)
     */
    public function santriAccount()
    {
        return $this->hasMany(SantriAccount::class, 'id_santri', 'id_santri');
    }

    /**
     * Relasi: Santri memiliki satu User Account (hasOne) - LEGACY
     */
    public function user()
    {
        return $this->hasOne(SantriAccount::class, 'id_santri', 'id_santri')
                    ->where('role', 'santri');
    }

    /**
     * Relasi: Santri memiliki satu akun Wali (orang tua) - LEGACY
     */
    public function waliUser()
    {
        return $this->hasOne(SantriAccount::class, 'id_santri', 'id_santri')
                    ->where('role', 'wali');
    }

    /**
     * Relasi: Santri memiliki banyak data kesehatan
     */
    public function kesehatanSantri()
    {
        return $this->hasMany(KesehatanSantri::class, 'id_santri', 'id_santri');
    }

    /**
     * Relasi: Kesehatan santri yang masih dirawat
     */
    public function kesehatanAktif()
    {
        return $this->hasMany(KesehatanSantri::class, 'id_santri', 'id_santri')
                    ->where('status', 'dirawat');
    }

    /**
     * Relasi: Santri memiliki banyak data kepulangan
     */
    public function kepulangan()
    {
        return $this->hasMany(Kepulangan::class, 'id_santri', 'id_santri');
    }

    /**
     * Relasi: Kepulangan yang sedang aktif
     */
    public function kepulanganAktif()
    {
        return $this->hasMany(Kepulangan::class, 'id_santri', 'id_santri')
                    ->where('status', 'Disetujui')
                    ->whereDate('tanggal_pulang', '<=', now())
                    ->whereDate('tanggal_kembali', '>=', now());
    }

    /**
     * Relasi: Santri memiliki banyak riwayat pelanggaran
     */
    public function riwayatPelanggaran()
    {
        return $this->hasMany(RiwayatPelanggaran::class, 'id_santri', 'id_santri');
    }

    /**
     * Relasi: Santri memiliki banyak pembayaran SPP
     */
    public function pembayaranSpp()
    {
        return $this->hasMany(PembayaranSpp::class, 'id_santri', 'id_santri');
    }

    /**
     * Relasi: SPP yang belum lunas
     */
    public function sppBelumLunas()
    {
        return $this->hasMany(PembayaranSpp::class, 'id_santri', 'id_santri')
                    ->where('status', 'Belum Lunas');
    }

    /**
     * Relasi: SPP yang telat
     */
    public function sppTelat()
    {
        return $this->hasMany(PembayaranSpp::class, 'id_santri', 'id_santri')
                    ->where('status', 'Belum Lunas')
                    ->where('batas_bayar', '<', now());
    }

    /**
     * Relasi: Santri memiliki banyak transaksi uang saku
     */
    public function uangSaku()
    {
        return $this->hasMany(UangSaku::class, 'id_santri', 'id_santri');
    }

    /**
     * Relasi: Santri memiliki banyak absensi kegiatan (BARU)
     */
    public function absensiKegiatans()
    {
        return $this->hasMany(AbsensiKegiatan::class, 'id_santri', 'id_santri');
    }

    /**
     * Accessor: Nama kelompok kelas
     */
    public function getKelompokNameAttribute()
    {
        return $this->kelasPrimary?->kelas?->kelompok?->nama_kelompok ?? '-';
    }

    /**
     * Accessor untuk mendapatkan badge HTML status
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Aktif' => '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Aktif</span>',
            'Lulus' => '<span class="badge badge-info"><i class="fas fa-graduation-cap"></i> Lulus</span>',
            'Khatam' => '<span class="badge badge-primary"><i class="fas fa-award"></i> Khatam</span>',
            'Tidak Aktif' => '<span class="badge badge-secondary"><i class="fas fa-times-circle"></i> Tidak Aktif</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }

    /**
     * Accessor: Total poin pelanggaran
     */
    public function getTotalPoinPelanggaranAttribute()
    {
        return $this->riwayatPelanggaran()->sum('poin');
    }

    /**
     * Accessor: Total tunggakan SPP
     */
    public function getTotalTunggakanAttribute()
    {
        return $this->sppBelumLunas()->sum('nominal');
    }

    /**
     * Accessor: Saldo uang saku terakhir
     */
    public function getSaldoUangSakuAttribute()
    {
        $transaksiTerakhir = $this->uangSaku()
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
        
        return $transaksiTerakhir ? $transaksiTerakhir->saldo_sesudah : 0;
    }

    /**
     * Accessor: Total pemasukan uang saku
     */
    public function getTotalPemasukanUangSakuAttribute()
    {
        return $this->uangSaku()->where('jenis_transaksi', 'pemasukan')->sum('nominal');
    }

    /**
     * Accessor: Total pengeluaran uang saku
     */
    public function getTotalPengeluaranUangSakuAttribute()
    {
        return $this->uangSaku()->where('jenis_transaksi', 'pengeluaran')->sum('nominal');
    }

    /**
     * Accessor: Status RFID (BARU)
     */
    public function getHasRfidAttribute()
    {
        return !empty($this->rfid_uid);
    }

    /**
     * Accessor: Total kehadiran kegiatan (BARU)
     */
    public function getTotalKehadiranAttribute()
    {
        return $this->absensiKegiatans()->where('status', 'Hadir')->count();
    }

    /**
     * Accessor: URL Foto Santri (BARU)
     */
    public function getFotoUrlAttribute()
    {
        if ($this->foto && file_exists(storage_path('app/public/' . $this->foto))) {
            return asset('storage/' . $this->foto);
        }
        
        // Fallback ke gambar default berdasarkan jenis kelamin
        if ($this->jenis_kelamin === 'Perempuan') {
            return asset('images/default-female.png');
        }
        
        return asset('images/default-male.png');
    }

    /**
     * Scope untuk filter santri aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Scope untuk filter santri lulus
     */
    public function scopeLulus($query)
    {
        return $query->where('status', 'Lulus');
    }

    /**
     * Scope untuk filter santri tidak aktif
     */
    public function scopeTidakAktif($query)
    {
        return $query->where('status', 'Tidak Aktif');
    }

    /**
     * Scope untuk filter berdasarkan kelas (santri yang punya kelas ini)
     */
    public function scopeKelas($query, $idKelas)
    {
        return $query->whereHas('kelasSantri', function($q) use ($idKelas) {
            $q->where('id_kelas', $idKelas);
        });
    }

    /**
     * Scope untuk filter berdasarkan kelompok kelas
     */
    public function scopeKelompok($query, $idKelompok)
    {
        return $query->whereHas('kelasSantri', function($q) use ($idKelompok) {
            $q->whereHas('kelas', function($q2) use ($idKelompok) {
                $q2->where('id_kelompok', $idKelompok);
            });
        });
    }

    /**
     * Scope: Filter santri by kelas name (via relational system)
     * Replaces old Santri::where('kelas', $name) queries
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $namaKelas - Nama kelas (e.g., 'PB', 'Lambatan', 'Cepatan')
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKelasByName($query, $namaKelas)
    {
        return $query->whereHas('kelasSantri', function($q) use ($namaKelas) {
            $q->whereHas('kelas', function($q2) use ($namaKelas) {
                $q2->where('nama_kelas', $namaKelas);
            });
        });
    }

    /**
     * Scope: Filter santri by PRIMARY kelas name only
     * Used in dashboard/capaian where only primary class matters
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $namaKelas - Nama kelas (e.g., 'PB', 'Lambatan', 'SMA 12')
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrimaryKelasByName($query, $namaKelas)
    {
        return $query->whereHas('kelasSantri', function($q) use ($namaKelas) {
            $q->where('is_primary', true)
              ->whereHas('kelas', function($q2) use ($namaKelas) {
                  $q2->where('nama_kelas', $namaKelas);
              });
        });
    }

    /**
     * Scope untuk search santri
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
              ->orWhere('nis', 'like', "%{$search}%")
              ->orWhere('id_santri', 'like', "%{$search}%");
        });
    }

    /**
     * Relasi: Santri memiliki banyak capaian
     */
    public function capaian()
    {
        return $this->hasMany(Capaian::class, 'id_santri', 'id_santri');
    }

    // ==========================================
    // RELASI SISTEM KELAS BARU
    // ==========================================

    /**
     * Relasi: Santri memiliki banyak record kelas (hasMany ke santri_kelas)
     */
    public function kelasSantri()
    {
        return $this->hasMany(SantriKelas::class, 'id_santri', 'id_santri');
    }

    /**
     * Relasi: Santri memiliki satu kelas primary (hasOne ke santri_kelas dengan is_primary = true)
     */
    public function kelasPrimary()
    {
        return $this->hasOne(SantriKelas::class, 'id_santri', 'id_santri')
                    ->where('is_primary', true)
                    ->with('kelas');
    }

    /**
     * Relasi: Santri belongs to many Kelas (many-to-many through santri_kelas)
     */
    public function kelasMany()
    {
        return $this->belongsToMany(Kelas::class, 'santri_kelas', 'id_santri', 'id_kelas', 'id_santri', 'id')
                    ->withPivot('tahun_ajaran', 'is_primary')
                    ->withTimestamps();
    }

    /**
     * Get rata-rata capaian per semester
     */
    public function getRataRataCapaianAttribute()
    {
        return $this->capaian()->avg('persentase') ?? 0;
    }

    // ==========================================
    // ACCESSOR SISTEM KELAS BARU
    // ==========================================

    /**
     * Accessor: Get kelas name (primary atau pertama)
     *
     * @return string
     */
    public function getKelasNameAttribute()
    {
        $primary = $this->kelasPrimary;
        if ($primary && $primary->kelas) {
            return $primary->kelas->nama_kelas;
        }

        // Fallback ke kelas pertama jika tidak ada primary
        $first = $this->kelasSantri->first();
        return $first && $first->kelas ? $first->kelas->nama_kelas : 'Belum Ada Kelas';
    }

    /**
     * Accessor: Backward compatible kelas accessor (replaces dropped column)
     * Returns primary kelas name for seamless migration from old system
     *
     * @return string
     */
    public function getKelasAttribute()
    {
        return $this->kelas_name;
    }

    /**
     * Accessor: Get semua kelas sebagai string (untuk display ringkas)
     *
     * @return string
     */
    public function getKelasListStringAttribute()
    {
        $items = $this->kelasSantri
            ->filter(fn($sk) => $sk->kelas && $sk->kelas->kelompok)
            ->map(fn($sk) => $sk->kelas->kelompok->nama_kelompok . ': ' . $sk->kelas->nama_kelas);

        return $items->isNotEmpty() ? $items->implode(', ') : 'Belum Ada Kelas';
    }

    /**
     * Accessor: Get kelas ID dari sistem baru (primary class ID)
     *
     * @return int|null
     */
    public function getPrimaryKelasIdAttribute()
    {
        $kelasPrimary = $this->kelasPrimary;
        return $kelasPrimary ? $kelasPrimary->id_kelas : null;
    }

    // ==========================================
    // HELPER METHODS SISTEM KELAS BARU
    // ==========================================

    /**
     * Check apakah santri ada di kelas tertentu
     *
     * @param int $id_kelas
     * @return bool
     */
    public function hasKelas($id_kelas)
    {
        return $this->kelasMany()->where('kelas.id', $id_kelas)->exists();
    }

    /**
     * Get all kelas santri untuk tahun ajaran tertentu
     *
     * @param string|null $tahun_ajaran - Format: 2024/2025, null untuk tahun ajaran saat ini
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getKelasByTahun($tahun_ajaran = null)
    {
        if ($tahun_ajaran === null) {
            $tahun_ajaran = SantriKelas::getCurrentAcademicYear();
        }
        
        return $this->kelasSantri()
                    ->with('kelas.kelompok')
                    ->where('tahun_ajaran', $tahun_ajaran)
                    ->get();
    }

    /**
     * Assign santri ke kelas baru
     *
     * @param int $id_kelas
     * @param string|null $tahun_ajaran - Format: 2024/2025, null untuk tahun ajaran saat ini
     * @param bool $is_primary - Set sebagai kelas utama
     * @return \App\Models\SantriKelas
     */
    public function assignKelas($id_kelas, $tahun_ajaran = null, $is_primary = false)
    {
        if ($tahun_ajaran === null) {
            $tahun_ajaran = SantriKelas::getCurrentAcademicYear();
        }
        
        // Jika set as primary, unset kelas primary lainnya di tahun ajaran yang sama
        if ($is_primary) {
            $this->kelasSantri()
                 ->where('tahun_ajaran', $tahun_ajaran)
                 ->update(['is_primary' => false]);
        }
        
        // Create or update santri_kelas
        return SantriKelas::updateOrCreate(
            [
                'id_santri' => $this->id_santri,
                'id_kelas' => $id_kelas,
                'tahun_ajaran' => $tahun_ajaran,
            ],
            [
                'is_primary' => $is_primary,
            ]
        );
    }
}