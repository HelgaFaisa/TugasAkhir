{{-- resources/views/santri/pembinaan/show.blade.php --}}
@extends('layouts.app')

@section('title', $pembinaan->judul)

@section('content')
<div class="page-header">
    <h2><i class="fas fa-book-open"></i> Pembinaan & Sanksi</h2>
</div>

<div style="display: grid; grid-template-columns: 1fr 280px; gap: 18px; align-items: start;">

    {{-- ===== KOLOM KIRI: KONTEN UTAMA ===== --}}
    <div>
        {{-- Header dokumen --}}
        <div style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark, #1a4980) 100%);
                    color: white; padding: 20px 24px; border-radius: var(--border-radius) var(--border-radius) 0 0;
                    display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="background: rgba(255,255,255,0.15); padding: 10px 13px; border-radius: 10px; flex-shrink: 0;">
                    <i class="fas fa-file-alt" style="font-size: 1.4em;"></i>
                </div>
                <div>
                    <div style="opacity: 0.8; font-size: 0.82em; margin-bottom: 3px;">
                        <span class="badge" style="background: rgba(255,255,255,0.25); color: white; font-size: 0.9em;">
                            {{ $pembinaan->id_pembinaan }}
                        </span>
                    </div>
                    <h3 style="margin: 0; font-size: 1.1em; line-height: 1.3;">{{ $pembinaan->judul }}</h3>
                </div>
            </div>
            <a href="{{ route('santri.pembinaan.index') }}"
               style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4);
                      color: white; padding: 7px 14px; border-radius: 6px; text-decoration: none;
                      font-size: 0.88em; white-space: nowrap; transition: background 0.2s;"
               onmouseover="this.style.background='rgba(255,255,255,0.3)'"
               onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-list"></i> Semua Dokumen
            </a>
        </div>

        {{-- Konten dokumen --}}
        <div class="content-box" style="border-radius: 0 0 var(--border-radius) var(--border-radius); border-top: none; margin-top: 0;">
            <div style="color: var(--text-light); font-size: 0.82em; margin-bottom: 18px;
                        padding-bottom: 12px; border-bottom: 1px solid #eee;
                        display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <span><i class="fas fa-clock"></i> Terakhir diperbarui: {{ $pembinaan->updated_at->isoFormat('D MMMM YYYY') }}</span>
            </div>

            {{-- Isi konten (HTML dari rich text editor) --}}
            <div class="pembinaan-content" style="line-height: 1.85; font-size: 0.97em; color: var(--text-color);">
                {!! $pembinaan->konten !!}
            </div>

            {{-- Navigasi Prev / Next --}}
            <div style="margin-top: 28px; padding-top: 18px; border-top: 1px solid #eee;
                        display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                @if($prev)
                <a href="{{ route('santri.pembinaan.show', $prev->id_pembinaan) }}"
                   style="display: flex; align-items: center; gap: 10px; padding: 12px 16px;
                          background: var(--primary-light); color: var(--primary-color);
                          border-radius: var(--border-radius-sm); text-decoration: none;
                          flex: 1; min-width: 0; transition: background 0.2s;"
                   onmouseover="this.style.background='#d0e4f7'"
                   onmouseout="this.style.background='var(--primary-light)'">
                    <i class="fas fa-arrow-left" style="flex-shrink: 0;"></i>
                    <div style="min-width: 0;">
                        <div style="font-size: 0.78em; opacity: 0.7; margin-bottom: 2px;">Sebelumnya</div>
                        <div style="font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $prev->judul }}
                        </div>
                    </div>
                </a>
                @else
                <div style="flex: 1;"></div>
                @endif

                @if($next)
                <a href="{{ route('santri.pembinaan.show', $next->id_pembinaan) }}"
                   style="display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 12px 16px;
                          background: var(--primary-light); color: var(--primary-color);
                          border-radius: var(--border-radius-sm); text-decoration: none;
                          flex: 1; min-width: 0; text-align: right; transition: background 0.2s;"
                   onmouseover="this.style.background='#d0e4f7'"
                   onmouseout="this.style.background='var(--primary-light)'">
                    <div style="min-width: 0;">
                        <div style="font-size: 0.78em; opacity: 0.7; margin-bottom: 2px;">Selanjutnya</div>
                        <div style="font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $next->judul }}
                        </div>
                    </div>
                    <i class="fas fa-arrow-right" style="flex-shrink: 0;"></i>
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== KOLOM KANAN: SIDEBAR ===== --}}
    <div style="display: flex; flex-direction: column; gap: 14px;">

        {{-- Daftar isi --}}
        <div class="content-box" style="padding: 0; overflow: hidden;">
            <div style="background: var(--primary-color); color: white; padding: 12px 16px; font-weight: 600; font-size: 0.92em;">
                <i class="fas fa-list-ul"></i> Daftar Dokumen
            </div>
            <div style="max-height: 400px; overflow-y: auto;">
                @foreach($pembinaanList as $index => $item)
                <a href="{{ route('santri.pembinaan.show', $item->id_pembinaan) }}"
                   style="display: flex; align-items: center; gap: 10px; padding: 11px 16px;
                          text-decoration: none; border-bottom: 1px solid #f0f0f0;
                          background: {{ $item->id_pembinaan === $pembinaan->id_pembinaan ? 'var(--primary-light)' : 'white' }};
                          color: {{ $item->id_pembinaan === $pembinaan->id_pembinaan ? 'var(--primary-color)' : 'var(--text-color)' }};
                          transition: background 0.15s;"
                   onmouseover="if(this.style.background !== 'var(--primary-light)') this.style.background='#f8f9fa'"
                   onmouseout="if('{{ $item->id_pembinaan }}' !== '{{ $pembinaan->id_pembinaan }}') this.style.background='white'">
                    <span style="background: {{ $item->id_pembinaan === $pembinaan->id_pembinaan ? 'var(--primary-color)' : '#e9ecef' }};
                                 color: {{ $item->id_pembinaan === $pembinaan->id_pembinaan ? 'white' : 'var(--text-light)' }};
                                 width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center;
                                 justify-content: center; font-size: 0.75em; font-weight: 700; flex-shrink: 0;">
                        {{ $index + 1 }}
                    </span>
                    <span style="font-size: 0.88em; line-height: 1.3;
                                 display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
                                 font-weight: {{ $item->id_pembinaan === $pembinaan->id_pembinaan ? '600' : 'normal' }};">
                        {{ $item->judul }}
                    </span>
                    @if($item->id_pembinaan === $pembinaan->id_pembinaan)
                    <i class="fas fa-bookmark" style="color: var(--primary-color); flex-shrink: 0; font-size: 0.85em;"></i>
                    @endif
                </a>
                @endforeach
            </div>
        </div>

        {{-- Info box --}}
        <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: var(--border-radius-sm); padding: 14px;">
            <h5 style="margin: 0 0 8px; color: #856404; font-size: 0.9em;">
                <i class="fas fa-lightbulb"></i> Ingat Selalu
            </h5>
            <p style="margin: 0; color: #856404; font-size: 0.84em; line-height: 1.5;">
                Mematuhi tata tertib adalah bentuk tanggung jawab dan cerminan akhlak mulia seorang santri.
                Jika ada yang tidak dipahami, segera tanyakan kepada pengurus.
            </p>
        </div>
    </div>
</div>

{{-- Styling konten rich text --}}
<style>
.pembinaan-content h1, .pembinaan-content h2, .pembinaan-content h3,
.pembinaan-content h4, .pembinaan-content h5 {
    color: var(--primary-color);
    margin-top: 22px;
    margin-bottom: 10px;
    line-height: 1.4;
}
.pembinaan-content h1 { font-size: 1.5em; border-bottom: 2px solid var(--primary-light); padding-bottom: 8px; }
.pembinaan-content h2 { font-size: 1.25em; }
.pembinaan-content h3 { font-size: 1.1em; }
.pembinaan-content p  { margin-bottom: 12px; }
.pembinaan-content ul, .pembinaan-content ol {
    margin-left: 24px;
    margin-bottom: 14px;
}
.pembinaan-content li { margin-bottom: 6px; }
.pembinaan-content strong { color: #2c3e50; font-weight: 600; }
.pembinaan-content table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 16px;
    font-size: 0.95em;
}
.pembinaan-content table td,
.pembinaan-content table th {
    border: 1px solid #dee2e6;
    padding: 9px 12px;
}
.pembinaan-content table th {
    background: var(--primary-light);
    color: var(--primary-color);
    font-weight: 600;
}
.pembinaan-content table tr:nth-child(even) { background: #f8f9fa; }
.pembinaan-content blockquote {
    border-left: 4px solid var(--primary-color);
    margin: 0 0 14px;
    padding: 10px 16px;
    background: var(--primary-light);
    border-radius: 0 6px 6px 0;
    color: var(--primary-dark, #1a4980);
}

@media (max-width: 768px) {
    div[style*="grid-template-columns: 1fr 280px"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection