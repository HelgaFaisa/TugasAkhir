@extends('layouts.app')

@section('content')
<style>
/* Filter periode */
.period-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}
.period-btn {
    padding: 6px 14px;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    background: #fff;
    cursor: pointer;
    font-size: 0.85rem;
    transition: all 0.2s;
}
.period-btn:hover,
.period-btn.active {
    background: var(--primary-color);
    color: #fff;
    border-color: var(--primary-color);
}
.custom-range {
    display: none;
    align-items: center;
    gap: 8px;
}
.custom-range.show { display: flex; }
.custom-range input[type=date] {
    padding: 5px 10px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.85rem;
}

/* Alert anomali */
.alert-row {
    padding: 10px 14px;
    border-radius: 8px;
    margin-bottom: 8px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 0.88rem;
}
.alert-row.a-danger  { background: #FEF2F2; border-left: 4px solid #EF4444; color: #991B1B; }
.alert-row.a-warning { background: #FFFBEB; border-left: 4px solid #F59E0B; color: #92400E; }
.alert-row.a-info    { background: #EFF6FF; border-left: 4px solid #3B82F6; color: #1E40AF; }
.alert-row.a-success { background: #ECFDF5; border-left: 4px solid #10B981; color: #065F46; }
.alert-text { flex: 1; }
.alert-title { font-weight: 600; }
.alert-desc  { font-size: 0.82rem; opacity: 0.85; }

/* Stat grid — 4 kartu sejajar */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}
.stat-box {
    background: #fff;
    border-radius: 10px;
    padding: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    text-align: center;
}
.stat-label { font-size: 0.8rem; color: #6B7280; margin-bottom: 4px; }
.stat-value { font-size: 1.5rem; font-weight: 700; color: var(--primary-dark); }
.stat-sub   { font-size: 0.78rem; margin-top: 4px; }
.stat-sub.up   { color: #10B981; }
.stat-sub.down { color: #EF4444; }

/* Kartu utama */
.main-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}
.lk-card { background: #fff; border-radius: 10px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.lk-card-title { font-size: 0.95rem; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
.lk-card-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.85rem;
}
.lk-card-item:last-child { border-bottom: none; }
.lk-card-item .item-name { flex: 1; }
.lk-card-empty { padding: 12px; border-radius: 8px; background: #ECFDF5; color: #065F46; font-size: 0.85rem; display: flex; align-items: center; gap: 8px; }
.pattern-item { padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }
.pattern-item:last-child { border-bottom: none; }
.pattern-item-title { font-weight: 600; display: flex; align-items: center; gap: 6px; }
.pattern-item-desc  { font-size: 0.8rem; color: #6B7280; margin-top: 2px; }

/* Chart */
.chart-wrap {
    background: #fff;
    border-radius: 10px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    margin-bottom: 20px;
}
.chart-wrap h4 { margin: 0 0 12px; font-size: 0.95rem; color: var(--primary-dark); }

/* Kelas detail */
.kelas-section { margin-bottom: 20px; }
.kelas-section h4 { font-size: 0.95rem; color: var(--primary-dark); margin-bottom: 12px; }
.kelas-section details {
    background: #fff;
    border-radius: 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    overflow: hidden;
}
.kelas-section summary {
    padding: 12px 16px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.9rem;
    list-style: none;
}
.kelas-section summary::-webkit-details-marker { display: none; }
.kelas-section summary::before {
    content: '\f054';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    font-size: 0.75rem;
    margin-right: 10px;
    transition: transform 0.2s;
    display: inline-block;
}
.kelas-section details[open] summary::before { transform: rotate(90deg); }
.kelas-section summary:hover { background: #f8fafc; }
.kelas-detail-body { padding: 0 16px 16px; }
.row-low { background: #FEF2F2; }
.text-center { text-align: center; }

@media (max-width: 900px) {
    .main-cards { grid-template-columns: 1fr; }
    .stat-grid   { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .stat-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>

{{-- Header --}}
<div class="page-header">
    <h2><i class="fas fa-chart-line"></i> Laporan Kegiatan</h2>
    <div style="display: flex; gap: 8px;">
    </div>
</div>

{{-- Filter Periode --}}
<div class="content-box" style="margin-bottom: 16px;">
    <form method="GET" id="periodForm" action="{{ route('admin.laporan-kegiatan.index') }}">
        <div class="period-bar">
            <span style="font-size: 0.85rem; color: var(--text-light);">
                <i class="fas fa-calendar-alt"></i> Periode:
            </span>
            @foreach(['hari_ini' => 'Hari Ini', 'minggu_ini' => 'Minggu Ini', 'bulan_ini' => 'Bulan Ini', 'semester_ini' => 'Semester', 'custom' => 'Custom'] as $key => $label)
                <button type="button"
                        class="period-btn {{ $periode === $key ? 'active' : '' }}"
                        onclick="setPeriode('{{ $key }}')">{{ $label }}</button>
            @endforeach
            <div class="custom-range {{ $periode === 'custom' ? 'show' : '' }}" id="customRange">
                <input type="date" name="tanggal_dari"
                       value="{{ request('tanggal_dari', $startDate->format('Y-m-d')) }}">
                <span>-</span>
                <input type="date" name="tanggal_sampai"
                       value="{{ request('tanggal_sampai', $endDate->format('Y-m-d')) }}">
            </div>
            <input type="hidden" name="periode" id="periodeInput" value="{{ $periode }}">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-sync-alt"></i> Terapkan
            </button>
        </div>
    </form>
    <p style="margin: 8px 0 0; font-size: 0.82rem; color: var(--text-light);">
        <i class="fas fa-info-circle"></i> Menampilkan data: <strong>{{ $periodeLabel }}</strong>
    </p>
</div>

{{-- KPI Cards: Rata-rata Kehadiran | Santri Perlu Perhatian | Kegiatan Terbaik | Kehadiran Terendah --}}
<div class="stat-grid">
    <div class="stat-box">
        <div class="stat-label">Rata-rata Kehadiran</div>
        <div class="stat-value">{{ $kpi['avg_kehadiran'] }}%</div>
        <div class="stat-sub {{ $kpiComparison['avg_kehadiran'] >= 0 ? 'up' : 'down' }}">
            <i class="fas fa-arrow-{{ $kpiComparison['avg_kehadiran'] >= 0 ? 'up' : 'down' }}"></i>
            {{ abs($kpiComparison['avg_kehadiran']) }}% vs sebelumnya
        </div>
    </div>

    <div class="stat-box">
        <div class="stat-label">Santri Perlu Perhatian</div>
        <div class="stat-value">{{ $kpi['santri_perlu_perhatian'] }}</div>
        <div class="stat-sub {{ $kpiComparison['santri_perlu_perhatian'] <= 0 ? 'up' : 'down' }}">
            <i class="fas fa-arrow-{{ $kpiComparison['santri_perlu_perhatian'] <= 0 ? 'down' : 'up' }}"></i>
            {{ abs($kpiComparison['santri_perlu_perhatian']) }} vs sebelumnya
        </div>
    </div>

    <div class="stat-box">
        <div class="stat-label">Kehadiran Terbaik</div>
        <div class="stat-value" style="font-size: 1rem;">{{ $kpi['kegiatan_terbaik']['nama'] }}</div>
        <div class="stat-sub up">
            <i class="fas fa-trophy"></i> {{ $kpi['kegiatan_terbaik']['persen'] }}%
        </div>
    </div>

    <div class="stat-box">
        <div class="stat-label">Kehadiran Terendah</div>
        @php
            $terendah = !empty($bottomKegiatan) ? $bottomKegiatan[0] : null;
        @endphp
        <div class="stat-value" style="font-size: 1rem; color: #EF4444;">
            {{ $terendah ? $terendah['nama_kegiatan'] : '-' }}
        </div>
        <div class="stat-sub down">
            <i class="fas fa-arrow-down"></i> {{ $terendah ? $terendah['persen'] . '%' : '-' }}
        </div>
    </div>
</div>

{{-- Alert Anomali --}}
<div class="content-box" style="margin-bottom: 16px; padding: 12px;">
    @if(!empty($patterns) && count($patterns) > 0)
        @foreach($patterns as $p)
            <div class="alert-row a-{{ $p['type'] }}">
                @if($p['type'] === 'danger')
                    <i class="fas fa-exclamation-circle"></i>
                @elseif($p['type'] === 'warning')
                    <i class="fas fa-exclamation-triangle"></i>
                @else
                    <i class="fas fa-info-circle"></i>
                @endif
                <div class="alert-text">
                    <div class="alert-title">{{ $p['title'] }}</div>
                    <div class="alert-desc">{{ $p['description'] }}</div>
                </div>
                @if(!empty($p['action_url']))
                    <a href="{{ $p['action_url'] }}"
                       class="btn btn-sm btn-{{ $p['type'] === 'danger' ? 'danger' : ($p['type'] === 'warning' ? 'warning' : 'info') }}">
                        {{ $p['action_text'] ?? 'Lihat' }}
                    </a>
                @endif
            </div>
        @endforeach
    @else
        <div class="alert-row a-success">
            <i class="fas fa-check-circle"></i>
            <div class="alert-text">
                <div class="alert-title">Tidak ada anomali terdeteksi</div>
                <div class="alert-desc">Pola kehadiran dalam kondisi normal.</div>
            </div>
        </div>
    @endif
</div>

{{-- Tiga Kartu Utama --}}
@php
    $topBolos       = $santriPerluPerhatianList ? $santriPerluPerhatianList->sortByDesc('alpa')->take(5) : collect();
    $dangerPatterns = collect($patterns ?? [])->where('type', 'danger');
@endphp

<div class="main-cards">
    {{-- Kartu 1: Santri Sering Bolos --}}
    <div class="lk-card">
        <div class="lk-card-title" style="color: #EF4444;">
            <i class="fas fa-user-times"></i> Santri Sering Bolos
        </div>
        @if($topBolos->count() > 0)
            @foreach($topBolos as $s)
                <div class="lk-card-item">
                    <a href="{{ route('admin.laporan-kegiatan.detail-santri', $s->id_santri) }}"
                       class="item-name" style="color: inherit; text-decoration: none;">
                        {{ $s->nama_lengkap }}
                    </a>
                    <span class="badge badge-danger">{{ $s->alpa }}x alpa</span>
                    <span class="badge badge-danger">{{ $s->persen }}%</span>
                </div>
            @endforeach
            <a href="{{ route('admin.laporan-kegiatan.santri-perlu-perhatian', request()->query()) }}"
               class="btn btn-sm btn-secondary" style="margin-top: 10px;">
                <i class="fas fa-list"></i> Lihat Semua
            </a>
        @else
            <div class="lk-card-empty">
                <i class="fas fa-check-circle"></i> Tidak ada santri bermasalah
            </div>
        @endif
    </div>

    {{-- Kartu 2: Kegiatan Paling Sepi --}}
    <div class="lk-card">
        <div class="lk-card-title" style="color: #F59E0B;">
            <i class="fas fa-calendar-times"></i> Kegiatan Paling Sepi
        </div>
        @if(!empty($bottomKegiatan) && count($bottomKegiatan) > 0)
            @foreach($bottomKegiatan as $kg)
                <div class="lk-card-item">
                    <a href="{{ route('admin.laporan-kegiatan.analisis-kegiatan', $kg['kegiatan_id']) }}"
                       class="item-name" style="color: inherit; text-decoration: none;">
                        {{ $kg['nama_kegiatan'] }}
                    </a>
                    <span class="badge badge-info">{{ $kg['nama_kategori'] ?? '-' }}</span>
                    <span style="font-weight: 700; color: {{ $kg['persen'] < 70 ? '#EF4444' : ($kg['persen'] < 85 ? '#F59E0B' : '#10B981') }};">
                        {{ $kg['persen'] }}%
                    </span>
                </div>
            @endforeach
        @else
            <div class="lk-card-empty">
                <i class="fas fa-check-circle"></i> Semua kegiatan berjalan baik
            </div>
        @endif
    </div>

    {{-- Kartu 3: Perlu Ditindaklanjuti --}}
    <div class="lk-card">
        <div class="lk-card-title" style="color: #EF4444;">
            <i class="fas fa-bell"></i> Perlu Ditindaklanjuti
        </div>
        @if($dangerPatterns->count() > 0)
            @foreach($dangerPatterns as $p)
                <div class="pattern-item">
                    <div class="pattern-item-title">
                        <i class="fas fa-exclamation-circle" style="color: #EF4444;"></i>
                        {{ $p['title'] }}
                    </div>
                    <div class="pattern-item-desc">{{ $p['description'] }}</div>
                    @if(!empty($p['action_url']))
                        <a href="{{ $p['action_url'] }}" class="btn btn-sm btn-danger" style="margin-top: 6px;">
                            {{ $p['action_text'] ?? 'Tindakan' }}
                        </a>
                    @endif
                </div>
            @endforeach
        @else
            <div class="lk-card-empty">
                <i class="fas fa-check-circle"></i> Semua kondisi normal
            </div>
        @endif
    </div>
</div>

{{-- Grafik Trend --}}
@php
    $avgTrendData   = [];
    $trendLabelsArr = $trendData['labels'] ?? [];
    $trendDatasets  = $trendData['datasets'] ?? [];
    $labelCount     = count($trendLabelsArr);
    for ($li = 0; $li < $labelCount; $li++) {
        $sum = 0; $cnt = 0;
        foreach ($trendDatasets as $ds) {
            if (isset($ds['data'][$li]) && $ds['data'][$li] !== null) {
                $sum += $ds['data'][$li]; $cnt++;
            }
        }
        $avgTrendData[] = $cnt > 0 ? round($sum / $cnt, 1) : 0;
    }
@endphp

<div class="chart-wrap">
    <h4><i class="fas fa-chart-line"></i> Trend Kehadiran Rata-rata</h4>
    <div style="position: relative; height: 200px;">
        <canvas id="trendChart"></canvas>
    </div>
</div>

{{-- Kehadiran Per Kelas --}}
<div class="kelas-section">
    <h4><i class="fas fa-school"></i> Kehadiran Per Kelas</h4>
    @foreach($kehadiranPerKelas as $kelompok)
        <details>
            <summary>{{ $kelompok['nama_kelompok'] }} ({{ count($kelompok['kelas']) }} kelas)</summary>
            <div class="kelas-detail-body">
                <table class="data-table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Alpa</th>
                            <th class="text-center">% Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelompok['kelas'] as $k)
                            <tr class="{{ $k['persen'] < 70 ? 'row-low' : '' }}">
                                <td><strong>{{ $k['nama_kelas'] }}</strong></td>
                                <td class="text-center">{{ $k['hadir'] }}</td>
                                <td class="text-center">{{ $k['alpa'] }}</td>
                                <td class="text-center">
                                    <strong style="color: {{ $k['persen'] >= 85 ? '#10B981' : ($k['persen'] >= 70 ? '#F59E0B' : '#EF4444') }};">
                                        {{ $k['persen'] }}%
                                    </strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </details>
    @endforeach
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
function setPeriode(p) {
    document.getElementById('periodeInput').value = p;
    document.querySelectorAll('.period-btn').forEach(function(b) { b.classList.remove('active'); });
    event.target.classList.add('active');
    var cr = document.getElementById('customRange');
    if (p === 'custom') {
        cr.classList.add('show');
    } else {
        cr.classList.remove('show');
        document.getElementById('periodForm').submit();
    }
}

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: [
            @foreach($trendLabelsArr as $label)
                '{!! addslashes($label) !!}',
            @endforeach
        ],
        datasets: [{
            label: 'Rata-rata Kehadiran',
            data: [{{ implode(',', $avgTrendData) }}],
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 3,
            pointHoverRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: function(ctx) { return ctx.parsed.y + '%'; } } }
        },
        scales: {
            y: { min: 0, max: 100, ticks: { callback: function(v) { return v + '%'; } }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});
</script>

<style>
@media print {
    .page-header .btn, .period-bar, .btn, button { display: none !important; }
    .content-box, .lk-card, .stat-box, .chart-wrap { box-shadow: none !important; border: 1px solid #e2e8f0; }
    .kelas-section details { box-shadow: none !important; border: 1px solid #e2e8f0; }
}
</style>
@endsection