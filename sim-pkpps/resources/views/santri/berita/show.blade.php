{{-- resources/views/santri/berita/show.blade.php --}}
@extends('layouts.app')

@section('title', $berita->judul)

@section('content')
<div class="page-header">
    <h2><i class="fas fa-newspaper"></i> Berita & Pengumuman</h2>
</div>

<div class="content-box" style="padding: 0; overflow: hidden;">

    {{-- ===== GAMBAR HEADER ===== --}}
    @if($berita->gambar)
    <div style="width: 100%; max-height: 340px; overflow: hidden; position: relative;">
        <img src="{{ asset('storage/' . $berita->gambar) }}"
             alt="{{ $berita->judul }}"
             style="width: 100%; height: 340px; object-fit: cover; display: block;">
        {{-- Overlay gradient bawah --}}
        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 120px;
                    background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.55));"></div>
    </div>
    @else
    {{-- Banner tanpa gambar --}}
    <div style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark, #1a4980) 100%);
                height: 90px; position: relative; overflow: hidden;">
        <div style="position: absolute; right: -20px; top: -20px; width: 120px; height: 120px;
                    background: rgba(255,255,255,0.07); border-radius: 50%;"></div>
        <div style="position: absolute; right: 80px; bottom: -30px; width: 80px; height: 80px;
                    background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
    </div>
    @endif

    {{-- ===== BODY KONTEN ===== --}}
    <div style="padding: 26px 28px;">

        {{-- Navigasi atas --}}
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <a href="{{ route('santri.berita.index') }}"
                   style="color: var(--primary-color); text-decoration: none; font-size: 0.88em;">
                    <i class="fas fa-newspaper"></i> Berita
                </a>
                <i class="fas fa-chevron-right" style="color: var(--text-light); font-size: 0.7em;"></i>
                <span style="color: var(--text-light); font-size: 0.88em;">Detail</span>
            </div>
            <a href="{{ route('santri.berita.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- ID & Badge --}}
        <div style="margin-bottom: 12px; display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <span class="badge badge-primary" style="font-size: 0.82em;">{{ $berita->id_berita }}</span>
            <span class="badge badge-success" style="font-size: 0.82em;">
                <i class="fas fa-check-circle"></i> Published
            </span>
        </div>

        {{-- Judul --}}
        <h2 style="margin: 0 0 14px; color: var(--text-color); font-size: 1.5em; line-height: 1.4;">
            {{ $berita->judul }}
        </h2>

        {{-- Meta info --}}
        <div style="display: flex; gap: 18px; color: var(--text-light); font-size: 0.88em;
                    padding-bottom: 18px; border-bottom: 2px solid var(--primary-light); flex-wrap: wrap;">
            <span><i class="fas fa-user" style="color: var(--primary-color);"></i> {{ $berita->penulis }}</span>
            <span><i class="fas fa-calendar" style="color: var(--primary-color);"></i>
                {{ $berita->created_at->isoFormat('dddd, D MMMM YYYY') }}
            </span>
            <span><i class="fas fa-clock" style="color: var(--primary-color);"></i>
                {{ $berita->created_at->format('H:i') }} WIB
            </span>
        </div>

        {{-- ===== KONTEN BERITA ===== --}}
        <div class="berita-body" style="margin-top: 22px; font-size: 1em; line-height: 1.85; color: var(--text-color);">
            {!! $berita->konten !!}
        </div>

        {{-- Footer --}}
        <div style="margin-top: 30px; padding-top: 18px; border-top: 1px solid #eee;
                    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <p style="margin: 0; color: var(--text-light); font-size: 0.85em;">
                <i class="fas fa-clock"></i>
                Terakhir diperbarui: {{ $berita->updated_at->isoFormat('D MMMM YYYY, HH:mm') }} WIB
            </p>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('santri.berita.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-list"></i> Berita Lainnya
                </a>
                <a href="{{ route('santri.dashboard') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Styling konten rich text dari Quill --}}
<style>
.berita-body h1, .berita-body h2, .berita-body h3 {
    color: var(--primary-color);
    margin-top: 20px;
    margin-bottom: 10px;
    line-height: 1.4;
}
.berita-body h1 { font-size: 1.5em; border-bottom: 2px solid var(--primary-light); padding-bottom: 8px; }
.berita-body h2 { font-size: 1.25em; }
.berita-body h3 { font-size: 1.1em; }
.berita-body p  { margin-bottom: 14px; }
.berita-body ul, .berita-body ol {
    margin-left: 24px;
    margin-bottom: 14px;
}
.berita-body li { margin-bottom: 6px; }
.berita-body strong { color: #2c3e50; font-weight: 600; }
.berita-body blockquote {
    border-left: 4px solid var(--primary-color);
    margin: 0 0 14px;
    padding: 10px 16px;
    background: var(--primary-light);
    border-radius: 0 6px 6px 0;
    color: var(--primary-dark, #1a4980);
}
.berita-body img {
    max-width: 100%;
    border-radius: var(--border-radius-sm);
    margin: 10px 0;
}
.berita-body table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 16px;
    font-size: 0.95em;
}
.berita-body table td, .berita-body table th {
    border: 1px solid #dee2e6;
    padding: 9px 12px;
}
.berita-body table th {
    background: var(--primary-light);
    color: var(--primary-color);
    font-weight: 600;
}
</style>
@endsection