<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'berita';

    protected $fillable = [
        'id_berita',
        'judul',
        'konten',
        'penulis',
        'gambar',
        'status',
        'target_berita',
        'target_kelas',
    ];

    protected $casts = [
        'target_kelas' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Auto-generate ID Berita (B001, B002, ...)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_berita)) {
                $last = Berita::orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->id_berita, 1)) + 1 : 1;
                $model->id_berita = 'B' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relasi: Kelas yang ditargetkan (via JSON target_kelas berisi id kelas)
     */
    public function kelasTertentu()
    {
        return Kelas::whereIn('id', $this->target_kelas ?? [])->get();
    }

    /**
     * Accessor: Tanggal Formatted
     */
    public function getTanggalFormattedAttribute()
    {
        return $this->created_at->format('d M Y');
    }

    /**
     * Accessor: Status Badge
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'published' ? 'badge-success' : 'badge-warning';
    }

    /**
     * Accessor: Target Audience (untuk display)
     */
    public function getTargetAudienceAttribute()
    {
        if ($this->target_berita === 'kelas_tertentu') {
            $namaKelas = Kelas::whereIn('id', $this->target_kelas ?? [])
                ->pluck('nama_kelas')->toArray();
            return 'Kelas: ' . (count($namaKelas) ? implode(', ', $namaKelas) : '-');
        }

        return match($this->target_berita) {
            'semua' => 'Semua Santri',
            default => '-'
        };
    }

    /**
     * Scope: Filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by target
     */
    public function scopeTarget($query, $target)
    {
        return $query->where('target_berita', $target);
    }

    /**
     * Scope: Search berita
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('penulis', 'like', "%{$search}%")
              ->orWhere('id_berita', 'like', "%{$search}%");
        });
    }
}