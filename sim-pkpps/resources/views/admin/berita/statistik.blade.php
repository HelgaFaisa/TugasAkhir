@extends('layouts.app')

@section('title', 'Statistik Berita')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-chart-bar"></i> Statistik Berita</h2>
</div>

<!-- Back Button -->
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.berita.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Berita
    </a>
</div>

<!-- Dashboard Cards -->
<div class="row-cards">
    <div class="card card-info">
        <h3>Total Berita</h3>
        <div class="card-value">{{ $totalBerita }}</div>
        <i class="fas fa-newspaper card-icon"></i>
    </div>
    
    <div class="card card-success">
        <h3>Published</h3>
        <div class="card-value">{{ $totalPublished }}</div>
        <i class="fas fa-check-circle card-icon"></i>
    </div>
    
    <div class="card card-warning">
        <h3>Draft</h3>
        <div class="card-value">{{ $totalDraft }}</div>
        <i class="fas fa-edit card-icon"></i>
    </div>
    
    <div class="card card-primary">
        <h3>Semua Santri</h3>
        <div class="card-value">{{ $beritaSemua }}</div>
        <i class="fas fa-globe card-icon"></i>
    </div>
    
    <div class="card card-secondary">
        <h3>Kelas Tertentu</h3>
        <div class="card-value">{{ $beritaKelas }}</div>
        <i class="fas fa-graduation-cap card-icon"></i>
    </div>
</div>

<!-- Grafik Distribusi -->
<div class="content-box" style="margin-top: 30px;">
    <h3 style="color: var(--primary-color); margin-bottom: 25px; display: flex; align-items: center;">
        <i class="fas fa-chart-pie" style="margin-right: 10px;"></i>
        Distribusi Berita
    </h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px;">
        <!-- Status Distribution -->
        <div style="background: linear-gradient(135deg, #F8FBF9 0%, #FFFFFF 100%); padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow-sm); border: 2px solid var(--primary-light);">
            <h4 style="margin-bottom: 20px; color: var(--primary-dark); display: flex; align-items: center;">
                <i class="fas fa-toggle-on" style="margin-right: 8px;"></i>
                Berdasarkan Status
            </h4>
            
            @php
                $totalForPercentage = max($totalBerita, 1);
                $publishedPercent = round(($totalPublished / $totalForPercentage) * 100, 1);
                $draftPercent = round(($totalDraft / $totalForPercentage) * 100, 1);
            @endphp
            
            <!-- Published -->
            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; align-items: center;">
                    <span style="font-weight: 600; color: var(--text-color);">
                        <i class="fas fa-check-circle" style="color: var(--success-color);"></i> Published
                    </span>
                    <span style="font-weight: 700; color: var(--success-color); font-size: 1.1em;">
                        {{ $publishedPercent }}%
                    </span>
                </div>
                <div style="background-color: #E8F7F2; border-radius: 20px; height: 12px; overflow: hidden;">
                    <div style="background: linear-gradient(90deg, var(--success-color), #5EA98C); width: {{ $publishedPercent }}%; height: 100%; border-radius: 20px; transition: width 0.5s ease;"></div>
                </div>
                <small style="color: var(--text-light); margin-top: 5px; display: block;">
                    {{ $totalPublished }} dari {{ $totalBerita }} berita
                </small>
            </div>
            
            <!-- Draft -->
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; align-items: center;">
                    <span style="font-weight: 600; color: var(--text-color);">
                        <i class="fas fa-edit" style="color: var(--warning-color);"></i> Draft
                    </span>
                    <span style="font-weight: 700; color: #E6B85C; font-size: 1.1em;">
                        {{ $draftPercent }}%
                    </span>
                </div>
                <div style="background-color: #FFF8E1; border-radius: 20px; height: 12px; overflow: hidden;">
                    <div style="background: linear-gradient(90deg, var(--warning-color), #FFAB91); width: {{ $draftPercent }}%; height: 100%; border-radius: 20px; transition: width 0.5s ease;"></div>
                </div>
                <small style="color: var(--text-light); margin-top: 5px; display: block;">
                    {{ $totalDraft }} dari {{ $totalBerita }} berita
                </small>
            </div>
        </div>
        
        <!-- Target Distribution -->
        <div style="background: linear-gradient(135deg, #F8FBF9 0%, #FFFFFF 100%); padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow-sm); border: 2px solid var(--primary-light);">
            <h4 style="margin-bottom: 20px; color: var(--primary-dark); display: flex; align-items: center;">
                <i class="fas fa-bullseye" style="margin-right: 8px;"></i>
                Berdasarkan Target
            </h4>
            
            @php
                $semuaPercent = round(($beritaSemua / $totalForPercentage) * 100, 1);
                $kelasPercent = round(($beritaKelas / $totalForPercentage) * 100, 1);
            @endphp
            
            <!-- Semua Santri -->
            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; align-items: center;">
                    <span style="font-weight: 600; color: var(--text-color);">
                        <i class="fas fa-globe" style="color: var(--info-color);"></i> Semua Santri
                    </span>
                    <span style="font-weight: 700; color: var(--info-color); font-size: 1.1em;">
                        {{ $semuaPercent }}%
                    </span>
                </div>
                <div style="background-color: #E3F2FD; border-radius: 20px; height: 12px; overflow: hidden;">
                    <div style="background: linear-gradient(90deg, var(--info-color), #5EAED4); width: {{ $semuaPercent }}%; height: 100%; border-radius: 20px; transition: width 0.5s ease;"></div>
                </div>
                <small style="color: var(--text-light); margin-top: 5px; display: block;">
                    {{ $beritaSemua }} dari {{ $totalBerita }} berita
                </small>
            </div>
            
            <!-- Kelas Tertentu -->
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; align-items: center;">
                    <span style="font-weight: 600; color: var(--text-color);">
                        <i class="fas fa-graduation-cap" style="color: var(--secondary-color);"></i> Kelas Tertentu
                    </span>
                    <span style="font-weight: 700; color: var(--secondary-color); font-size: 1.1em;">
                        {{ $kelasPercent }}%
                    </span>
                </div>
                <div style="background-color: #FFE8EA; border-radius: 20px; height: 12px; overflow: hidden;">
                    <div style="background: linear-gradient(90deg, var(--secondary-color), #FF6B7A); width: {{ $kelasPercent }}%; height: 100%; border-radius: 20px; transition: width 0.5s ease;"></div>
                </div>
                <small style="color: var(--text-light); margin-top: 5px; display: block;">
                    {{ $beritaKelas }} dari {{ $totalBerita }} berita
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="content-box" style="margin-top: 30px;">
    <h3 style="color: var(--primary-color); margin-bottom: 20px; display: flex; align-items: center;">
        <i class="fas fa-bolt" style="margin-right: 10px;"></i>
        Aksi Cepat
    </h3>
    
    <div style="display: flex; flex-wrap: wrap; gap: 12px;">
        <a href="{{ route('admin.berita.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Berita Baru
        </a>
        
        <a href="{{ route('admin.berita.index') }}?status=draft" class="btn btn-warning">
            <i class="fas fa-edit"></i> Lihat Draft ({{ $totalDraft }})
        </a>
        
        <a href="{{ route('admin.berita.index') }}?status=published" class="btn btn-primary">
            <i class="fas fa-eye"></i> Lihat Published ({{ $totalPublished }})
        </a>
        
        <a href="{{ route('admin.berita.index') }}?target=kelas_tertentu" class="btn btn-secondary">
            <i class="fas fa-graduation-cap"></i> Berita Kelas Tertentu ({{ $beritaKelas }})
        </a>
    </div>
</div>

<!-- Empty State -->
@if($totalBerita == 0)
    <div class="content-box" style="margin-top: 30px; text-align: center; padding: 60px 20px;">
        <i class="fas fa-newspaper" style="font-size: 5em; color: #ccc; margin-bottom: 25px;"></i>
        <h3 style="color: var(--text-light); margin-bottom: 15px;">Belum Ada Berita</h3>
        <p style="color: var(--text-light); margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto;">
            Mulai dengan membuat berita pertama untuk pesantren Anda. Berita dapat dipublikasikan untuk semua santri atau target tertentu.
        </p>
        <a href="{{ route('admin.berita.create') }}" class="btn btn-success btn-lg">
            <i class="fas fa-plus"></i> Buat Berita Pertama
        </a>
    </div>
@endif
@endsection