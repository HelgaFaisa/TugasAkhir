@extends('layouts.app')

@section('title', 'Detail Berita - ' . $berita->id_berita)

@section('content')
<div class="page-header">
    <h2><i class="fas fa-newspaper"></i> Detail Berita</h2>
</div>

<!-- Header Actions -->
<div class="content-box" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
            <span class="badge {{ $berita->status_badge }}" style="font-size: 1em; padding: 8px 15px;">
                @if($berita->status === 'published')
                    <i class="fas fa-check-circle"></i> Published
                @else
                    <i class="fas fa-edit"></i> Draft
                @endif
            </span>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.berita.edit', $berita->id_berita) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.berita.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- Detail Berita -->
<div class="content-box">
    <div style="padding: 10px;">
        <!-- Header Berita -->
        <div style="border-bottom: 3px solid var(--primary-color); padding-bottom: 25px; margin-bottom: 30px;">
            <div style="margin-bottom: 15px;">
                <span style="background: var(--primary-light); color: var(--primary-dark); padding: 6px 12px; border-radius: var(--border-radius-sm); font-weight: 600; font-size: 0.9em;">
                    ID: {{ $berita->id_berita }}
                </span>
            </div>
            
            <h1 style="color: var(--primary-dark); margin-bottom: 20px; font-size: 2em; line-height: 1.3;">
                {{ $berita->judul }}
            </h1>
            
            <div style="display: flex; flex-wrap: wrap; gap: 20px; align-items: center; color: var(--text-light); font-size: 0.95em;">
                <span>
                    <i class="fas fa-user"></i> 
                    <strong>Penulis:</strong> {{ $berita->penulis }}
                </span>
                <span>
                    <i class="fas fa-calendar"></i> 
                    <strong>Tanggal:</strong> {{ $berita->created_at->format('d M Y, H:i') }} WIB
                </span>
                <span>
                    @php
                        $badgeClass = match($berita->target_berita) {
                            'semua' => 'badge-primary',
                            'kelas_tertentu' => 'badge-info',
                            default => 'badge-secondary'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">
                        <i class="fas fa-bullseye"></i> {{ $berita->target_audience }}
                    </span>
                </span>
            </div>
        </div>

        <!-- Gambar Berita -->
        @if($berita->gambar)
        <div style="text-align: center; margin: 40px 0;">
            <img src="{{ asset('storage/' . $berita->gambar) }}" 
                 alt="Gambar Berita" 
                 style="max-width: 100%; max-height: 500px; border-radius: var(--border-radius); box-shadow: var(--shadow-lg); object-fit: cover;">
        </div>
        @endif

        <!-- Konten Berita -->
        <div class="detail-section">
            <h4><i class="fas fa-align-left"></i> Konten Berita</h4>
            <div style="line-height: 1.9; font-size: 1.05em; color: var(--text-color); background: var(--primary-light); padding: 25px; border-radius: var(--border-radius-sm); border-left: 4px solid var(--primary-color);">
                {!! $berita->konten !!}
            </div>
        </div>

        <!-- Info Target Kelas -->
        @if($berita->target_berita === 'kelas_tertentu')
        <div class="detail-section">
            <h4>
                <i class="fas fa-graduation-cap"></i> 
                Target Kelas
            </h4>
            <div style="background: linear-gradient(135deg, #E3F2FD 0%, #D1E9F9 100%); padding: 20px; border-radius: var(--border-radius-sm); border-left: 4px solid var(--info-color);">
                <p style="margin: 0; color: var(--text-color); font-size: 1em;">
                    <i class="fas fa-info-circle"></i>
                    Berita ini ditujukan untuk: 
                    <strong>{{ $berita->target_audience }}</strong>
                </p>
            </div>
        </div>
        @endif

        <!-- Aksi -->
        <div style="border-top: 2px solid var(--primary-light); padding-top: 30px; margin-top: 40px; text-align: center;">
            <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('admin.berita.edit', $berita->id_berita) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Berita
                </a>
                
                <form action="{{ route('admin.berita.destroy', $berita->id_berita) }}" 
                      method="POST" 
                      style="display: inline;"
                      onsubmit="return confirm('Yakin ingin menghapus berita ini? Tindakan ini tidak dapat dibatalkan!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Hapus Berita
                    </button>
                </form>
                
                <a href="{{ route('admin.berita.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Daftar Berita
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
