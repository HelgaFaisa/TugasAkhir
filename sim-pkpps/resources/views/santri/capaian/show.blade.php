{{-- resources/views/santri/capaian/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Capaian – ' . $capaian->materi->nama_kitab)

@section('content')

@php
    $pct = min(100, round($capaian->persentase, 1));
    if ($pct >= 100)     { $statColor = '#2e7d32'; $statLabel = 'Khatam'; $statIcon = 'fa-check-double'; $statBg = '#e8f5e9'; }
    elseif ($pct >= 75)  { $statColor = '#1565c0'; $statLabel = 'Hampir Selesai'; $statIcon = 'fa-fire'; $statBg = '#e3f2fd'; }
    elseif ($pct >= 50)  { $statColor = '#e65100'; $statLabel = 'Setengah Jalan'; $statIcon = 'fa-bolt'; $statBg = '#fff3e0'; }
    elseif ($pct >= 25)  { $statColor = '#f57f17'; $statLabel = 'Sedang Belajar'; $statIcon = 'fa-seedling'; $statBg = '#fffde7'; }
    else                 { $statColor = '#c62828'; $statLabel = 'Baru Dimulai'; $statIcon = 'fa-circle'; $statBg = '#fbe9e7'; }

    $kategori = $capaian->materi->kategori;
    if ($kategori === "Al-Qur'an")        { $katColor = 'var(--success-color)'; $katIcon = 'fas fa-book-quran'; }
    elseif ($kategori === 'Hadist')        { $katColor = 'var(--info-color)';    $katIcon = 'fas fa-scroll'; }
    else                                   { $katColor = 'var(--warning-color)'; $katIcon = 'fas fa-book'; }

    $completedPages = $capaian->pages_array;
    $totalPages     = $capaian->materi->total_halaman;
    $startPage      = $capaian->materi->halaman_mulai;
    $endPage        = $capaian->materi->halaman_akhir;

    $r = 54;
    $circ = round(2 * 3.14159 * $r, 2);
    $offset = round($circ * (1 - $pct/100), 2);
@endphp

<style>
/* ====== SHOW PAGE ====== */
.show-hero {
    background: linear-gradient(135deg, var(--primary-dark,#1a4980) 0%, var(--primary-color) 100%);
    border-radius: var(--border-radius);
    padding: 28px;
    color: #fff;
    display: flex;
    gap: 28px;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 18px;
    position: relative;
    overflow: hidden;
}
.show-hero::after {
    content: '';
    position: absolute;
    right: -40px; bottom: -40px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
}
.hero-circle {
    flex-shrink: 0;
    position: relative;
    width: 140px; height: 140px;
}
.hero-circle svg { transform: rotate(-90deg); }
.hero-circle .hc-center {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: #fff;
}
.hero-circle .hc-pct { font-size: 1.8rem; font-weight: 800; line-height: 1; }
.hero-circle .hc-sub { font-size: 0.7rem; opacity: 0.75; margin-top: 2px; }
.hero-info { flex: 1; min-width: 200px; }
.hero-info h2 { margin: 0 0 8px; font-size: 1.3rem; line-height: 1.3; }
.hero-info .meta-row { display: flex; gap: 14px; flex-wrap: wrap; opacity: 0.8; font-size: 0.85rem; margin-top: 10px; }
.status-pill {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,0.18);
    border-radius: 20px; padding: 4px 12px;
    font-size: 0.8rem; font-weight: 600;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,0.25);
}

/* Info grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px; margin-bottom: 18px;
}
.info-box-card {
    background: #fff; border-radius: 12px;
    padding: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    display: flex; align-items: center; gap: 12px;
    border-left: 4px solid;
}
.info-box-card .ib-icon { font-size: 1.4rem; flex-shrink: 0; }
.info-box-card .ib-label { font-size: 0.72rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.3px; }
.info-box-card .ib-val { font-size: 1rem; font-weight: 700; margin-top: 1px; }

/* Page grid */
.page-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(42px, 1fr));
    gap: 6px;
}
.page-cell {
    height: 40px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem; font-weight: 700;
    cursor: default;
    transition: transform 0.15s;
    position: relative;
}
.page-cell:hover { transform: scale(1.12); z-index: 2; }
.page-cell.done {
    background: linear-gradient(135deg, var(--success-color), #2e7d32);
    color: #fff;
    box-shadow: 0 2px 6px rgba(46,125,50,0.3);
}
.page-cell.undone {
    background: #f5f5f5; color: #bbb;
    border: 1px dashed #ddd;
}

/* Catatan */
.catatan-box {
    background: #fffde7; border-left: 4px solid #fdd835;
    border-radius: 0 10px 10px 0; padding: 14px 16px; margin-top: 12px;
}

/* Motivasi */
.moti-card {
    border-radius: 14px; padding: 20px;
    text-align: center;
    margin-bottom: 18px;
}

@media (max-width: 600px) {
    .show-hero { flex-direction: column; text-align: center; }
    .hero-info .meta-row { justify-content: center; }
    .info-grid { grid-template-columns: 1fr 1fr; }
}
</style>

{{-- ===== HERO ===== --}}
<div class="show-hero">
    {{-- Big circle --}}
    <div class="hero-circle">
        <svg width="140" height="140" viewBox="0 0 140 140">
            <circle fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="10" cx="70" cy="70" r="{{ $r }}"/>
            <circle id="heroCircle"
                fill="none"
                stroke="#fff"
                stroke-width="10"
                stroke-linecap="round"
                cx="70" cy="70" r="{{ $r }}"
                stroke-dasharray="{{ $circ }}"
                stroke-dashoffset="{{ $circ }}"
                data-final="{{ $offset }}"
                style="transition:stroke-dashoffset 1.4s cubic-bezier(0.4,0,0.2,1);"/>
        </svg>
        <div class="hc-center">
            <div class="hc-pct">{{ $pct }}%</div>
            <div class="hc-sub">progress</div>
        </div>
    </div>

    {{-- Info --}}
    <div class="hero-info">
        <div style="margin-bottom:10px;">
            <span class="status-pill">
                <i class="{{ $katIcon }}"></i>
                {{ $kategori }}
            </span>
            <span class="status-pill" style="margin-left:8px;">
                <i class="fas fa-calendar"></i>
                {{ $capaian->semester->nama_semester }}
            </span>
        </div>
        <h2>{{ $capaian->materi->nama_kitab }}</h2>
        <div class="meta-row">
            <span><i class="fas fa-file-alt"></i> {{ count($completedPages) }}/{{ $totalPages }} halaman</span>
            <span><i class="fas fa-calendar-day"></i> {{ \Carbon\Carbon::parse($capaian->tanggal_input)->isoFormat('D MMMM YYYY') }}</span>
        </div>
    </div>

    {{-- Back button (top right) --}}
    <div style="position:absolute;top:18px;right:18px;z-index:5;">
        <a href="{{ route('santri.capaian.index') }}"
           style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.35);
                  padding:7px 14px;border-radius:8px;text-decoration:none;font-size:0.82rem;font-weight:600;
                  backdrop-filter:blur(4px);display:inline-flex;align-items:center;gap:6px;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- ===== MOTIVASI BANNER ===== --}}
<div class="moti-card" style="background:{{ $statBg }};border:2px solid {{ $statColor }}22;">
    <div style="font-size:2.2rem;margin-bottom:6px;">
        @if($pct >= 100) 🏆 @elseif($pct >= 75) 🔥 @elseif($pct >= 50) ⚡ @elseif($pct >= 25) 🌱 @else 📖 @endif
    </div>
    <strong style="color:{{ $statColor }};font-size:1.05rem;">{{ $statLabel }}</strong>
    <p style="margin:6px 0 0;color:#555;font-size:0.85rem;">
        @if($pct >= 100) Alhamdulillah! Materi ini telah khatam diselesaikan. Terus semangat untuk materi berikutnya!
        @elseif($pct >= 75) Tinggal {{ 100 - $pct }}% lagi! Pertahankan semangat, finish line sudah terlihat!
        @elseif($pct >= 50) Sudah setengah jalan! Setiap halaman yang selesai adalah langkah menuju khatam.
        @elseif($pct >= 25) Baru {{ $pct }}% — setiap perjalanan dimulai dari langkah pertama. Tetap semangat!
        @else Ayo mulai! Satu halaman sehari membawa perubahan besar di akhir semester.
        @endif
    </p>
</div>

{{-- ===== INFO GRID ===== --}}
<div class="info-grid">
    <div class="info-box-card" style="border-color:{{ $katColor }};">
        <i class="{{ $katIcon }} ib-icon" style="color:{{ $katColor }};"></i>
        <div>
            <div class="ib-label">Kategori</div>
            <div class="ib-val" style="color:{{ $katColor }};">{{ $kategori }}</div>
        </div>
    </div>
    <div class="info-box-card" style="border-color:var(--primary-color);">
        <i class="fas fa-book-open ib-icon" style="color:var(--primary-color);"></i>
        <div>
            <div class="ib-label">Halaman Selesai</div>
            <div class="ib-val">{{ count($completedPages) }} <span style="font-weight:400;color:var(--text-light);">dari {{ $totalPages }}</span></div>
        </div>
    </div>
    <div class="info-box-card" style="border-color:var(--warning-color);">
        <i class="fas fa-hourglass-half ib-icon" style="color:var(--warning-color);"></i>
        <div>
            <div class="ib-label">Sisa Halaman</div>
            <div class="ib-val" style="color:var(--warning-color);">{{ $totalPages - count($completedPages) }} halaman</div>
        </div>
    </div>
    <div class="info-box-card" style="border-color:var(--info-color);">
        <i class="fas fa-calendar ib-icon" style="color:var(--info-color);"></i>
        <div>
            <div class="ib-label">Range Halaman</div>
            <div class="ib-val">{{ $startPage }}–{{ $endPage }}</div>
        </div>
    </div>
</div>

{{-- ===== HALAMAN RANGE ===== --}}
@if($capaian->halaman_selesai)
<div class="content-box" style="margin-bottom:14px;">
    <h4 style="margin:0 0 6px;color:var(--primary-color);font-size:0.95rem;">
        <i class="fas fa-list-ol"></i> Range Halaman yang Selesai
    </h4>
    <div style="background:#f8f9fa;border-radius:8px;padding:12px 16px;font-family:monospace;font-size:1rem;color:var(--text-color);word-break:break-all;letter-spacing:0.5px;">
        {{ $capaian->halaman_selesai }}
    </div>
    @if($capaian->catatan)
    <div class="catatan-box">
        <i class="fas fa-sticky-note" style="color:#f57f17;margin-right:6px;"></i>
        <strong>Catatan:</strong> {{ $capaian->catatan }}
    </div>
    @endif
</div>
@endif

{{-- ===== VISUAL PAGE GRID ===== --}}
<div class="content-box" style="margin-bottom:14px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px;">
        <h4 style="margin:0;color:var(--primary-color);font-size:0.95rem;">
            <i class="fas fa-th"></i> Visualisasi Halaman
        </h4>
        <div style="display:flex;gap:14px;font-size:0.8rem;">
            <span style="display:flex;align-items:center;gap:5px;">
                <span style="width:16px;height:16px;background:linear-gradient(135deg,var(--success-color),#2e7d32);border-radius:4px;display:inline-block;"></span>
                Selesai ({{ count($completedPages) }})
            </span>
            <span style="display:flex;align-items:center;gap:5px;">
                <span style="width:16px;height:16px;background:#f5f5f5;border:1px dashed #ddd;border-radius:4px;display:inline-block;"></span>
                Belum ({{ $totalPages - count($completedPages) }})
            </span>
        </div>
    </div>

    {{-- Search halaman --}}
    <div style="margin-bottom:12px;">
        <input type="number" id="searchPage" placeholder="Cari nomor halaman..."
               class="form-control" style="max-width:220px;"
               oninput="highlightPage(this.value)">
    </div>

    <div class="page-grid" id="pageGrid">
        @for($i = $startPage; $i <= $endPage; $i++)
            @php $isDone = in_array($i, $completedPages); @endphp
            <div class="page-cell {{ $isDone ? 'done' : 'undone' }}"
                 id="page-{{ $i }}"
                 title="Halaman {{ $i }} — {{ $isDone ? 'Selesai ✓' : 'Belum' }}">
                {{ $i }}
            </div>
        @endfor
    </div>

    {{-- Progress summary --}}
    <div style="margin-top:14px;display:flex;align-items:center;gap:12px;">
        <div style="flex:1;background:#f0f0f0;border-radius:20px;height:12px;overflow:hidden;">
            <div style="height:100%;width:{{ $pct }}%;border-radius:20px;
                        background:linear-gradient(90deg,
                            @if($pct >= 75) var(--success-color),#2e7d32
                            @elseif($pct >= 50) var(--warning-color),#e65100
                            @else var(--danger-color),#b71c1c @endif);
                        transition:width 1s ease;"></div>
        </div>
        <span style="font-weight:800;color:var(--primary-color);min-width:44px;text-align:right;">{{ $pct }}%</span>
    </div>
</div>

{{-- ===== QUICK NAV ===== --}}
<div style="display:flex;gap:10px;flex-wrap:wrap;">
    <a href="{{ route('santri.capaian.index') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-list"></i> Semua Capaian
    </a>
    <a href="{{ route('santri.dashboard') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-home"></i> Dashboard
    </a>
</div>

<script>
// Animate hero circle on load
document.addEventListener('DOMContentLoaded', function() {
    const circle = document.getElementById('heroCircle');
    if (circle) {
        setTimeout(() => {
            circle.style.strokeDashoffset = circle.dataset.final;
        }, 300);
    }
});

// Search/highlight a specific page cell
function highlightPage(val) {
    // Remove previous highlight
    document.querySelectorAll('.page-cell.highlighted').forEach(el => {
        el.classList.remove('highlighted');
        el.style.outline = '';
    });

    if (!val) return;
    const cell = document.getElementById('page-' + val);
    if (cell) {
        cell.style.outline = '3px solid #ff5722';
        cell.classList.add('highlighted');
        cell.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}
</script>

@endsection