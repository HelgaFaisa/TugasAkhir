@extends('layouts.app')

@section('content')
{{-- ═════════════════ INLINE STYLES ═════════════════ --}}
<style>
/* ── Tab Navigation ── */
.laporan-tabs { display: flex; gap: 0; border-bottom: 2px solid #e2e8f0; margin-bottom: 0; flex-wrap: wrap; }
.laporan-tab { padding: 12px 20px; cursor: pointer; border: none; background: none; font-size: 0.9rem; color: var(--text-light); transition: all 0.3s; border-bottom: 3px solid transparent; white-space: nowrap; }
.laporan-tab:hover { color: var(--primary-color); background: rgba(16,185,129,0.05); }
.laporan-tab.active { color: var(--primary-color); border-bottom: 3px solid var(--primary-color); font-weight: 600; }
.laporan-tab i { margin-right: 6px; }
.tab-pane { display: none; padding: 20px 0; }
.tab-pane.active { display: block; }

/* ── KPI Cards ── */
.kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px; }
.kpi-card { padding: 20px; border-radius: 12px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.06); position: relative; overflow: hidden; }
.kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:4px; }
.kpi-card.kpi-blue::before { background: linear-gradient(90deg,#3B82F6,#60A5FA); }
.kpi-card.kpi-green::before { background: linear-gradient(90deg,#10B981,#34D399); }
.kpi-card.kpi-gold::before { background: linear-gradient(90deg,#F59E0B,#FBBF24); }
.kpi-card.kpi-red::before { background: linear-gradient(90deg,#EF4444,#F87171); }
.kpi-label { font-size: 0.82rem; color: var(--text-light); margin-bottom: 6px; }
.kpi-value { font-size: 1.8rem; font-weight: 700; color: var(--primary-dark); }
.kpi-sub { font-size: 0.78rem; margin-top: 6px; }
.kpi-sub.up { color: #10B981; }
.kpi-sub.down { color: #EF4444; }
.kpi-sub.neutral { color: #6B7280; }
.kpi-icon { position:absolute; top:18px; right:18px; font-size:2rem; opacity:0.12; }

/* ── Period Selector ── */
.period-bar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }
.period-btn { padding: 6px 14px; border-radius: 20px; border: 1px solid #e2e8f0; background: #fff; cursor: pointer; font-size: 0.82rem; transition: all 0.2s; }
.period-btn:hover, .period-btn.active { background: var(--primary-color); color: #fff; border-color: var(--primary-color); }
.custom-range { display: none; align-items: center; gap: 8px; }
.custom-range.show { display: flex; }
.custom-range input[type=date] { padding: 5px 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.82rem; }

/* ── Charts ── */
.chart-box { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; }
.chart-box h4 { margin: 0 0 16px 0; color: var(--primary-dark); font-size: 1rem; }
.chart-container { position: relative; width: 100%; }
.chart-container canvas { max-width: 100%; }

/* ── Heatmap ── */
.heatmap-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
.heatmap-table th { padding: 10px 8px; background: #f8fafc; color: var(--text-light); font-weight: 600; text-align: center; border: 1px solid #e2e8f0; }
.heatmap-table td { padding: 0; border: 1px solid #e2e8f0; }
.heatmap-cell { padding: 10px 8px; text-align: center; cursor: pointer; transition: all 0.2s; font-weight: 600; font-size: 0.8rem; }
.heatmap-cell:hover { transform: scale(1.08); box-shadow: 0 2px 8px rgba(0,0,0,0.15); position: relative; z-index: 2; }
.heatmap-90 { background: #10B981; color: #fff; }
.heatmap-80 { background: #34D399; color: #fff; }
.heatmap-70 { background: #FBBF24; color: #333; }
.heatmap-low { background: #EF4444; color: #fff; }
.heatmap-na { background: #F3F4F6; color: #9CA3AF; cursor: not-allowed; }
.heatmap-table .row-label { text-align: left; padding: 10px 12px; font-weight: 600; background: #fff; white-space: nowrap; }

/* ── Top/Bottom Cards ── */
.rank-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
@media(max-width:768px){ .rank-grid { grid-template-columns: 1fr; } }
.rank-card { padding: 14px 16px; border-radius: 10px; background: #fff; border-left: 4px solid; display: flex; align-items: center; gap: 14px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); }
.rank-card.top { border-color: #10B981; }
.rank-card.bottom { border-color: #EF4444; }
.rank-num { font-size: 1.4rem; font-weight: 700; min-width: 30px; text-align: center; }
.rank-card.top .rank-num { color: #10B981; }
.rank-card.bottom .rank-num { color: #EF4444; }
.rank-info { flex: 1; }
.rank-info .name { font-weight: 600; font-size: 0.9rem; }
.rank-info .meta { font-size: 0.78rem; color: var(--text-light); }
.rank-persen { font-weight: 700; font-size: 1.1rem; }

/* ── Leaderboard ── */
.leaderboard-table { width: 100%; }
.leaderboard-table .medal { font-size: 1.3rem; }
.streak-badge { background: linear-gradient(135deg,#F59E0B,#FBBF24); color:#fff; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }

/* ── Progress bar inline ── */
.progress-inline { display: flex; align-items: center; gap: 8px; }
.progress-bar-mini { flex: 1; background: #e9ecef; border-radius: 8px; height: 8px; overflow: hidden; max-width: 120px; }
.progress-bar-mini .fill { height: 100%; border-radius: 8px; transition: width 0.4s ease; }

/* ── Pattern Cards ── */
.pattern-card { padding: 16px; border-radius: 10px; border-left: 4px solid; margin-bottom: 12px; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.05); }
.pattern-card.p-danger { border-color: #EF4444; background: #FEF2F2; }
.pattern-card.p-warning { border-color: #F59E0B; background: #FFFBEB; }
.pattern-card.p-info { border-color: #3B82F6; background: #EFF6FF; }
.pattern-card.p-success { border-color: #10B981; background: #ECFDF5; }
.pattern-title { font-weight: 600; font-size: 0.92rem; margin-bottom: 4px; }
.pattern-desc { font-size: 0.82rem; color: #4B5563; }
.pattern-category { display: inline-block; font-size: 0.72rem; padding: 2px 8px; border-radius: 10px; background: rgba(0,0,0,0.06); margin-bottom: 6px; }

/* ── Export Section ── */
.export-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
.export-card { padding: 24px; border-radius: 12px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.export-card h4 { margin: 0 0 16px; color: var(--primary-dark); }
.checkbox-group { display: flex; flex-direction: column; gap: 8px; margin: 12px 0; }
.checkbox-group label { display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer; }
.checkbox-group input[type=checkbox] { accent-color: var(--primary-color); }

/* ── Accordion ── */
.accordion-item { border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 8px; overflow: hidden; }
.accordion-header { padding: 14px 16px; background: #f8fafc; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size:0.9rem; transition: background 0.2s; }
.accordion-header:hover { background: #eef2f7; }
.accordion-header .arrow { transition: transform 0.3s; }
.accordion-header.open .arrow { transform: rotate(180deg); }
.accordion-body { padding: 0 16px; max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.3s; }
.accordion-body.open { max-height: 2000px; padding: 16px; }

/* ── Insight Box ── */
.insight-box { padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; display: flex; align-items: flex-start; gap: 10px; font-size: 0.85rem; }
.insight-box.i-success { background: #ECFDF5; color: #065F46; }
.insight-box.i-warning { background: #FFFBEB; color: #92400E; }
.insight-box.i-danger { background: #FEF2F2; color: #991B1B; }
.insight-box.i-info { background: #EFF6FF; color: #1E40AF; }
.insight-box i { margin-top: 2px; }

/* ── Misc ── */
.section-title { font-size: 1.05rem; font-weight: 600; color: var(--primary-dark); margin: 0 0 16px; display: flex; align-items: center; gap: 8px; }
.text-center { text-align: center; }
.text-right { text-align: right; }
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
@media(max-width:768px){ .two-col { grid-template-columns: 1fr; } }
</style>

{{-- ═════════════════ HEADER ═════════════════ --}}
<div class="page-header">
    <h2><i class="fas fa-chart-line"></i> Laporan & Statistik Kegiatan</h2>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.riwayat-kegiatan.index') }}" class="btn btn-info btn-sm">
            <i class="fas fa-history"></i> Riwayat Kegiatan
        </a>
        <a href="{{ route('admin.laporan-kegiatan.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm" target="_blank">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
        <button onclick="exportExcel()" class="btn btn-success btn-sm">
            <i class="fas fa-file-excel"></i> Excel
        </button>
    </div>
</div>

{{-- ═════════════════ PERIOD SELECTOR ═════════════════ --}}
<div class="content-box" style="margin-bottom: 20px;">
    <form method="GET" id="periodForm" action="{{ route('admin.laporan-kegiatan.index') }}">
        <div class="period-bar">
            <span style="font-size:0.85rem; color:var(--text-light);"><i class="fas fa-calendar-alt"></i> Periode:</span>
            @foreach(['hari_ini'=>'Hari Ini','minggu_ini'=>'Minggu Ini','bulan_ini'=>'Bulan Ini','semester_ini'=>'Semester','custom'=>'Custom'] as $key=>$label)
                <button type="button" class="period-btn {{ $periode === $key ? 'active' : '' }}" data-periode="{{ $key }}" onclick="setPeriode('{{ $key }}')">{{ $label }}</button>
            @endforeach
            <div class="custom-range {{ $periode === 'custom' ? 'show' : '' }}" id="customRange">
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari', $startDate->format('Y-m-d')) }}">
                <span>—</span>
                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai', $endDate->format('Y-m-d')) }}">
            </div>
            <input type="hidden" name="periode" id="periodeInput" value="{{ $periode }}">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-sync-alt"></i> Terapkan</button>
        </div>
    </form>
    <p style="margin: 8px 0 0; font-size: 0.82rem; color: var(--text-light);">
        <i class="fas fa-info-circle"></i> Menampilkan data: <strong>{{ $periodeLabel }}</strong>
    </p>
</div>

{{-- ═════════════════ KPI CARDS ═════════════════ --}}
<div class="kpi-grid">
    <div class="kpi-card kpi-blue">
        <div class="kpi-label">Total Kegiatan</div>
        <div class="kpi-value">{{ $kpi['total_kegiatan'] }}</div>
        <div class="kpi-sub {{ $kpiComparison['total_kegiatan'] >= 0 ? 'up' : 'down' }}">
            <i class="fas fa-{{ $kpiComparison['total_kegiatan'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
            {{ abs($kpiComparison['total_kegiatan']) }} vs periode lalu
        </div>
        <i class="fas fa-calendar-check kpi-icon"></i>
    </div>
    <div class="kpi-card kpi-green">
        <div class="kpi-label">Rata-rata Kehadiran</div>
        <div class="kpi-value">{{ $kpi['avg_kehadiran'] }}%</div>
        <div class="kpi-sub {{ $kpiComparison['avg_kehadiran'] >= 0 ? 'up' : 'down' }}">
            <i class="fas fa-{{ $kpiComparison['avg_kehadiran'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
            {{ abs($kpiComparison['avg_kehadiran']) }}% vs periode lalu
        </div>
        <i class="fas fa-percentage kpi-icon"></i>
    </div>
    <div class="kpi-card kpi-gold">
        <div class="kpi-label">Kegiatan Terbaik</div>
        <div class="kpi-value" style="font-size:1.2rem;">{{ $kpi['kegiatan_terbaik']['nama'] }}</div>
        <div class="kpi-sub up">
            <i class="fas fa-trophy"></i> {{ $kpi['kegiatan_terbaik']['persen'] }}% kehadiran
        </div>
        <i class="fas fa-award kpi-icon"></i>
    </div>
    <div class="kpi-card kpi-red">
        <div class="kpi-label">Santri Perlu Perhatian</div>
        <div class="kpi-value">{{ $kpi['santri_perlu_perhatian'] }}</div>
        <div class="kpi-sub {{ $kpiComparison['santri_perlu_perhatian'] <= 0 ? 'up' : 'down' }}">
            <i class="fas fa-{{ $kpiComparison['santri_perlu_perhatian'] <= 0 ? 'arrow-down' : 'arrow-up' }}"></i>
            {{ abs($kpiComparison['santri_perlu_perhatian']) }} vs periode lalu
        </div>
        <i class="fas fa-user-clock kpi-icon"></i>
    </div>
</div>

{{-- ═════════════════ TAB NAVIGATION ═════════════════ --}}
<div class="content-box" style="padding:0;">
    <div class="laporan-tabs" id="laporanTabs">
        <button class="laporan-tab active" data-tab="overview"><i class="fas fa-tachometer-alt"></i> Overview</button>
        <button class="laporan-tab" data-tab="kelas"><i class="fas fa-school"></i> Analisis Kelas</button>
        <button class="laporan-tab" data-tab="santri"><i class="fas fa-users"></i> Analisis Santri</button>
        <button class="laporan-tab" data-tab="kegiatan"><i class="fas fa-tasks"></i> Analisis Kegiatan</button>
        <button class="laporan-tab" data-tab="pattern"><i class="fas fa-brain"></i> Pola & Anomali</button>
        <button class="laporan-tab" data-tab="export"><i class="fas fa-download"></i> Export</button>
    </div>
</div>

{{-- ═══════════════ TAB 1: OVERVIEW ═══════════════ --}}
<div class="tab-pane active" id="tab-overview">
    {{-- Trend Chart --}}
    <div class="chart-box">
        <h4><i class="fas fa-chart-line"></i> Trend Kehadiran Per Kategori</h4>
        <div class="chart-container" style="height:320px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <div class="two-col">
        {{-- Distribusi Santri --}}
        <div class="chart-box">
            <h4><i class="fas fa-chart-bar"></i> Distribusi Kehadiran Santri</h4>
            <div class="chart-container" style="height:280px;">
                <canvas id="distribusiChart"></canvas>
            </div>
        </div>

        {{-- Quick Insights --}}
        <div class="chart-box">
            <h4><i class="fas fa-lightbulb"></i> Insight Otomatis</h4>
            @if($kpi['avg_kehadiran'] >= 85)
                <div class="insight-box i-success"><i class="fas fa-check-circle"></i> Rata-rata kehadiran sangat baik ({{ $kpi['avg_kehadiran'] }}%). Pertahankan!</div>
            @elseif($kpi['avg_kehadiran'] >= 70)
                <div class="insight-box i-warning"><i class="fas fa-exclamation-triangle"></i> Rata-rata kehadiran cukup ({{ $kpi['avg_kehadiran'] }}%). Ada ruang untuk peningkatan.</div>
            @else
                <div class="insight-box i-danger"><i class="fas fa-times-circle"></i> Rata-rata kehadiran rendah ({{ $kpi['avg_kehadiran'] }}%). Perlu evaluasi serius.</div>
            @endif

            @if($kpi['santri_perlu_perhatian'] > 0)
                <div class="insight-box i-danger"><i class="fas fa-user-clock"></i> {{ $kpi['santri_perlu_perhatian'] }} santri memiliki kehadiran &lt;70%. <a href="#" onclick="switchTab('santri')">Lihat daftar →</a></div>
            @endif

            @if(!empty($bottomKegiatan))
                @php $worst = $bottomKegiatan[0] ?? null; @endphp
                @if($worst && $worst['persen'] < 70)
                    <div class="insight-box i-warning"><i class="fas fa-arrow-down"></i> {{ $worst['nama_kegiatan'] }} memiliki kehadiran terendah ({{ $worst['persen'] }}%). <a href="{{ route('admin.laporan-kegiatan.analisis-kegiatan', $worst['kegiatan_id']) }}">Analisis →</a></div>
                @endif
            @endif

            @if($kpiComparison['avg_kehadiran'] > 5)
                <div class="insight-box i-success"><i class="fas fa-arrow-up"></i> Kehadiran meningkat {{ $kpiComparison['avg_kehadiran'] }}% dibanding periode lalu.</div>
            @elseif($kpiComparison['avg_kehadiran'] < -5)
                <div class="insight-box i-danger"><i class="fas fa-arrow-down"></i> Kehadiran menurun {{ abs($kpiComparison['avg_kehadiran']) }}% dibanding periode lalu.</div>
            @endif
        </div>
    </div>

    {{-- Top & Bottom Kegiatan --}}
    <div class="two-col">
        <div class="chart-box">
            <h4><i class="fas fa-trophy" style="color:#10B981;"></i> Top 5 Kegiatan Terbaik</h4>
            @forelse($topKegiatan as $i => $kg)
                <div class="rank-card top" style="margin-bottom: 8px;">
                    <div class="rank-num">{{ $i + 1 }}</div>
                    <div class="rank-info">
                        <div class="name">{{ $kg['nama_kegiatan'] }}</div>
                        <div class="meta">{{ $kg['nama_kategori'] ?? '-' }}</div>
                    </div>
                    <div class="progress-inline">
                        <div class="progress-bar-mini"><div class="fill" style="width:{{ $kg['persen'] }}%; background:#10B981;"></div></div>
                        <span class="rank-persen" style="color:#10B981;">{{ $kg['persen'] }}%</span>
                    </div>
                </div>
            @empty
                <p style="color:var(--text-light); font-size:0.85rem;">Belum ada data</p>
            @endforelse
        </div>
        <div class="chart-box">
            <h4><i class="fas fa-exclamation-triangle" style="color:#EF4444;"></i> 5 Kegiatan Perlu Evaluasi</h4>
            @forelse($bottomKegiatan as $i => $kg)
                <div class="rank-card bottom" style="margin-bottom: 8px;">
                    <div class="rank-num">{{ $i + 1 }}</div>
                    <div class="rank-info">
                        <div class="name">{{ $kg['nama_kegiatan'] }}</div>
                        <div class="meta">{{ $kg['nama_kategori'] ?? '-' }}</div>
                    </div>
                    <div class="progress-inline">
                        <div class="progress-bar-mini"><div class="fill" style="width:{{ $kg['persen'] }}%; background:#EF4444;"></div></div>
                        <span class="rank-persen" style="color:#EF4444;">{{ $kg['persen'] }}%</span>
                    </div>
                </div>
            @empty
                <p style="color:var(--text-light); font-size:0.85rem;">Belum ada data</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ═══════════════ TAB 2: ANALISIS KELAS ═══════════════ --}}
<div class="tab-pane" id="tab-kelas">
    {{-- Kehadiran Per Kelompok Bar Chart --}}
    <div class="chart-box">
        <h4><i class="fas fa-chart-bar"></i> Kehadiran Per Kelompok Kelas</h4>
        <div class="chart-container" style="height:300px;">
            <canvas id="kelasBarChart"></canvas>
        </div>
    </div>

    {{-- Heatmap Table --}}
    <div class="chart-box">
        <h4><i class="fas fa-th"></i> Heatmap: Kelas vs Kategori Kegiatan</h4>
        <div style="overflow-x:auto;">
            <table class="heatmap-table">
                <thead>
                    <tr>
                        <th style="text-align:left; min-width:140px;">Kelas</th>
                        @foreach($heatmapData['columns'] as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                        <th>Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($heatmapData['rows'] as $row)
                        <tr>
                            <td class="row-label">
                                <span style="font-size:0.72rem; color:var(--text-light);">{{ $row['kelompok'] }}</span><br>
                                {{ $row['kelas'] }}
                            </td>
                            @php $rowVals = []; @endphp
                            @foreach($heatmapData['columns'] as $col)
                                @php
                                    $val = $row['data'][$col] ?? null;
                                    if ($val !== null) $rowVals[] = $val;
                                    $cls = $val === null ? 'heatmap-na' : ($val >= 90 ? 'heatmap-90' : ($val >= 80 ? 'heatmap-80' : ($val >= 70 ? 'heatmap-70' : 'heatmap-low')));
                                @endphp
                                <td><div class="heatmap-cell {{ $cls }}" title="{{ $row['kelas'] }} — {{ $col }}: {{ $val !== null ? $val.'%' : 'N/A' }}">{{ $val !== null ? $val.'%' : '-' }}</div></td>
                            @endforeach
                            @php $avg = count($rowVals) > 0 ? round(array_sum($rowVals)/count($rowVals),1) : null; @endphp
                            <td><div class="heatmap-cell {{ $avg === null ? 'heatmap-na' : ($avg >= 90 ? 'heatmap-90' : ($avg >= 80 ? 'heatmap-80' : ($avg >= 70 ? 'heatmap-70' : 'heatmap-low'))) }}"><strong>{{ $avg !== null ? $avg.'%' : '-' }}</strong></div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:12px; display:flex; gap:16px; font-size:0.78rem; flex-wrap:wrap;">
            <span><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:#10B981;vertical-align:middle;"></span> ≥90%</span>
            <span><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:#34D399;vertical-align:middle;"></span> 80-89%</span>
            <span><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:#FBBF24;vertical-align:middle;"></span> 70-79%</span>
            <span><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:#EF4444;vertical-align:middle;"></span> &lt;70%</span>
            <span><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:#F3F4F6;vertical-align:middle;"></span> N/A</span>
        </div>
    </div>

    {{-- Accordion per kelompok --}}
    <div class="chart-box">
        <h4><i class="fas fa-layer-group"></i> Detail Per Kelas</h4>
        @foreach($kehadiranPerKelas as $kelompok)
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <span><i class="fas fa-folder-open" style="color:var(--primary-color);margin-right:8px;"></i> {{ $kelompok['nama_kelompok'] }} ({{ count($kelompok['kelas']) }} kelas)</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="accordion-body">
                    <table class="data-table" style="font-size:0.85rem;">
                        <thead>
                            <tr>
                                <th>Kelas</th><th class="text-center">Santri</th><th class="text-center">Hadir</th><th class="text-center">Izin</th><th class="text-center">Sakit</th><th class="text-center">Alpa</th><th class="text-center">% Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelompok['kelas'] as $k)
                                <tr>
                                    <td><strong>{{ $k['nama_kelas'] }}</strong></td>
                                    <td class="text-center">{{ $k['jumlah_santri'] }}</td>
                                    <td class="text-center"><span class="badge badge-success">{{ $k['hadir'] }}</span></td>
                                    <td class="text-center"><span class="badge badge-warning">{{ $k['izin'] }}</span></td>
                                    <td class="text-center"><span class="badge badge-info">{{ $k['sakit'] }}</span></td>
                                    <td class="text-center"><span class="badge badge-danger">{{ $k['alpa'] }}</span></td>
                                    <td class="text-center">
                                        <div class="progress-inline" style="justify-content:center;">
                                            <div class="progress-bar-mini"><div class="fill" style="width:{{ $k['persen'] }}%; background:{{ $k['persen'] >= 85 ? '#10B981' : ($k['persen'] >= 70 ? '#FBBF24' : '#EF4444') }};"></div></div>
                                            <strong>{{ $k['persen'] }}%</strong>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ═══════════════ TAB 3: ANALISIS SANTRI ═══════════════ --}}
<div class="tab-pane" id="tab-santri">
    <div class="two-col">
        {{-- Santri Perlu Perhatian --}}
        <div class="chart-box">
            <h4><i class="fas fa-user-clock" style="color:#EF4444;"></i> Santri Perlu Perhatian (&lt;70%)</h4>
            @if($santriPerluPerhatianList && $santriPerluPerhatianList->count() > 0)
                <table class="data-table" style="font-size:0.85rem;">
                    <thead><tr><th>No</th><th>Nama</th><th class="text-center">Alpa</th><th class="text-center">Kehadiran</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach($santriPerluPerhatianList as $i => $s)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ $s->nama_lengkap }}</strong></td>
                                <td class="text-center"><span class="badge badge-danger">{{ $s->alpa }}x</span></td>
                                <td class="text-center">
                                    <div class="progress-inline" style="justify-content:center;">
                                        <div class="progress-bar-mini"><div class="fill" style="width:{{ $s->persen }}%; background:#EF4444;"></div></div>
                                        <strong style="color:#EF4444;">{{ $s->persen }}%</strong>
                                    </div>
                                </td>
                                <td><a href="{{ route('admin.laporan-kegiatan.detail-santri', $s->id_santri) }}" class="btn btn-sm btn-info" title="Detail"><i class="fas fa-eye"></i></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{ route('admin.laporan-kegiatan.santri-perlu-perhatian', request()->query()) }}" class="btn btn-sm btn-secondary" style="margin-top:12px;">
                    <i class="fas fa-list"></i> Lihat Semua
                </a>
            @else
                <div class="insight-box i-success"><i class="fas fa-check-circle"></i> Tidak ada santri dengan kehadiran di bawah 70%. Alhamdulillah!</div>
            @endif
        </div>

        {{-- Leaderboard --}}
        <div class="chart-box">
            <h4><i class="fas fa-medal" style="color:#F59E0B;"></i> Leaderboard Kehadiran Terbaik</h4>
            @if($leaderboard && $leaderboard->count() > 0)
                <table class="data-table leaderboard-table" style="font-size:0.85rem;">
                    <thead><tr><th style="width:40px;">#</th><th>Nama</th><th class="text-center">Kehadiran</th><th class="text-center">Streak</th></tr></thead>
                    <tbody>
                        @foreach($leaderboard as $i => $s)
                            <tr>
                                <td class="text-center">
                                    @if($i === 0) <span class="medal">🥇</span>
                                    @elseif($i === 1) <span class="medal">🥈</span>
                                    @elseif($i === 2) <span class="medal">🥉</span>
                                    @else {{ $i + 1 }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.laporan-kegiatan.detail-santri', $s->id_santri) }}" style="color:inherit;text-decoration:none;">
                                        <strong>{{ $s->nama_lengkap }}</strong>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <strong style="color:#10B981;">{{ $s->persen }}%</strong>
                                </td>
                                <td class="text-center">
                                    @if($s->streak > 0)
                                        <span class="streak-badge">🔥 {{ $s->streak }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color:var(--text-light); font-size:0.85rem;">Belum ada data kehadiran.</p>
            @endif
        </div>
    </div>

    {{-- Distribusi Santri Chart (duplicate for this tab) --}}
    <div class="chart-box">
        <h4><i class="fas fa-chart-pie"></i> Distribusi Kehadiran Santri</h4>
        <div class="chart-container" style="height:280px;">
            <canvas id="distribusiChart2"></canvas>
        </div>
        <div style="margin-top:12px;">
            @foreach($distribusiSantri as $d)
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px; font-size:0.85rem;">
                    <div style="flex:1;"><strong>{{ $d['label'] }}</strong></div>
                    <div style="width:60px; text-align:right;">{{ $d['count'] }} santri</div>
                    <div style="width:60px; text-align:right; color:var(--text-light);">({{ $d['percentage'] }}%)</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ═══════════════ TAB 4: ANALISIS KEGIATAN ═══════════════ --}}
<div class="tab-pane" id="tab-kegiatan">
    <div class="chart-box">
        <h4><i class="fas fa-tasks"></i> Performa Semua Kegiatan</h4>
        <div style="overflow-x:auto;">
            <table class="data-table" style="font-size:0.85rem;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kegiatan</th>
                        <th>Kategori</th>
                        <th>Hari</th>
                        <th class="text-center">Total Absensi</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-center" style="min-width:180px;">% Kehadiran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kegiatanPerformance as $i => $kg)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $kg->nama_kegiatan }}</strong></td>
                            <td><span class="badge badge-info">{{ $kg->nama_kategori ?? '-' }}</span></td>
                            <td>{{ $kg->hari ?? '-' }}</td>
                            <td class="text-center">{{ $kg->total }}</td>
                            <td class="text-center"><span class="badge badge-success">{{ $kg->hadir }}</span></td>
                            <td class="text-center">
                                <div class="progress-inline" style="justify-content:center;">
                                    <div class="progress-bar-mini"><div class="fill" style="width:{{ $kg->persen }}%; background:{{ $kg->persen >= 85 ? '#10B981' : ($kg->persen >= 70 ? '#FBBF24' : '#EF4444') }};"></div></div>
                                    <strong style="color:{{ $kg->persen >= 85 ? '#10B981' : ($kg->persen >= 70 ? '#92400E' : '#EF4444') }};">{{ $kg->persen }}%</strong>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.laporan-kegiatan.analisis-kegiatan', $kg->kegiatan_id) }}" class="btn btn-sm btn-primary" title="Analisis Detail">
                                    <i class="fas fa-search-plus"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center" style="color:var(--text-light);">Belum ada data kegiatan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════ TAB 5: PATTERN & ANOMALY ═══════════════ --}}
<div class="tab-pane" id="tab-pattern">
    <div class="chart-box">
        <h4><i class="fas fa-brain"></i> Pola & Anomali Terdeteksi</h4>
        <p style="font-size:0.82rem; color:var(--text-light); margin-bottom:16px;">Sistem secara otomatis mendeteksi pola kehadiran dan anomali berdasarkan data 3 minggu terakhir.</p>

        @if(!empty($patterns) && count($patterns) > 0)
            @foreach($patterns as $p)
                <div class="pattern-card p-{{ $p['type'] }}">
                    <div class="pattern-category">{{ $p['category'] }}</div>
                    <div class="pattern-title">{{ $p['title'] }}</div>
                    <div class="pattern-desc">{{ $p['description'] }}</div>
                    @if(!empty($p['action_url']))
                        <a href="{{ $p['action_url'] }}" class="btn btn-sm btn-{{ $p['type'] === 'danger' ? 'danger' : ($p['type'] === 'warning' ? 'warning' : 'info') }}" style="margin-top:8px;">
                            <i class="fas fa-arrow-right"></i> {{ $p['action_text'] }}
                        </a>
                    @endif
                </div>
            @endforeach
        @else
            <div class="insight-box i-success">
                <i class="fas fa-check-circle"></i>
                Tidak ada anomali terdeteksi. Pola kehadiran dalam kondisi normal.
            </div>
        @endif
    </div>

    {{-- Prediksi Trend --}}
    <div class="chart-box">
        <h4><i class="fas fa-chart-line"></i> Prediksi Trend Minggu Depan</h4>
        <div class="chart-container" style="height:250px;">
            <canvas id="prediksiChart"></canvas>
        </div>
        <div class="insight-box i-info" style="margin-top:12px;">
            <i class="fas fa-robot"></i>
            Prediksi ini berdasarkan tren 4 minggu terakhir menggunakan regresi linear sederhana. Akurasi bervariasi.
        </div>
    </div>
</div>

{{-- ═══════════════ TAB 6: EXPORT ═══════════════ --}}
<div class="tab-pane" id="tab-export">
    <div class="export-grid">
        {{-- Export Options --}}
        <div class="export-card">
            <h4><i class="fas fa-file-download"></i> Download Laporan</h4>
            <form id="exportForm">
                <input type="hidden" name="periode" value="{{ $periode }}">
                <input type="hidden" name="tanggal_dari" value="{{ $startDate->format('Y-m-d') }}">
                <input type="hidden" name="tanggal_sampai" value="{{ $endDate->format('Y-m-d') }}">

                <label style="font-size:0.85rem; font-weight:600; margin-bottom:8px; display:block;">Pilih Konten:</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="content[]" value="summary" checked> Executive Summary (KPI)</label>
                    <label><input type="checkbox" name="content[]" value="per_kelas"> Analisis Per Kelas</label>
                    <label><input type="checkbox" name="content[]" value="per_santri"> Detail Per Santri <small style="color:var(--text-light);">(file besar)</small></label>
                    <label><input type="checkbox" name="content[]" value="top_bottom"> Top & Bottom Kegiatan</label>
                    <label><input type="checkbox" name="content[]" value="patterns"> Pola & Anomali</label>
                </div>

                <div style="display:flex; gap:8px; margin-top:16px;">
                    <button type="button" onclick="exportExcel()" class="btn btn-success"><i class="fas fa-file-excel"></i> Download Excel (CSV)</button>
                    <a href="{{ route('admin.laporan-kegiatan.export-pdf', request()->query()) }}" class="btn btn-danger" target="_blank"><i class="fas fa-file-pdf"></i> Download PDF</a>
                </div>
            </form>
        </div>

        {{-- Quick Templates --}}
        <div class="export-card">
            <h4><i class="fas fa-file-alt"></i> Template Laporan Cepat</h4>
            <div style="display:flex; flex-direction:column; gap:10px;">
                <a href="{{ route('admin.laporan-kegiatan.export-pdf', ['periode'=>'minggu_ini']) }}" class="btn btn-secondary" target="_blank" style="text-align:left;">
                    <i class="fas fa-calendar-week"></i> Laporan Mingguan untuk Pimpinan
                </a>
                <a href="{{ route('admin.laporan-kegiatan.export-pdf', ['periode'=>'bulan_ini']) }}" class="btn btn-secondary" target="_blank" style="text-align:left;">
                    <i class="fas fa-calendar-alt"></i> Laporan Bulanan untuk Yayasan
                </a>
                <a href="{{ route('admin.laporan-kegiatan.export-excel', ['periode'=>'bulan_ini','content'=>['summary','per_kelas','per_santri']]) }}" class="btn btn-secondary" target="_blank" style="text-align:left;">
                    <i class="fas fa-table"></i> Data Lengkap (CSV) - Bulan Ini
                </a>
            </div>

            <h4 style="margin-top:24px;"><i class="fas fa-print"></i> Cetak Langsung</h4>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Cetak Halaman Ini
            </button>
        </div>
    </div>
</div>

{{-- ═════════════════ CHART.JS CDN ═════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
// ── Tab Management ──
document.querySelectorAll('.laporan-tab').forEach(tab => {
    tab.addEventListener('click', () => switchTab(tab.dataset.tab));
});

function switchTab(tabId) {
    document.querySelectorAll('.laporan-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');
    document.getElementById('tab-' + tabId).classList.add('active');

    // Lazy-load charts
    if (tabId === 'kelas' && !window._kelasChartInited) initKelasChart();
    if (tabId === 'santri' && !window._distribusi2Inited) initDistribusi2();
    if (tabId === 'pattern' && !window._prediksiInited) initPrediksiChart();
}

// ── Period Selector ──
function setPeriode(p) {
    document.getElementById('periodeInput').value = p;
    document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');
    document.getElementById('customRange').classList.toggle('show', p === 'custom');
    if (p !== 'custom') document.getElementById('periodForm').submit();
}

// ── Accordion ──
function toggleAccordion(el) {
    el.classList.toggle('open');
    el.nextElementSibling.classList.toggle('open');
}

// ═══ Chart Colors ═══
const chartColors = ['#10B981','#3B82F6','#F59E0B','#EF4444','#8B5CF6','#EC4899','#06B6D4','#84CC16'];

// ═══ TREND CHART (Tab 1) ═══
const trendData = @json($trendData);
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendData.labels,
        datasets: trendData.datasets.map((ds, i) => ({
            label: ds.kategori,
            data: ds.data,
            borderColor: chartColors[i % chartColors.length],
            backgroundColor: chartColors[i % chartColors.length] + '20',
            tension: 0.4,
            fill: false,
            pointRadius: 4,
            pointHoverRadius: 6,
            spanGaps: true,
        }))
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { usePointStyle: true, padding: 16 } },
            tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + (ctx.parsed.y ?? '-') + '%' } }
        },
        scales: {
            y: { min: 0, max: 100, ticks: { callback: v => v + '%' }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});

// ═══ DISTRIBUSI CHART (Tab 1) ═══
const distribusi = @json($distribusiSantri);
const distribusiColors = ['#10B981','#34D399','#60A5FA','#FBBF24','#EF4444'];
new Chart(document.getElementById('distribusiChart'), {
    type: 'bar',
    data: {
        labels: distribusi.map(d => d.label),
        datasets: [{
            data: distribusi.map(d => d.count),
            backgroundColor: distribusiColors,
            borderRadius: 6,
            barThickness: 28,
        }]
    },
    options: {
        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.raw + ' santri (' + distribusi[ctx.dataIndex].percentage + '%)' } }
        },
        scales: {
            x: { grid: { color: '#f1f5f9' }, ticks: { stepSize: 1 } },
            y: { grid: { display: false } }
        }
    }
});

// ═══ KELAS BAR CHART (Tab 2 - lazy) ═══
function initKelasChart() {
    window._kelasChartInited = true;
    const kelasData = @json($kehadiranPerKelas);
    const labels = [];
    const data = [];
    const bgColors = [];
    kelasData.forEach((kelompok, ki) => {
        kelompok.kelas.forEach(k => {
            labels.push(k.nama_kelas);
            data.push(k.persen);
            bgColors.push(chartColors[ki % chartColors.length]);
        });
    });
    new Chart(document.getElementById('kelasBarChart'), {
        type: 'bar',
        data: { labels, datasets: [{ label: '% Kehadiran', data, backgroundColor: bgColors, borderRadius: 6 }] },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.raw + '%' } } },
            scales: { y: { min: 0, max: 100, ticks: { callback: v => v + '%' } }, x: { grid: { display: false } } }
        }
    });
}

// ═══ DISTRIBUSI 2 (Tab 3 - lazy) ═══
function initDistribusi2() {
    window._distribusi2Inited = true;
    new Chart(document.getElementById('distribusiChart2'), {
        type: 'doughnut',
        data: {
            labels: distribusi.map(d => d.label),
            datasets: [{ data: distribusi.map(d => d.count), backgroundColor: distribusiColors, borderWidth: 2, hoverOffset: 8 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 12 } } }
        }
    });
}

// ═══ PREDIKSI CHART (Tab 5 - lazy) ═══
function initPrediksiChart() {
    window._prediksiInited = true;
    const t = trendData.datasets[0] ? trendData.datasets[0].data.filter(v => v !== null) : [];
    const prediksi = t.length >= 2 ? Math.max(0, Math.min(100, Math.round(t[t.length-1] + (t[t.length-1] - t[t.length-2]) * 0.5))) : null;
    const allLabels = [...trendData.labels];
    const histData = t.length > 0 ? [...t] : [];
    const predData = new Array(histData.length).fill(null);
    if (prediksi !== null) {
        allLabels.push('Prediksi');
        histData.push(null);
        predData.push(prediksi);
        predData[predData.length-2] = t[t.length-1]; // connect line
    }
    new Chart(document.getElementById('prediksiChart'), {
        type: 'line',
        data: {
            labels: allLabels,
            datasets: [
                { label: 'Aktual', data: histData, borderColor: '#3B82F6', tension: 0.4, pointRadius: 4, spanGaps: false },
                { label: 'Prediksi', data: predData, borderColor: '#F59E0B', borderDash: [6,4], tension: 0.4, pointRadius: 6, spanGaps: false, pointStyle: 'star' }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: { y: { min: 0, max: 100, ticks: { callback: v => v + '%' } } }
        }
    });
}

// ═══ Export Functions ═══
function exportExcel() {
    const form = document.getElementById('exportForm');
    const formData = new FormData(form);
    const params = new URLSearchParams();
    formData.forEach((v, k) => params.append(k, v));
    window.open('{{ route("admin.laporan-kegiatan.export-excel") }}?' + params.toString(), '_blank');
}

// ═══ Auto Refresh KPI (every 5 min) ═══
setInterval(() => {
    fetch('{{ route("admin.laporan-kegiatan.refresh-kpi") }}?periode={{ $periode }}&tanggal_dari={{ $startDate->format("Y-m-d") }}&tanggal_sampai={{ $endDate->format("Y-m-d") }}')
        .then(r => r.json())
        .then(data => {
            // Silently update – could update DOM elements if needed
            console.log('KPI refreshed', data);
        })
        .catch(() => {});
}, 300000);

// ═══ Print Styles ═══
window.addEventListener('beforeprint', () => {
    document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'block');
});
window.addEventListener('afterprint', () => {
    document.querySelectorAll('.tab-pane').forEach(p => {
        p.style.display = p.classList.contains('active') ? 'block' : 'none';
    });
});
</script>

{{-- Print-specific CSS --}}
<style>
@media print {
    .page-header .btn, .period-bar, .laporan-tabs, .btn, button, .export-card, .accordion-header .arrow,
    #tab-export { display: none !important; }
    .tab-pane { display: block !important; page-break-inside: avoid; }
    .content-box { box-shadow: none !important; border: 1px solid #e2e8f0; }
    .chart-box { box-shadow: none !important; border: 1px solid #e2e8f0; page-break-inside: avoid; }
    .kpi-card { box-shadow: none !important; border: 1px solid #e2e8f0; }
}
</style>
@endsection
