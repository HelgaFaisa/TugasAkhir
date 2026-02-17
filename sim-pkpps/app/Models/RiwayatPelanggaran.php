<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RiwayatPelanggaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_riwayat',
        'id_santri',
        'id_kategori',
        'tanggal',
        'poin',
        'poin_asli',
        'keterangan',
        'is_kafaroh_selesai',
        'tanggal_kafaroh_selesai',
        'admin_kafaroh_id',
        'catatan_kafaroh',
        'is_published_to_parent',
        'tanggal_published',
        'admin_published_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'poin' => 'integer',
        'poin_asli' => 'integer',
        'is_kafaroh_selesai' => 'boolean',
        'is_published_to_parent' => 'boolean',
        'tanggal_kafaroh_selesai' => 'datetime',
        'tanggal_published' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_riwayat)) {
                $last = RiwayatPelanggaran::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_riwayat, 1)) + 1 : 1;
                $model->id_riwayat = 'P' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
            
            // Set poin_asli = poin saat pertama kali dibuat
            if (empty($model->poin_asli)) {
                $model->poin_asli = $model->poin;
            }
        });
    }

    // Relasi
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriPelanggaran::class, 'id_kategori', 'id_kategori');
    }

    public function adminKafaroh()
    {
        return $this->belongsTo(User::class, 'admin_kafaroh_id');
    }

    public function adminPublished()
    {
        return $this->belongsTo(User::class, 'admin_published_id');
    }

    // Scopes
    public function scopeBySantri($query, $idSantri)
    {
        return $query->where('id_santri', $idSantri);
    }

    public function scopeByKategori($query, $idKategori)
    {
        return $query->where('id_kategori', $idKategori);
    }

    public function scopeByTanggal($query, $tanggalMulai, $tanggalSelesai = null)
    {
        if ($tanggalSelesai) {
            return $query->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);
        }
        return $query->whereDate('tanggal', $tanggalMulai);
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal', Carbon::now()->month)
                     ->whereYear('tanggal', Carbon::now()->year);
    }

    public function scopeTerbaru($query)
    {
        return $query->orderBy('tanggal', 'desc')
                     ->orderBy('created_at', 'desc');
    }

    public function scopeKafarohSelesai($query)
    {
        return $query->where('is_kafaroh_selesai', true);
    }

    public function scopeKafarohBelumSelesai($query)
    {
        return $query->where('is_kafaroh_selesai', false);
    }

    public function scopePublishedToParent($query)
    {
        return $query->where('is_published_to_parent', true);
    }

    public function scopeNotPublishedToParent($query)
    {
        return $query->where('is_published_to_parent', false);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('id_riwayat', 'like', "%{$search}%")
              ->orWhere('keterangan', 'like', "%{$search}%")
              ->orWhereHas('santri', function($sq) use ($search) {
                  $sq->where('nama_lengkap', 'like', "%{$search}%");
              })
              ->orWhereHas('kategori', function($sq) use ($search) {
                  $sq->where('nama_pelanggaran', 'like', "%{$search}%");
              });
        });
    }

    // Accessor
    public function getTanggalFormatAttribute()
    {
        return Carbon::parse($this->tanggal)->isoFormat('D MMMM YYYY');
    }

    public function getStatusKafarohAttribute()
    {
        return $this->is_kafaroh_selesai ? 'Selesai' : 'Belum Selesai';
    }

    public function getStatusPublishAttribute()
    {
        return $this->is_published_to_parent ? 'Terkirim' : 'Belum Terkirim';
    }
}