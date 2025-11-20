@extends('layouts.app')

@section('title', $berita->judul)

@section('content')
<div class="content-box">
    {{-- Header --}}
    <div class="detail-header">
        <div>
            <h3 style="margin-bottom: 5px;">{{ $berita->judul }}</h3>
            <div style="display: flex; gap: 20px; color: var(--text-light); font-size: 0.9rem; margin-top: 10px;">
                <span><i class="fas fa-user"></i> {{ $berita->penulis }}</span>
                <span><i class="fas fa-calendar"></i> {{ $berita->created_at->format('d F Y, H:i') }} WIB</span>
                <span><i class="fas fa-eye"></i> Sudah dibaca</span>
            </div>
        </div>
        <a href="{{ route('santri.berita.index') }}" class="btn btn-secondary btn-sm hover-lift">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    
    <hr style="border: none; border-top: 2px solid var(--primary-light); margin: 20px 0;">
    
    {{-- Gambar Berita --}}
    @if($berita->gambar)
        <div style="text-align: center; margin: 30px 0;">
            <img src="{{ asset('storage/' . $berita->gambar) }}" 
                 alt="{{ $berita->judul }}" 
                 style="max-width: 100%; max-height: 500px; border-radius: var(--border-radius); box-shadow: var(--shadow-md); object-fit: contain;">
        </div>
    @endif
    
    {{-- Konten Berita --}}
    <div style="font-size: 1rem; line-height: 1.8; color: var(--text-color); margin-top: 25px;">
        {!! nl2br(e($berita->konten)) !!}
    </div>
    
    {{-- Footer --}}
    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--primary-light); text-align: center;">
        <p style="color: var(--text-light); margin: 0;">
            <i class="fas fa-clock"></i> Dipublikasikan pada {{ $berita->created_at->format('d F Y, H:i') }} WIB
        </p>
    </div>
</div>

{{-- Quick Action --}}
<div style="margin-top: 20px; text-align: center;">
    <a href="{{ route('santri.berita.index') }}" class="btn btn-primary hover-lift">
        <i class="fas fa-list"></i> Lihat Berita Lainnya
    </a>
    <a href="{{ route('santri.dashboard') }}" class="btn btn-secondary hover-lift">
        <i class="fas fa-home"></i> Kembali ke Dashboard
    </a>
</div>
@endsection