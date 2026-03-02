{{-- resources/views/admin/kegiatan/data/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
@php
    // -- Day-tab calculations --
    $isoDay       = $selectedDate->dayOfWeekIso;
    $monOfWeek    = $selectedDate->copy()->subDays($isoDay - 1);
    $hariMapTab   = [
        'Senin' => 'Senin','Selasa' => 'Selasa','Rabu' => 'Rabu',
        'Kamis' => 'Kamis','Jumat' => 'Jumat','Sabtu' => 'Sabtu','Minggu' => 'Ahad'
    ];
    $activeTab = $hariMapTab[$selectedDate->locale('id')->isoFormat('dddd')] ?? 'Senin';
    $todayHari = $hariMapTab[now()->locale('id')->isoFormat('dddd')] ?? 'Senin';

    $tabHari = [
        ['nama' => 'Senin',  'offset' => 0],
        ['nama' => 'Selasa', 'offset' => 1],
        ['nama' => 'Rabu',   'offset' => 2],
        ['nama' => 'Kamis',  'offset' => 3],
        ['nama' => 'Jumat',  'offset' => 4],
        ['nama' => 'Sabtu',  'offset' => 5],
        ['nama' => 'Minggu',   'offset' => 6],
    ];

    $kelompokGroups = $kelasList->groupBy('kelompok.nama_kelompok');
@endphp

{{-- ============================================================ --}}
{{-- PAGE HEADER                                                   --}}
{{-- ============================================================ --}}
<div class="page-header">
    <h2><i class="fas fa-tachometer-alt"></i> Dashboard Absensi</h2>
</div>

<p style="color: var(--text-light); margin-top: 5px; margin-bottom: 14px;">
    {{ $selectedDate->locale('id')->isoFormat('dddd, D MMMM Y') }}
</p>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

{{-- ============================================================ --}}
{{-- 1. KPI CARDS                                                  --}}
{{-- ============================================================ --}}
<div class="kpi-grid-kegiatan">
    <div class="kpi-kegiatan bg-primary">
        <i class="fas fa-calendar-day kpi-icon"></i>
        <div class="kpi-value">{{ $totalKegiatanHariIni }}</div>
        <div class="kpi-label">Total Kegiatan</div>
        <div class="kpi-sub">
            <i class="fas fa-{{ $comparisonTotal >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
            {{ abs($comparisonTotal) }} vs minggu lalu
        </div>
    </div>

    <div class="kpi-kegiatan bg-success">
        <i class="fas fa-check-circle kpi-icon"></i>
        <div class="kpi-value">{{ $kegiatanSelesai }}</div>
        <div class="kpi-label">Kegiatan Selesai</div>
        <div class="kpi-sub">dari {{ $totalKegiatanHariIni }} kegiatan</div>
    </div>

    <div class="kpi-kegiatan bg-warning">
        <i class="fas fa-chart-line kpi-icon"></i>
        <div class="kpi-value">{{ $avgKehadiran }}%</div>
        <div class="kpi-label">Rata-rata Kehadiran</div>
        <div class="kpi-sub">
            <i class="fas fa-{{ $comparisonAvg >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
            {{ abs($comparisonAvg) }}% vs minggu lalu
        </div>
    </div>

    <div class="kpi-kegiatan bg-info">
        <i class="fas fa-clock kpi-icon"></i>
        <div class="kpi-value">{{ $kegiatanBerlangsung }}</div>
        <div class="kpi-label">Sedang Berlangsung</div>
        @if($kegiatanBerlangsung > 0)
            <div class="kpi-sub"><i class="fas fa-circle" style="color: #90ee90;"></i> Live Now</div>
        @else
            <div class="kpi-sub">Tidak ada kegiatan</div>
        @endif
    </div>
</div>

{{-- ============================================================ --}}
{{-- 2. DAY TABS (Senin - Ahad)                                   --}}
{{-- ============================================================ --}}
<div class="content-box">
    <div class="day-tabs">
        @foreach($tabHari as $tab)
            @php
                $tabDate    = $monOfWeek->copy()->addDays($tab['offset']);
                $tabDateStr = $tabDate->format('Y-m-d');
                $isActive   = ($activeTab === $tab['nama']);
                $isToday    = ($todayHari === $tab['nama'] && now()->format('W') === $selectedDate->format('W'));
                $params     = array_merge(
                    request()->only(['kelas', 'kategori_id']),
                    ['tanggal' => $tabDateStr]
                );
            @endphp
            <a href="{{ route('admin.kegiatan.index', $params) }}"
               class="day-tab {{ $isActive ? 'active' : '' }} {{ $isToday ? 'today-tab' : '' }}">
                <span class="day-name">{{ $tab['nama'] }}</span>
                <span class="day-date">{{ $tabDate->format('d/m') }}</span>
            </a>
        @endforeach
    </div>

    {{-- ======================================================== --}}
    {{-- 3. FILTER BAR (Tanggal + Kategori + Kelas + Aksi)        --}}
    {{-- ======================================================== --}}
    <form method="GET" action="{{ route('admin.kegiatan.index') }}" id="filterForm" class="filter-form-inline" style="margin-top: 10px;">
        <input type="hidden" name="kelas" id="kelasInput" value="{{ $selectedKelasId }}">

        <div class="filter-form-inline" style="gap: 8px;">
            <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                <i class="fas fa-calendar"></i> Tanggal:
            </label>
            <input type="date" name="tanggal" class="form-control"
                   value="{{ $selectedDate->format('Y-m-d') }}"
                   onchange="this.form.submit()" style="max-width: 170px;">
        </div>

        <div class="filter-form-inline" style="gap: 8px;">
            <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                <i class="fas fa-tags"></i> Kategori:
            </label>
            <select name="kategori_id" class="form-control" onchange="this.form.submit()" style="max-width: 180px;">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->kategori_id }}" {{ request('kategori_id') == $kat->kategori_id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-form-inline" style="gap: 8px;">
            <label style="font-weight: 600; white-space: nowrap; margin: 0;">
                <i class="fas fa-school"></i> Kelas:
            </label>
            <select name="kelas" class="form-control" onchange="this.form.submit()" style="max-width: 200px;">
                <option value="">Semua Kelas</option>
                <option value="umum" {{ $selectedKelasId === 'umum' ? 'selected' : '' }}>Kegiatan Umum</option>
                @foreach($kelompokGroups as $kelompokNama => $kelasGroup)
                    <optgroup label="{{ $kelompokNama }}">
                        @foreach($kelasGroup as $kelas)
                            <option value="{{ $kelas->id }}" {{ $selectedKelasId == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <button type="button" class="btn btn-sm btn-primary" onclick="setToday()">
            <i class="fas fa-calendar-day"></i> Hari Ini
        </button>
        <button type="button" class="btn btn-sm btn-secondary" onclick="prevDay()">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button type="button" class="btn btn-sm btn-secondary" onclick="nextDay()">
            <i class="fas fa-chevron-right"></i>
        </button>

        {{-- <div style="margin-left: auto; display: flex; gap: 8px;">
            <a href="{{ route('admin.kategori-kegiatan.index') }}" class="btn btn-info btn-sm">
                <i class="fas fa-tags"></i> Kategori
            </a>
            <a href="{{ route('admin.kegiatan.jadwal') }}" class="btn btn-info btn-sm">
                <i class="fas fa-list"></i> Semua Jadwal
            </a>
        </div> --}}
    </form>
</div>

{{-- ============================================================ --}}
{{-- 4. INSIGHTS PANEL                                             --}}
{{-- ============================================================ --}}
@if(count($insights) > 0)
<div class="content-box" style="margin-top: 14px;">
    <h4 style="margin: 0 0 12px; color: var(--primary-color);">
        <i class="fas fa-lightbulb"></i> Insight Hari Ini
    </h4>
    @foreach($insights as $insight)
        <div class="insight-item {{ $insight['type'] }}">
            <div class="insight-content">
                <div class="insight-message">
                    <i class="fas fa-{{ $insight['icon'] }}"></i> {{ $insight['message'] }}
                </div>
                <div class="insight-detail">{{ $insight['detail'] }}</div>
            </div>
            @if($insight['action_url'])
                <a href="{{ $insight['action_url'] }}" class="btn btn-sm btn-{{ $insight['type'] }}">
                    {{ $insight['action_text'] }}
                </a>
            @endif
        </div>
    @endforeach
</div>
@endif

{{-- ============================================================ --}}
{{-- 5. MAIN LAYOUT: Kegiatan Cards (2/3) + Heatmap (1/3)         --}}
{{-- ============================================================ --}}
<div class="layout-kegiatan" style="margin-top: 14px;">

    {{-- LEFT: Kegiatan Cards --}}
    <div>
        @if($kegiatanHariIni->count() > 0)
            <div class="kegiatan-list">
                @foreach($kegiatanHariIni as $kegiatan)
                    <div class="kegiatan-card">
                        <div class="kegiatan-card-header">
                            <div class="kegiatan-info">
                                <h3 class="kegiatan-title">
                                    <i class="fas fa-calendar" style="color: var(--primary-color);"></i>
                                    {{ $kegiatan->nama_kegiatan }}
                                </h3>
                                <div class="kegiatan-meta">
                                    <span>
                                        <i class="fas fa-clock"></i>
                                        {{ date('H:i', strtotime($kegiatan->waktu_mulai)) }} &ndash;
                                        {{ date('H:i', strtotime($kegiatan->waktu_selesai)) }}
                                    </span>
                                    <span class="badge badge-info">
                                        <i class="fas fa-tag"></i>
                                        {{ $kegiatan->kategori->nama_kategori }}
                                    </span>
                                    @if($kegiatan->materi)
                                        <span>
                                            <i class="fas fa-book"></i>
                                            {{ Str::limit($kegiatan->materi, 40) }}
                                        </span>
                                    @endif
                                    <span>
                                        <i class="fas fa-layer-group"></i>
                                        @if($kegiatan->kelasKegiatan->isEmpty())
                                            <span class="badge badge-secondary">Kegiatan Umum</span>
                                        @else
                                            {{ $kegiatan->kelasKegiatan->pluck('nama_kelas')->implode(', ') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <span class="status-badge status-{{ $kegiatan->status_kegiatan }}">
                                @if($kegiatan->status_kegiatan == 'belum')
                                    <i class="fas fa-hourglass-start"></i> Belum Dimulai
                                @elseif($kegiatan->status_kegiatan == 'berlangsung')
                                    <i class="fas fa-play-circle"></i> Berlangsung
                                @else
                                    <i class="fas fa-check-circle"></i> Selesai
                                @endif
                            </span>
                        </div>

                        {{-- Progress Bar --}}
                        @php
                            $persen = $kegiatan->persen_kehadiran;
                            $pClass = $persen >= 85 ? 'p-success' : ($persen >= 70 ? 'p-warning' : ($persen >= 50 ? 'p-orange' : 'p-danger'));
                            $denominator = $kegiatan->total_absensi > 0 ? $kegiatan->total_absensi : $totalSantriAktif;
                        @endphp
                        <div class="kegiatan-progress">
                            <div class="kegiatan-progress-header">
                                <span style="font-weight: 500;">
                                    <i class="fas fa-users"></i> Kehadiran
                                </span>
                                <span style="font-weight: 700;">
                                    {{ $kegiatan->total_hadir }}/{{ $denominator }}
                                    ({{ $persen }}%)
                                </span>
                            </div>
                            <div class="kegiatan-progress-bar">
                                <div class="kegiatan-progress-fill {{ $pClass }}"
                                     data-width="{{ $persen }}">
                                    {{ $persen }}%
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="kegiatan-actions">
                            <a href="{{ route('admin.absensi-kegiatan.input', $kegiatan->kegiatan_id) }}?tanggal={{ $selectedDate->format('Y-m-d') }}"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-clipboard-check"></i> Input Absensi
                            </a>
                            <button type="button" class="btn btn-sm btn-info"
                                    data-id="{{ $kegiatan->kegiatan_id }}"
                                    data-tanggal="{{ $selectedDate->format('Y-m-d') }}"
                                    onclick="showDetailModal(this.getAttribute('data-id'), this.getAttribute('data-tanggal'))">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </button>
                            <a href="{{ route('admin.absensi-kegiatan.rekap', $kegiatan->kegiatan_id) }}?tanggal={{ $selectedDate->format('Y-m-d') }}"
                               class="btn btn-sm btn-secondary">
                                <i class="fas fa-chart-bar"></i> Rekap
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>Tidak Ada Kegiatan Dijadwalkan</h3>
                <p>Tidak ada kegiatan untuk
                    {{ $selectedKelasId ? 'kelas ini' : 'hari ini' }}
                    pada {{ $selectedDate->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </p>
                <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Buat Kegiatan Baru
                </a>
            </div>
        @endif
    </div>

    {{-- RIGHT: Heatmap Calendar --}}
    <div>
        <div class="heatmap-calendar">
            <div class="heatmap-header">
                <i class="fas fa-calendar-alt"></i>
                <span>Kalender Kehadiran</span>
            </div>

            {{-- Month/Year Selector --}}
            <div class="filter-form-inline" style="margin-bottom: 12px;">
                <button type="button" class="btn btn-sm btn-secondary" style="padding: 4px 8px;"
                        onclick="changeHeatmapMonth(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <select id="heatmapMonth" onchange="updateHeatmap()" class="form-control" style="flex: 1;">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
                <select id="heatmapYear" onchange="updateHeatmap()" class="form-control" style="width: 80px;">
                    @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="button" class="btn btn-sm btn-secondary" style="padding: 4px 8px;"
                        onclick="changeHeatmapMonth(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div style="text-align: center; font-weight: 600; color: var(--primary-color); margin-bottom: 10px;"
                 id="heatmapMonthName">
                {{ now()->locale('id')->isoFormat('MMMM Y') }}
            </div>

            <div class="heatmap-days">
                <div class="heatmap-day-label">Sen</div>
                <div class="heatmap-day-label">Sel</div>
                <div class="heatmap-day-label">Rab</div>
                <div class="heatmap-day-label">Kam</div>
                <div class="heatmap-day-label">Jum</div>
                <div class="heatmap-day-label">Sab</div>
                <div class="heatmap-day-label">Ahd</div>
            </div>

            <div class="heatmap-grid" id="heatmapGrid">
                @foreach($heatmapData as $day)
                    <div class="heatmap-cell heatmap-level-{{ $day['level'] }} {{ $day['is_today'] ? 'today' : '' }}"
                         data-date="{{ $day['date'] }}"
                         data-percentage="{{ $day['percentage'] }}"
                         title="{{ \Carbon\Carbon::parse($day['date'])->locale('id')->isoFormat('dddd, D MMM Y') }}: {{ $day['percentage'] }}%">
                        {{ \Carbon\Carbon::parse($day['date'])->format('j') }}
                    </div>
                @endforeach
            </div>

            <div class="heatmap-legend">
                <div class="heatmap-legend-title">Legend:</div>
                <div class="heatmap-legend-items">
                    <div class="heatmap-legend-item">
                        <div class="heatmap-legend-box heatmap-level-4"></div>
                        <span>&gt;90%</span>
                    </div>
                    <div class="heatmap-legend-item">
                        <div class="heatmap-legend-box heatmap-level-3"></div>
                        <span>80-90%</span>
                    </div>
                    <div class="heatmap-legend-item">
                        <div class="heatmap-legend-box heatmap-level-2"></div>
                        <span>70-80%</span>
                    </div>
                    <div class="heatmap-legend-item">
                        <div class="heatmap-legend-box heatmap-level-1"></div>
                        <span>&lt;70%</span>
                    </div>
                    <div class="heatmap-legend-item">
                        <div class="heatmap-legend-box heatmap-level-0"></div>
                        <span>No data</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- 6. MODAL DETAIL ABSENSI (AJAX)                                --}}
{{-- ============================================================ --}}
<div id="detailModal" class="modal-kegiatan">
    <div class="modal-kegiatan-panel">
        <div class="modal-kegiatan-head">
            <h3><i class="fas fa-info-circle"></i> Detail Absensi Kegiatan</h3>
            <button class="modal-kegiatan-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-kegiatan-body" id="modalBody">
            <div style="text-align: center; padding: 22px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary-color);"></i>
                <p style="margin-top: 10px;">Memuat data...</p>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- JAVASCRIPT                                                    --}}
{{-- ============================================================ --}}
<script>
// -- Date Navigation --
function setToday() {
    var dateInput = document.querySelector('input[name="tanggal"]');
    var now = new Date();
    var y = now.getFullYear();
    var m = ('0' + (now.getMonth() + 1)).slice(-2);
    var d = ('0' + now.getDate()).slice(-2);
    dateInput.value = y + '-' + m + '-' + d;
    document.getElementById('filterForm').submit();
}

function prevDay() {
    var currentDate = new Date('{{ $selectedDate->format("Y-m-d") }}');
    currentDate.setDate(currentDate.getDate() - 1);
    document.querySelector('input[name="tanggal"]').value = currentDate.toISOString().split('T')[0];
    document.getElementById('filterForm').submit();
}

function nextDay() {
    var currentDate = new Date('{{ $selectedDate->format("Y-m-d") }}');
    currentDate.setDate(currentDate.getDate() + 1);
    document.querySelector('input[name="tanggal"]').value = currentDate.toISOString().split('T')[0];
    document.getElementById('filterForm').submit();
}

function goToDate(date) {
    document.querySelector('input[name="tanggal"]').value = date;
    document.getElementById('filterForm').submit();
}

// -- Show Detail Modal (AJAX) --
function showDetailModal(kegiatanId, tanggal) {
    var modal = document.getElementById('detailModal');
    var modalBody = document.getElementById('modalBody');
    modal.classList.add('active');

    var baseUrl = '{{ route("admin.kegiatan.detail-modal", ":id") }}';
    var url = baseUrl.replace(':id', kegiatanId) + '?tanggal=' + tanggal;

    fetch(url)
        .then(function(response) { return response.text(); })
        .then(function(html) { modalBody.innerHTML = html; })
        .catch(function() {
            modalBody.innerHTML =
                '<div style="text-align:center;padding:22px;">' +
                '<i class="fas fa-exclamation-circle" style="font-size:2.2rem;color:var(--danger-color);"></i>' +
                '<h4 style="margin:20px 0 10px;color:var(--danger-color);">Gagal Memuat Data</h4>' +
                '<p style="color:var(--text-light);">Terjadi kesalahan saat memuat detail absensi.</p>' +
                '<button class="btn btn-primary" onclick="closeModal()">Tutup</button>' +
                '</div>';
        });
}

function closeModal() {
    document.getElementById('detailModal').classList.remove('active');
}

// -- Close modal on backdrop / Escape --
window.onclick = function(event) {
    var modal = document.getElementById('detailModal');
    if (event.target === modal) { closeModal(); }
};
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') { closeModal(); }
});

// -- Progress bar width animation --
document.querySelectorAll('.kegiatan-progress-fill[data-width]').forEach(function(el) {
    el.style.width = el.getAttribute('data-width') + '%';
});

// -- Heatmap: delegated click for cells --
document.getElementById('heatmapGrid').addEventListener('click', function(e) {
    var cell = e.target.closest('.heatmap-cell');
    if (cell && cell.dataset.date) {
        goToDate(cell.dataset.date);
    }
});

// -- Heatmap: month navigation --
function changeHeatmapMonth(delta) {
    var monthSelect = document.getElementById('heatmapMonth');
    var yearSelect  = document.getElementById('heatmapYear');
    var month = parseInt(monthSelect.value) + delta;
    var year  = parseInt(yearSelect.value);

    if (month > 12) { month = 1; year++; }
    else if (month < 1) { month = 12; year--; }

    monthSelect.value = month;
    yearSelect.value  = year;
    updateHeatmap();
}

// -- Heatmap: AJAX reload --
function updateHeatmap() {
    var month   = document.getElementById('heatmapMonth').value;
    var year    = document.getElementById('heatmapYear').value;
    var kelasId = document.getElementById('kelasInput').value;

    var monthNames = [
        'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];
    document.getElementById('heatmapMonthName').textContent = monthNames[month - 1] + ' ' + year;

    var url = '{{ route("admin.kegiatan.index") }}' +
              '?heatmap=1&month=' + month + '&year=' + year + '&kelas=' + kelasId;

    fetch(url)
        .then(function(response) { return response.json(); })
        .then(function(data) {
            var grid = document.getElementById('heatmapGrid');
            grid.innerHTML = '';

            // -- Empty cells for first week alignment --
            var firstDay = new Date(year, month - 1, 1).getDay();
            var startDay = firstDay === 0 ? 6 : firstDay - 1;
            var i;
            for (i = 0; i < startDay; i++) {
                var empty = document.createElement('div');
                empty.className = 'heatmap-cell';
                empty.style.visibility = 'hidden';
                grid.appendChild(empty);
            }

            // -- Render calendar cells --
            data.heatmapData.forEach(function(day) {
                var cell = document.createElement('div');
                var date = new Date(day.date);
                var today = new Date();
                var isToday = date.toDateString() === today.toDateString();

                cell.className = 'heatmap-cell heatmap-level-' + day.level + (isToday ? ' today' : '');
                cell.setAttribute('data-date', day.date);
                cell.setAttribute('data-percentage', day.percentage);
                cell.setAttribute('title', day.title);
                cell.onclick = function() { goToDate(day.date); };
                cell.textContent = date.getDate();
                grid.appendChild(cell);
            });
        })
        .catch(function(err) {
            console.error('Error loading heatmap:', err);
        });
}
</script>
@endsection