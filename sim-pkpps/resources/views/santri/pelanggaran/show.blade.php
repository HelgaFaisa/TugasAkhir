{{-- resources/views/santri/pelanggaran/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Riwayat Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-file-alt"></i> Detail Riwayat Pelanggaran</h2>
</div>

@php
    $poin = $riwayatPelanggaran->poin;
    $isKafarohSelesai = $riwayatPelanggaran->is_kafaroh_selesai ?? false;

    if ($poin == 0 && $isKafarohSelesai) {
        $bannerBg    = 'linear-gradient(135deg, #28a745 0%, #1e7e34 100%)';
        $bannerIcon  = 'fas fa-check-double';
        $bannerLabel = 'Kafaroh Selesai — Poin Telah Dilebur';
        $bannerColor = 'white';
    } elseif ($poin >= 20) {
        $bannerBg    = 'linear-gradient(135deg, #dc3545 0%, #a71d2a 100%)';
        $bannerIcon  = 'fas fa-exclamation-triangle';
        $bannerLabel = 'Pelanggaran Berat';
        $bannerColor = 'white';
    } elseif ($poin >= 10) {
        $bannerBg    = 'linear-gradient(135deg, #e67e22 0%, #ca6f1e 100%)';
        $bannerIcon  = 'fas fa-exclamation-circle';
        $bannerLabel = 'Pelanggaran Sedang';
        $bannerColor = 'white';
    } else {
        $bannerBg    = 'linear-gradient(135deg, #ffc107 0%, #e0a800 100%)';
        $bannerIcon  = 'fas fa-info-circle';
        $bannerLabel = 'Pelanggaran Ringan';
        $bannerColor = '#333';
    }
@endphp

{{-- ===== BANNER STATUS ===== --}}
<div style="background: {{ $bannerBg }}; color: {{ $bannerColor }}; padding: 18px 22px; border-radius: var(--border-radius); margin-bottom: 18px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
    <div style="display: flex; align-items: center; gap: 14px;">
        <i class="{{ $bannerIcon }}" style="font-size: 2.2em; opacity: 0.9;"></i>
        <div>
            <div style="font-size: 0.85em; opacity: 0.85; margin-bottom: 2px;">ID Riwayat: {{ $riwayatPelanggaran->id_riwayat }}</div>
            <strong style="font-size: 1.2em;">{{ $bannerLabel }}</strong>
        </div>
    </div>
    <div style="text-align: right;">
        <div style="opacity: 0.85; font-size: 0.85em;">Poin Pelanggaran</div>
        <div style="font-size: 2.2em; font-weight: 900; line-height: 1.1;">{{ $poin }}</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 340px; gap: 18px; align-items: start;">

    {{-- ===== KOLOM KIRI: DETAIL ===== --}}
    <div>
        {{-- Detail Informasi --}}
        <div class="content-box" style="margin-bottom: 0;">
            <div class="detail-header">
                <h3 style="display: flex; align-items: center; gap: 10px;">
                    <span style="background: var(--primary-light); color: var(--primary-color); padding: 6px 12px; border-radius: 50px; font-size: 0.9em;">
                        <i class="fas fa-info-circle"></i> Informasi Pelanggaran
                    </span>
                </h3>
                <a href="{{ route('santri.pelanggaran.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <table class="detail-table">
                <tr>
                    <th style="width: 40%;"><i class="fas fa-hashtag"></i> ID Riwayat</th>
                    <td>
                        <span class="badge badge-secondary" style="font-size: 0.95em;">
                            {{ $riwayatPelanggaran->id_riwayat }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-calendar-day"></i> Tanggal Kejadian</th>
                    <td>
                        <strong>{{ \Carbon\Carbon::parse($riwayatPelanggaran->tanggal)->isoFormat('dddd, D MMMM YYYY') }}</strong>
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-tag"></i> Kode Kategori</th>
                    <td>
                        @if($riwayatPelanggaran->kategori)
                            <span class="badge badge-primary">{{ $riwayatPelanggaran->kategori->id_kategori }}</span>
                        @else
                            <span style="color: var(--text-light);">—</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-list-alt"></i> Jenis Pelanggaran</th>
                    <td>
                        <strong>{{ $riwayatPelanggaran->kategori->nama_pelanggaran ?? '—' }}</strong>
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-fire"></i> Poin Pelanggaran</th>
                    <td>
                        @if($poin == 0 && $isKafarohSelesai)
                            <span class="badge badge-success">
                                <i class="fas fa-check"></i> 0 Poin (Sudah dilebur)
                            </span>
                        @else
                            <span class="badge badge-danger badge-lg" style="font-size: 1em; padding: 6px 14px;">
                                <i class="fas fa-fire"></i> {{ $poin }} Poin
                            </span>
                        @endif
                    </td>
                </tr>

                @if(isset($riwayatPelanggaran->poin_asli) && $riwayatPelanggaran->poin_asli != $poin)
                <tr>
                    <th><i class="fas fa-history"></i> Poin Awal</th>
                    <td>
                        <span style="text-decoration: line-through; color: var(--text-light);">
                            {{ $riwayatPelanggaran->poin_asli }} Poin
                        </span>
                        <span class="badge badge-success" style="margin-left: 8px; font-size: 0.8em;">Dilebur</span>
                    </td>
                </tr>
                @endif

                {{-- Status Kafaroh --}}
                @if(isset($riwayatPelanggaran->is_kafaroh_selesai))
                <tr>
                    <th><i class="fas fa-hands"></i> Status Kafaroh</th>
                    <td>
                        @if($isKafarohSelesai)
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle"></i> Selesai
                            </span>
                            @if($riwayatPelanggaran->tanggal_kafaroh_selesai)
                            <br><small style="color: var(--text-light);">
                                {{ \Carbon\Carbon::parse($riwayatPelanggaran->tanggal_kafaroh_selesai)->isoFormat('D MMMM YYYY') }}
                            </small>
                            @endif
                        @else
                            <span class="badge badge-warning">
                                <i class="fas fa-clock"></i> Belum Selesai
                            </span>
                        @endif
                    </td>
                </tr>
                @endif

                {{-- Kafaroh (jika ada) --}}
                @if($riwayatPelanggaran->kategori && $riwayatPelanggaran->kategori->kafaroh ?? false)
                <tr>
                    <th style="vertical-align: top;"><i class="fas fa-book-open"></i> Kafaroh</th>
                    <td>
                        <div style="background: #fff3cd; border-left: 4px solid var(--warning-color); padding: 12px 14px; border-radius: 0 var(--border-radius-sm) var(--border-radius-sm) 0; font-size: 0.9em;">
                            {{ $riwayatPelanggaran->kategori->kafaroh }}
                        </div>
                    </td>
                </tr>
                @endif

                @if($riwayatPelanggaran->catatan_kafaroh ?? false)
                <tr>
                    <th style="vertical-align: top;"><i class="fas fa-sticky-note"></i> Catatan Kafaroh</th>
                    <td>
                        <div style="background: #d1ecf1; padding: 12px 14px; border-radius: var(--border-radius-sm); font-size: 0.9em;">
                            {{ $riwayatPelanggaran->catatan_kafaroh }}
                        </div>
                    </td>
                </tr>
                @endif

                <tr>
                    <th style="vertical-align: top;"><i class="fas fa-comment-alt"></i> Keterangan</th>
                    <td>
                        @if($riwayatPelanggaran->keterangan)
                            <div style="padding: 10px 14px; background: #f8f9fa; border-radius: var(--border-radius-sm); font-size: 0.9em;">
                                {{ $riwayatPelanggaran->keterangan }}
                            </div>
                        @else
                            <span style="color: var(--text-light); font-style: italic;">Tidak ada keterangan</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-clock"></i> Dicatat Pada</th>
                    <td style="color: var(--text-light); font-size: 0.9em;">
                        {{ $riwayatPelanggaran->created_at->isoFormat('D MMMM YYYY, HH:mm') }} WIB
                    </td>
                </tr>
            </table>

            {{-- Info Box --}}
            <div class="info-box" style="margin-top: 16px;">
                <p style="margin: 0;">
                    <i class="fas fa-info-circle"></i>
                    <strong>Catatan:</strong> Data ini dicatat oleh pengurus pondok.
                    Jika ada ketidaksesuaian, silakan hubungi bagian administrasi.
                </p>
            </div>
        </div>
    </div>

    {{-- ===== KOLOM KANAN: SIDEBAR ===== --}}
    <div style="display: flex; flex-direction: column; gap: 16px;">

        {{-- Card Poin Detail --}}
        <div style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark, #1a4980) 100%);
                    color: white; padding: 20px; border-radius: var(--border-radius); box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
            <h4 style="margin: 0 0 14px; opacity: 0.9; font-size: 0.95em;">
                <i class="fas fa-fire"></i> Informasi Poin
            </h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div style="background: rgba(255,255,255,0.15); padding: 12px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 1.8em; font-weight: 800;">{{ $poin }}</div>
                    <div style="opacity: 0.8; font-size: 0.8em;">Poin Saat Ini</div>
                </div>
                <div style="background: rgba(255,255,255,0.15); padding: 12px; border-radius: 8px; text-align: center;">
                    <div style="font-size: 1.8em; font-weight: 800;">{{ $riwayatPelanggaran->poin_asli ?? $poin }}</div>
                    <div style="opacity: 0.8; font-size: 0.8em;">Poin Awal</div>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.15); padding: 10px 14px; border-radius: 8px; font-size: 0.85em; opacity: 0.9;">
                <i class="fas fa-calendar-day"></i>
                Kejadian: <strong>{{ \Carbon\Carbon::parse($riwayatPelanggaran->tanggal)->isoFormat('D MMM YYYY') }}</strong>
            </div>
        </div>

        {{-- Status Kafaroh Card --}}
        @if(isset($riwayatPelanggaran->is_kafaroh_selesai))
        <div style="background: white; border: 2px solid {{ $isKafarohSelesai ? 'var(--success-color)' : 'var(--warning-color)' }};
                    padding: 16px; border-radius: var(--border-radius);">
            <h4 style="margin: 0 0 10px; color: {{ $isKafarohSelesai ? 'var(--success-color)' : '#856404' }}; font-size: 0.95em;">
                <i class="fas fa-hands"></i> Status Kafaroh
            </h4>
            @if($isKafarohSelesai)
                <div style="display: flex; align-items: center; gap: 10px; color: var(--success-color);">
                    <i class="fas fa-check-circle" style="font-size: 1.5em;"></i>
                    <div>
                        <strong>Kafaroh Selesai</strong><br>
                        <small style="color: var(--text-light);">Poin telah dilebur menjadi 0</small>
                    </div>
                </div>
            @else
                <div style="display: flex; align-items: center; gap: 10px; color: #856404;">
                    <i class="fas fa-clock" style="font-size: 1.5em;"></i>
                    <div>
                        <strong>Belum Selesai</strong><br>
                        <small style="color: var(--text-light);">Segera tuntaskan kafaroh Anda</small>
                    </div>
                </div>
                @if($riwayatPelanggaran->kategori && ($riwayatPelanggaran->kategori->kafaroh ?? false))
                <div style="margin-top: 10px; background: #fff3cd; padding: 10px 12px; border-radius: 6px; font-size: 0.85em; color: #856404;">
                    <strong><i class="fas fa-book-open"></i> Kafaroh:</strong><br>
                    {{ $riwayatPelanggaran->kategori->kafaroh }}
                </div>
                @endif
            @endif
        </div>
        @endif

        {{-- Tombol Navigasi --}}
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <a href="{{ route('santri.pelanggaran.index') }}" class="btn btn-secondary" style="text-align: center;">
                <i class="fas fa-list"></i> Semua Riwayat
            </a>
            <a href="{{ route('santri.pelanggaran.kategori') }}" class="btn btn-warning" style="text-align: center;">
                <i class="fas fa-list-ul"></i> Daftar Kategori & Poin
            </a>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    div[style*="grid-template-columns: 1fr 340px"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection