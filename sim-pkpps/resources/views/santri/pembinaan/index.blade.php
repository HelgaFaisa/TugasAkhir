{{-- resources/views/santri/pembinaan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pembinaan & Sanksi')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-book-open"></i> Pembinaan & Sanksi</h2>
</div>

{{-- ===== HEADER BANNER ===== --}}
<div style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark, #1a4980) 100%);
            color: white; padding: 22px 26px; border-radius: var(--border-radius); margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15); position: relative; overflow: hidden;">
    {{-- Dekorasi lingkaran background --}}
    <div style="position: absolute; right: -30px; top: -30px; width: 130px; height: 130px;
                background: rgba(255,255,255,0.07); border-radius: 50%;"></div>
    <div style="position: absolute; right: 50px; bottom: -40px; width: 90px; height: 90px;
                background: rgba(255,255,255,0.05); border-radius: 50%;"></div>

    <div style="position: relative; z-index: 1;">
        <div style="display: flex; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
            <div style="background: rgba(255,255,255,0.15); padding: 14px 16px; border-radius: 12px; flex-shrink: 0;">
                <i class="fas fa-scroll" style="font-size: 2em;"></i>
            </div>
            <div>
                <h3 style="margin: 0 0 6px; font-size: 1.2em;">Panduan Tata Tertib Pondok</h3>
                <p style="margin: 0; opacity: 0.88; font-size: 0.92em; line-height: 1.6;">
                    Berikut adalah panduan pembinaan dan ketentuan sanksi yang berlaku di pondok.
                    Baca dan pahami setiap poin dengan baik. Ketaatan pada aturan mencerminkan akhlak mulia.
                </p>
                @if($pembinaanList->count() > 0)
                <div style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap;">
                    <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 50px; font-size: 0.85em;">
                        <i class="fas fa-file-alt"></i> {{ $pembinaanList->count() }} dokumen tersedia
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ===== DAFTAR KONTEN ===== --}}
@if($pembinaanList->count() > 0)
    <div style="display: grid; gap: 14px;">
        @foreach($pembinaanList as $index => $item)
        <div style="background: white; border: 1px solid #e8eaed; border-radius: var(--border-radius);
                    transition: box-shadow 0.2s, transform 0.2s; overflow: hidden;"
             onmouseover="this.style.boxShadow='0 4px 20px rgba(0,0,0,0.1)'; this.style.transform='translateY(-1px)';"
             onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)';">

            <a href="{{ route('santri.pembinaan.show', $item->id_pembinaan) }}"
               style="display: flex; align-items: stretch; text-decoration: none; color: inherit;">

                {{-- Nomor urut --}}
                <div style="background: var(--primary-light); color: var(--primary-color);
                            padding: 0 20px; display: flex; align-items: center; justify-content: center;
                            min-width: 64px; font-size: 1.4em; font-weight: 800; flex-shrink: 0;">
                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                </div>

                {{-- Konten --}}
                <div style="padding: 16px 20px; flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 0;">
                            <h4 style="margin: 0 0 6px; color: var(--primary-color); font-size: 1.05em;
                                       white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $item->judul }}
                            </h4>
                            <p style="margin: 0; color: var(--text-light); font-size: 0.88em; line-height: 1.5;
                                      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ \Illuminate\Support\Str::limit(strip_tags($item->konten), 140) }}
                            </p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                            <div style="text-align: right;">
                                <span class="badge badge-primary" style="font-size: 0.78em; display: block; margin-bottom: 4px;">
                                    {{ $item->id_pembinaan }}
                                </span>
                                <span style="color: var(--text-light); font-size: 0.78em;">
                                    <i class="fas fa-clock"></i>
                                    {{ $item->updated_at->diffForHumans() }}
                                </span>
                            </div>
                            <i class="fas fa-chevron-right" style="color: var(--primary-color); opacity: 0.5; font-size: 1.1em;"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    {{-- Info Footer --}}
    <div class="info-box" style="margin-top: 20px;">
        <p style="margin: 0;">
            <i class="fas fa-info-circle"></i>
            Jika ada pertanyaan tentang isi tata tertib ini, silakan hubungi pengurus atau bagian administrasi pondok.
        </p>
    </div>

@else
    <div class="content-box">
        <div class="empty-state">
            <i class="fas fa-book-open" style="color: var(--primary-color); opacity: 0.4;"></i>
            <h3>Belum Ada Konten</h3>
            <p>Panduan pembinaan & sanksi belum tersedia. Silakan hubungi pengurus pondok untuk informasi lebih lanjut.</p>
        </div>
    </div>
@endif
@endsection