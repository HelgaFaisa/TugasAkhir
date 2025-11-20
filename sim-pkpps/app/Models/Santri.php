<?php
// app/Models/Santri.php - Model untuk Data Santri (LENGKAP)

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
        'kelas',
        'status',
        'alamat_santri',
        'daerah_asal',
        'nama_orang_tua',
        'nomor_hp_ortu',
        'rfid_uid', // TAMBAHAN BARU
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
     * Relasi: Santri memiliki satu User Account (hasOne)
     */
    public function user()
    {
        return $this->hasOne(User::class, 'role_id', 'id_santri')
                    ->where('role', 'santri'); 
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
     * Relasi: Santri memiliki banyak berita (Many-to-Many)
     */
    public function berita()
    {
        return $this->belongsToMany(Berita::class, 'berita_santri', 'id_santri', 'id_berita', 'id_santri', 'id_berita')
                    ->withPivot('sudah_dibaca', 'tanggal_baca')
                    ->withTimestamps();
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
     * Accessor untuk mendapatkan nama kelas lengkap
     */
    public function getKelasLengkapAttribute()
    {
        $kelasMap = [
            'PB' => 'Pembinaan (PB)',
            'Lambatan' => 'Lambatan',
            'Cepatan' => 'Cepatan',
        ];

        return $kelasMap[$this->kelas] ?? $this->kelas;
    }

    /**
     * Accessor untuk mendapatkan badge HTML status
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Aktif' => '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Aktif</span>',
            'Lulus' => '<span class="badge badge-info"><i class="fas fa-graduation-cap"></i> Lulus</span>',
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
     * Scope untuk filter berdasarkan kelas
     */
    public function scopeKelas($query, $kelas)
    {
        return $query->where('kelas', $kelas);
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

    /**
     * Get rata-rata capaian per semester
     */
    public function getRataRataCapaianAttribute()
    {
        return $this->capaian()->avg('persentase') ?? 0;
    }
}