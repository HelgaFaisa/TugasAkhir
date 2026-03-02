{{-- resources/views/admin/kegiatan/riwayat/show.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-header h2 { margin: 0; color: var(--primary-dark); font-size: 1.5rem; display: flex; align-items: center; gap: 10px; }
.btn-back { padding: 8px 16px; background: #6B7280; color: #fff; border: none; border-radius: 8px; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; }
.btn-back:hover { background: #4B5563; color: #fff; }

.info-box-header { background: linear-gradient(135deg, var(--primary-color), #059669); color: #fff; padding: 20px 24px; border-radius: 12px; margin-bottom: 14px; box-shadow: 0 4px 12px rgba(16,185,129,0.2); }
.info-box-header h3 { margin: 0 0 6px; font-size: 1.3rem; display: flex; align-items: center; gap: 10px; }
.info-box-header .meta { opacity: 0.9; font-size: 0.88rem; display: flex; flex-wrap: wrap; gap: 12px; margin-top: 6px; }
.info-box-header .periode-tag { background: rgba(255,255,255,0.2); padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 5px; }

/* 6 KPI cards */
.stats-row {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 12px;
    margin-bottom: 14px;
}
@media (max-width: 1100px) { .stats-row { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 600px)  { .stats-row { grid-template-columns: repeat(2, 1fr); } }

.stat-card { background: #fff; padding: 14px 12px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 12px; }
.stat-card .icon { font-size: 1.4rem; width: 40px; height: 40px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 10px; }
.stat-card.hadir     .icon { background: #D1FAE5; color: #065F46; }
.stat-card.terlambat .icon { background: #FFF3E0; color: #E65100; }
.stat-card.izin      .icon { background: #FEF3C7; color: #92400E; }
.stat-card.sakit     .icon { background: #DBEAFE; color: #1E40AF; }
.stat-card.alpa      .icon { background: #FEE2E2; color: #991B1B; }
.stat-card.pulang    .icon { background: #F3E8FF; color: #6B21A8; }
.stat-card .content { flex: 1; min-width: 0; }
.stat-card .label { font-size: 0.78rem; color: var(--text-light); margin-bottom: 2px; }
.stat-card .value { font-size: 1.5rem; font-weight: 700; color: var(--primary-dark); line-height: 1.2; }

.filter-box  { background: #fff; padding: 14px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 14px; }
.filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 11px; }
.form-group  { margin: 0; }
.form-group label { display: block; font-size: 0.85rem; margin-bottom: 5px; color: var(--text-light); font-weight: 500; }
.form-control { width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; }
.btn-filter  { background: var(--primary-color); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
.btn-filter:hover { background: #059669; }
.btn-reset   { background: #6B7280; color: #fff; border: none; padding: 9px 12px; border-radius: 8px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
.btn-reset:hover { background: #4B5563; color: #fff; }

.day-group   { margin-bottom: 18px; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
.day-header  { background: linear-gradient(135deg, #f0fdf4 0%, #E8F7F2 100%); padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #E8F7F2; cursor: pointer; transition: background 0.2s; user-select: none; }
.day-header:hover { background: linear-gradient(135deg, #E8F7F2 0%, #d1f2e8 100%); }
.day-title   { font-weight: 700; font-size: 1rem; color: var(--primary-dark); display: flex; align-items: center; gap: 8px; }
.day-title i { color: var(--primary-color); }
.day-stats   { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.mini-badge  { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
.mini-badge.hadir     { background: #D1FAE5; color: #065F46; }
.mini-badge.terlambat { background: #FFF3E0; color: #E65100; }
.mini-badge.izin      { background: #FEF3C7; color: #92400E; }
.mini-badge.sakit     { background: #DBEAFE; color: #1E40AF; }
.mini-badge.alpa      { background: #FEE2E2; color: #991B1B; }
.mini-badge.pulang    { background: #F3E8FF; color: #6B21A8; }
.day-body table { width: 100%; border-collapse: collapse; }
.day-body table thead { background: #f8fafc; }
.day-body table th { padding: 10px 14px; text-align: left; font-weight: 600; font-size: 0.82rem; color: #64748b; border-bottom: 1px solid #e2e8f0; }
.day-body table td { padding: 9px 14px; font-size: 0.85rem; border-bottom: 1px solid #f1f5f9; }
.day-body table tbody tr:last-child td { border-bottom: none; }
.day-body table tbody tr:hover { background: #f8fafc; }
.toggle-icon { transition: transform 0.3s; font-size: 0.85rem; color: #94a3b8; }
.toggle-icon.collapsed { transform: rotate(-90deg); }

.empty-state { text-align: center; padding: 36px 20px; color: var(--text-light); background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.empty-state i { font-size: 3.5rem; margin-bottom: 14px; opacity: 0.3; display: block; }

.pagination { display: flex; justify-content: center; align-items: center; gap: 6px; margin-top: 14px; }
.pagination a, .pagination span { padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.82rem; text-decoration: none; color: var(--text-dark); transition: all 0.2s; }
.pagination a:hover { background: var(--primary-color); color: #fff; border-color: var(--primary-color); }
.pagination .active { background: var(--primary-color); color: #fff; border-color: var(--primary-color); font-weight: 600; }
.pagination .disabled { color: #cbd5e1; cursor: not-allowed; }

@media (max-width: 768px) {
    .day-header { flex-direction: column; align-items: flex-start; gap: 8px; }
    .day-body table th:nth-child(2), .day-body table td:nth-child(2) { display: none; }
    .day-body table th:nth-child(7), .day-body table td:nth-child(7) { display: none; }
}
</style>

@php
    // Ambil range dari query string (diteruskan dari index)
    $defaultDari   = now()->startOfWeek(\Carbon\Carbon::MONDAY)->format('Y-m-d');
    $defaultSampai = now()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('Y-m-d');
    $filterDari    = request('tanggal_dari',   $defaultDari);
    $filterSampai  = request('tanggal_sampai', $defaultSampai);
    $filterBulan   = request('bulan', '');

    if ($filterBulan) {
        $periodeLabel = 'Bulan ' . \Carbon\Carbon::parse($filterBulan . '-01')->locale('id')->isoFormat('MMMM Y');
    } else {
        $periodeLabel = \Carbon\Carbon::parse($filterDari)->locale('id')->isoFormat('D MMM Y')
                      . ' – '
                      . \Carbon\Carbon::parse($filterSampai)->locale('id')->isoFormat('D MMM Y');
    }

    // Hitung KPI dari data yang sudah difilter (semua halaman, bukan hanya halaman ini)
    // $stats sudah dihitung di controller berdasarkan filter — gunakan langsung
    // Tapi jika controller belum menghitung per filter, hitung dari koleksi paginator saat ini
    // Gunakan $stats dari controller jika ada, fallback ke hitung manual
    $statsHadir     = $stats['Hadir']     ?? 0;
    $statsTerlambat = $stats['Terlambat'] ?? 0;
    $statsIzin      = $stats['Izin']      ?? 0;
    $statsSakit     = $stats['Sakit']     ?? 0;
    $statsAlpa      = $stats['Alpa']      ?? 0;
    $statsPulang    = $stats['Pulang']    ?? 0;
@endphp

<div class="page-header">
    <h2><i class="fas fa-file-alt"></i> Detail Riwayat: {{ $kegiatan->nama_kegiatan }}</h2>
    <a href="{{ route('admin.riwayat-kegiatan.index', ['tanggal_dari' => $filterDari, 'tanggal_sampai' => $filterSampai]) }}"
       class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

{{-- Info Kegiatan --}}
<div class="info-box-header">
    <h3><i class="fas fa-clipboard-check"></i> {{ $kegiatan->nama_kegiatan }}</h3>
    <div class="meta">
        <span><i class="fas fa-tag"></i> {{ $kegiatan->kategori->nama_kategori }}</span>
        <span><i class="fas fa-clock"></i> {{ date('H:i', strtotime($kegiatan->waktu_mulai)) }} - {{ date('H:i', strtotime($kegiatan->waktu_selesai)) }}</span>
        <span><i class="fas fa-calendar-day"></i> {{ $kegiatan->hari }}</span>
        @if($kegiatan->kelasKegiatan->count() > 0)
            <span><i class="fas fa-users"></i> {{ $kegiatan->kelasKegiatan->pluck('nama_kelas')->implode(', ') }}</span>
        @else
            <span><i class="fas fa-globe"></i> Umum</span>
        @endif
        <span class="periode-tag"><i class="fas fa-calendar-check"></i> {{ $periodeLabel }}</span>
    </div>
</div>

{{-- 6 KPI Cards --}}
<div class="stats-row">
    <div class="stat-card hadir">
        <div class="icon"><i class="fas fa-check-circle"></i></div>
        <div class="content">
            <div class="label">Hadir</div>
            <div class="value">{{ $statsHadir }}</div>
        </div>
    </div>
    <div class="stat-card terlambat">
        <div class="icon"><i class="fas fa-clock"></i></div>
        <div class="content">
            <div class="label">Terlambat</div>
            <div class="value">{{ $statsTerlambat }}</div>
        </div>
    </div>
    <div class="stat-card izin">
        <div class="icon"><i class="fas fa-envelope"></i></div>
        <div class="content">
            <div class="label">Izin</div>
            <div class="value">{{ $statsIzin }}</div>
        </div>
    </div>
    <div class="stat-card sakit">
        <div class="icon"><i class="fas fa-heartbeat"></i></div>
        <div class="content">
            <div class="label">Sakit</div>
            <div class="value">{{ $statsSakit }}</div>
        </div>
    </div>
    <div class="stat-card alpa">
        <div class="icon"><i class="fas fa-times-circle"></i></div>
        <div class="content">
            <div class="label">Alpa</div>
            <div class="value">{{ $statsAlpa }}</div>
        </div>
    </div>
    <div class="stat-card pulang">
        <div class="icon"><i class="fas fa-home"></i></div>
        <div class="content">
            <div class="label">Pulang</div>
            <div class="value">{{ $statsPulang }}</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="filter-box">
    <form method="GET">
        {{-- Pertahankan range periode dari index --}}
        <input type="hidden" name="tanggal_dari"   value="{{ $filterDari }}">
        <input type="hidden" name="tanggal_sampai" value="{{ $filterSampai }}">

        <div class="filter-grid">
            <div class="form-group">
                <label for="id_santri">Santri</label>
                <select name="id_santri" id="id_santri" class="form-control">
                    <option value="">-- Semua Santri --</option>
                    @foreach($santris as $s)
                        <option value="{{ $s->id_santri }}" {{ request('id_santri') == $s->id_santri ? 'selected' : '' }}>
                            {{ $s->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="id_kelas">Kelas</label>
                <select name="id_kelas" id="id_kelas" class="form-control">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelasList->groupBy('kelompok.nama_kelompok') as $kelompokNama => $kelasList_group)
                        <optgroup label="{{ $kelompokNama }}">
                            @foreach($kelasList_group as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('id_kelas') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">-- Semua Status --</option>
                    <option value="Hadir"     {{ request('status') == 'Hadir'     ? 'selected' : '' }}>Hadir</option>
                    <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="Izin"      {{ request('status') == 'Izin'      ? 'selected' : '' }}>Izin</option>
                    <option value="Sakit"     {{ request('status') == 'Sakit'     ? 'selected' : '' }}>Sakit</option>
                    <option value="Alpa"      {{ request('status') == 'Alpa'      ? 'selected' : '' }}>Alpa</option>
                    <option value="Pulang"    {{ request('status') == 'Pulang'    ? 'selected' : '' }}>Pulang</option>
                </select>
            </div>

            <div class="form-group">
                <label for="tanggal_spesifik">Filter Tanggal Spesifik</label>
                <input type="date" name="tanggal_spesifik" id="tanggal_spesifik" class="form-control"
                       value="{{ request('tanggal_spesifik') }}"
                       min="{{ $filterDari }}" max="{{ $filterSampai }}">
            </div>

            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" class="btn-filter" style="flex: 1;">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['id_santri', 'id_kelas', 'status', 'tanggal_spesifik']))
                    <a href="{{ route('admin.riwayat-kegiatan.show', $kegiatan->id) }}?tanggal_dari={{ $filterDari }}&tanggal_sampai={{ $filterSampai }}"
                       class="btn-reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Tabel Riwayat — Grouped by Tanggal --}}
@if($riwayats->count() > 0)

    @php
        $grouped = $riwayats->getCollection()->groupBy(function($item) {
            return $item->tanggal->format('Y-m-d');
        })->sortKeysDesc();
    @endphp

    @foreach($grouped as $tanggal => $records)
        @php
            $tglCarbon    = \Carbon\Carbon::parse($tanggal);
            $hariIndo     = $tglCarbon->locale('id')->isoFormat('dddd');
            $tglFormatted = $tglCarbon->locale('id')->isoFormat('D MMMM Y');

            $dayHadir     = $records->where('status', 'Hadir')->count();
            $dayTerlambat = $records->where('status', 'Terlambat')->count();
            $dayIzin      = $records->where('status', 'Izin')->count();
            $daySakit     = $records->where('status', 'Sakit')->count();
            $dayAlpa      = $records->where('status', 'Alpa')->count();
            $dayPulang    = $records->where('status', 'Pulang')->count();
            $dayTotal     = $records->count();
        @endphp

        <div class="day-group">
            <div class="day-header" onclick="toggleDay(this)">
                <div class="day-title">
                    <i class="fas fa-calendar-day"></i>
                    {{ $hariIndo }}, {{ $tglFormatted }}
                    <span style="font-weight: 400; font-size: 0.85rem; color: #94a3b8; margin-left: 4px;">({{ $dayTotal }} santri)</span>
                </div>
                <div class="day-stats">
                    @if($dayHadir > 0)
                        <span class="mini-badge hadir"><i class="fas fa-check"></i> {{ $dayHadir }}</span>
                    @endif
                    @if($dayTerlambat > 0)
                        <span class="mini-badge terlambat"><i class="fas fa-clock"></i> {{ $dayTerlambat }}</span>
                    @endif
                    @if($dayIzin > 0)
                        <span class="mini-badge izin"><i class="fas fa-envelope"></i> {{ $dayIzin }}</span>
                    @endif
                    @if($daySakit > 0)
                        <span class="mini-badge sakit"><i class="fas fa-heartbeat"></i> {{ $daySakit }}</span>
                    @endif
                    @if($dayAlpa > 0)
                        <span class="mini-badge alpa"><i class="fas fa-times"></i> {{ $dayAlpa }}</span>
                    @endif
                    @if($dayPulang > 0)
                        <span class="mini-badge pulang"><i class="fas fa-home"></i> {{ $dayPulang }}</span>
                    @endif
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
            </div>
            <div class="day-body">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 45px;">No</th>
                            <th style="width: 90px;">ID Santri</th>
                            <th>Nama Santri</th>
                            <th style="width: 140px;">Kelas</th>
                            <th style="width: 90px; text-align: center;">Status</th>
                            <th style="width: 80px; text-align: center;">Waktu</th>
                            <th style="width: 80px;">Metode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $index => $riwayat)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $riwayat->id_santri }}</strong></td>
                            <td>
                                <a href="{{ route('admin.riwayat-kegiatan.detail-santri', $riwayat->id_santri) }}"
                                   style="color: var(--primary-color); text-decoration: none; font-weight: 500;">
                                    {{ $riwayat->santri->nama_lengkap }}
                                </a>
                            </td>
                            <td>
                                @if($riwayat->santri->kelasSantri->first() && $riwayat->santri->kelasSantri->first()->kelas)
                                    {{ $riwayat->santri->kelasSantri->first()->kelas->nama_kelas }}
                                @else
                                    <span style="color: #9CA3AF;">-</span>
                                @endif
                            </td>
                            <td style="text-align: center;">{!! $riwayat->status_badge !!}</td>
                            <td style="text-align: center;">
                                {{ $riwayat->waktu_absen ? \Carbon\Carbon::parse($riwayat->waktu_absen)->format('H:i') : '-' }}
                            </td>
                            <td>
                                @if($riwayat->metode_absen == 'RFID')
                                    <span style="background: #DBEAFE; color: #1E40AF; padding: 3px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: 600;">
                                        <i class="fas fa-id-card"></i> RFID
                                    </span>
                                @else
                                    <span style="background: #E5E7EB; color: #374151; padding: 3px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: 600;">
                                        <i class="fas fa-hand-pointer"></i> Manual
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <div class="pagination">
        {!! $riwayats->appends(request()->query())->links('pagination::simple-bootstrap-4') !!}
    </div>

@else
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h3>Tidak Ada Riwayat</h3>
        <p>Tidak ada data absensi untuk periode <strong>{{ $periodeLabel }}</strong>.</p>
    </div>
@endif

<script>
function toggleDay(header) {
    var body = header.nextElementSibling;
    var icon = header.querySelector('.toggle-icon');
    if (body.style.display === 'none') {
        body.style.display = 'block';
        icon.classList.remove('collapsed');
    } else {
        body.style.display = 'none';
        icon.classList.add('collapsed');
    }
}
</script>
@endsection