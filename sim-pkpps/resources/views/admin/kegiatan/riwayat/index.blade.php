{{-- resources/views/admin/kegiatan/riwayat/index.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
.day-group       { margin-bottom: 18px; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
.day-header      { background: linear-gradient(135deg, #f0fdf4 0%, #E8F7F2 100%); padding: 13px 18px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #E8F7F2; cursor: pointer; transition: background 0.2s; user-select: none; }
.day-header:hover{ background: linear-gradient(135deg, #E8F7F2 0%, #d1f2e8 100%); }
.day-title       { font-weight: 700; font-size: 1rem; color: var(--primary-dark); display: flex; align-items: center; gap: 8px; }
.day-title i     { color: var(--primary-color); }
.day-meta        { display: flex; gap: 10px; align-items: center; }
.day-count       { background: var(--primary-color); color: #fff; padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
.toggle-icon     { transition: transform 0.3s; font-size: 0.85rem; color: #94a3b8; }
.toggle-icon.collapsed { transform: rotate(-90deg); }
.day-body        { overflow: hidden; }
.day-body table  { width: 100%; border-collapse: collapse; }
.day-body thead  { background: #f8fafc; }
.day-body th     { padding: 9px 14px; text-align: left; font-weight: 600; font-size: 0.8rem; color: #64748b; border-bottom: 1px solid #e2e8f0; }
.day-body td     { padding: 10px 14px; font-size: 0.85rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.day-body tbody tr:last-child td { border-bottom: none; }
.day-body tbody tr:hover { background: #f8fafc; }

.stat-chips     { display: flex; gap: 5px; flex-wrap: wrap; }
.chip           { padding: 2px 8px; border-radius: 10px; font-size: 0.74rem; font-weight: 600; display: inline-flex; align-items: center; gap: 3px; }
.chip-hadir     { background: #D1FAE5; color: #065F46; }
.chip-terlambat { background: #FFF3E0; color: #E65100; }
.chip-izin      { background: #FEF3C7; color: #92400E; }
.chip-sakit     { background: #DBEAFE; color: #1E40AF; }
.chip-alpa      { background: #FEE2E2; color: #991B1B; }
.chip-pulang    { background: #F3E8FF; color: #6B21A8; }
.chip-none      { background: #F1F5F9; color: #94a3b8; }
.kelas-tag      { display: inline-block; background: #E8F7F2; color: var(--primary-dark); padding: 2px 7px; border-radius: 8px; font-size: 0.74rem; margin: 2px 2px 0 0; }
.umum-tag       { display: inline-block; background: #F1F5F9; color: #64748b; padding: 2px 7px; border-radius: 8px; font-size: 0.74rem; }
.btn-detail     { padding: 5px 12px; background: var(--primary-color); color: #fff; border: none; border-radius: 6px; font-size: 0.8rem; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s; }
.btn-detail:hover { background: #059669; color: #fff; transform: translateY(-1px); }

.filter-box  { background: #fff; padding: 14px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 14px; }
.filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 11px; }
.form-group  { margin: 0; }
.form-group label { display: block; font-size: 0.85rem; margin-bottom: 5px; color: var(--text-light); font-weight: 500; }
.form-control { width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; }
.btn-filter  { background: var(--primary-color); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
.btn-filter:hover { background: #059669; transform: translateY(-1px); }
.btn-reset   { background: #6B7280; color: #fff; border: none; padding: 9px 12px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
.btn-reset:hover { background: #4B5563; }

.period-tabs { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 12px; }
.period-tab  { padding: 5px 14px; border-radius: 20px; border: 1px solid #e2e8f0; background: #fff; font-size: 0.82rem; cursor: pointer; text-decoration: none; color: var(--text-dark); transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; }
.period-tab:hover, .period-tab.active { background: var(--primary-color); color: #fff; border-color: var(--primary-color); }

.periode-info { font-size: 0.82rem; color: var(--text-light); margin-bottom: 12px; padding: 8px 12px; background: #f0fdf4; border-radius: 8px; border-left: 3px solid var(--primary-color); }
.periode-info strong { color: var(--primary-color); }

.empty-state    { text-align: center; padding: 44px 14px; color: var(--text-light); background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.empty-state i  { font-size: 3.5rem; margin-bottom: 14px; opacity: 0.3; display: block; }
.empty-state h3 { margin: 0 0 6px; }

@media (max-width: 768px) {
    .day-header { flex-direction: column; align-items: flex-start; gap: 6px; }
    .day-body th:nth-child(3), .day-body td:nth-child(3),
    .day-body th:nth-child(4), .day-body td:nth-child(4) { display: none; }
}
</style>

@php
    // Default: minggu ini
    $defaultDari   = now()->startOfWeek(\Carbon\Carbon::MONDAY)->format('Y-m-d');
    $defaultSampai = now()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('Y-m-d');

    $activeDari    = request('tanggal_dari',   $defaultDari);
    $activeSampai  = request('tanggal_sampai', $defaultSampai);
    $activeKategori = request('kategori_id', '');
    $activeBulan   = request('bulan', '');

    // Label periode
    if ($activeBulan) {
        $periodeLabel = 'Bulan ' . \Carbon\Carbon::parse($activeBulan . '-01')->locale('id')->isoFormat('MMMM Y');
    } else {
        $periodeLabel = \Carbon\Carbon::parse($activeDari)->locale('id')->isoFormat('D MMM Y')
                      . ' – '
                      . \Carbon\Carbon::parse($activeSampai)->locale('id')->isoFormat('D MMM Y');
    }

    // Cek shortcut aktif
    $isMingguIni = !$activeBulan
        && $activeDari   == $defaultDari
        && $activeSampai == $defaultSampai;

    $isBulanIni  = !$activeBulan
        && $activeDari   == now()->startOfMonth()->format('Y-m-d')
        && $activeSampai == now()->endOfMonth()->format('Y-m-d');
@endphp

<div class="page-header">
    <h2><i class="fas fa-history"></i> Riwayat Kegiatan & Absensi</h2>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

{{-- Filter --}}
<div class="filter-box">

    {{-- Shortcut tabs --}}
    <div class="period-tabs">
        <a href="{{ route('admin.riwayat-kegiatan.index', ['tanggal_dari' => $defaultDari, 'tanggal_sampai' => $defaultSampai, 'kategori_id' => $activeKategori]) }}"
           class="period-tab {{ $isMingguIni ? 'active' : '' }}">
            <i class="fas fa-calendar-week"></i> Minggu Ini
        </a>
        <a href="{{ route('admin.riwayat-kegiatan.index', ['tanggal_dari' => now()->startOfMonth()->format('Y-m-d'), 'tanggal_sampai' => now()->endOfMonth()->format('Y-m-d'), 'kategori_id' => $activeKategori]) }}"
           class="period-tab {{ $isBulanIni ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i> Bulan Ini
        </a>
        <a href="{{ route('admin.riwayat-kegiatan.index') }}"
           class="period-tab {{ !$isMingguIni && !$isBulanIni ? 'active' : '' }}"
           style="{{ !$isMingguIni && !$isBulanIni ? '' : 'display:none' }}">
            <i class="fas fa-filter"></i> Custom
        </a>
    </div>

    <form method="GET">
        <div class="filter-grid">
            <div class="form-group">
                <label for="kategori_id">Kategori</label>
                <select name="kategori_id" id="kategori_id" class="form-control">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->kategori_id }}" {{ $activeKategori == $k->kategori_id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal_dari">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" id="tanggal_dari" class="form-control" value="{{ $activeDari }}">
            </div>
            <div class="form-group">
                <label for="tanggal_sampai">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" id="tanggal_sampai" class="form-control" value="{{ $activeSampai }}">
            </div>
            <div class="form-group">
                <label for="bulan">Atau Pilih Bulan</label>
                <input type="month" name="bulan" id="bulan" class="form-control" value="{{ $activeBulan }}">
            </div>
            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" class="btn-filter" style="flex: 1;">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.riwayat-kegiatan.index') }}" class="btn-reset" title="Reset ke minggu ini">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Info periode --}}
<p class="periode-info">
    <i class="fas fa-calendar-check"></i> Menampilkan kegiatan periode: <strong>{{ $periodeLabel }}</strong>
</p>

@if($kegiatans->count() > 0)

    @php
        $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
        $grouped   = $kegiatans->getCollection()->groupBy('hari');
    @endphp

    @foreach($hariOrder as $hari)
        @if($grouped->has($hari))
            @php $items = $grouped[$hari]; @endphp
            <div class="day-group">
                <div class="day-header" onclick="toggleDay(this)">
                    <div class="day-title">
                        <i class="fas fa-calendar-day"></i> {{ $hari }}
                    </div>
                    <div class="day-meta">
                        <span class="day-count">{{ $items->count() }} kegiatan</span>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                </div>

                <div class="day-body">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Nama Kegiatan</th>
                                <th style="width: 110px;">Waktu</th>
                                <th>Kelas</th>
                                <th style="width: 130px;">Kategori</th>
                                <th style="width: 250px; text-align: center;">Statistik Absensi</th>
                                <th style="width: 90px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $i => $kegiatan)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ $kegiatan->nama_kegiatan }}</strong></td>
                                <td style="white-space: nowrap; color: #64748b; font-size: 0.82rem;">
                                    <i class="fas fa-clock" style="color: var(--primary-color);"></i>
                                    {{ date('H:i', strtotime($kegiatan->waktu_mulai)) }}–{{ date('H:i', strtotime($kegiatan->waktu_selesai)) }}
                                </td>
                                <td>
                                    @if($kegiatan->kelasKegiatan->isEmpty())
                                        <span class="umum-tag"><i class="fas fa-globe"></i> Umum</span>
                                    @else
                                        @foreach($kegiatan->kelasKegiatan->take(3) as $kls)
                                            <span class="kelas-tag">{{ $kls->nama_kelas }}</span>
                                        @endforeach
                                        @if($kegiatan->kelasKegiatan->count() > 3)
                                            <span class="umum-tag">+{{ $kegiatan->kelasKegiatan->count() - 3 }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-success">{{ $kegiatan->kategori->nama_kategori }}</span>
                                </td>
                                <td style="text-align: center;">
                                    @if($kegiatan->total_absensi > 0)
                                        <div class="stat-chips" style="justify-content: center;">
                                            @if($kegiatan->hadir > 0)
                                                <span class="chip chip-hadir"><i class="fas fa-check"></i> {{ $kegiatan->hadir }}</span>
                                            @endif
                                            @if(($kegiatan->terlambat ?? 0) > 0)
                                                <span class="chip chip-terlambat"><i class="fas fa-clock"></i> {{ $kegiatan->terlambat }}</span>
                                            @endif
                                            @if($kegiatan->izin > 0)
                                                <span class="chip chip-izin"><i class="fas fa-envelope"></i> {{ $kegiatan->izin }}</span>
                                            @endif
                                            @if($kegiatan->sakit > 0)
                                                <span class="chip chip-sakit"><i class="fas fa-heartbeat"></i> {{ $kegiatan->sakit }}</span>
                                            @endif
                                            @if($kegiatan->alpa > 0)
                                                <span class="chip chip-alpa"><i class="fas fa-times"></i> {{ $kegiatan->alpa }}</span>
                                            @endif
                                            @if(($kegiatan->pulang ?? 0) > 0)
                                                <span class="chip chip-pulang"><i class="fas fa-home"></i> {{ $kegiatan->pulang }}</span>
                                            @endif
                                        </div>
                                        <div style="font-size: 0.74rem; color: #94a3b8; margin-top: 3px;">
                                            Total: {{ $kegiatan->total_absensi }}
                                        </div>
                                    @else
                                        <span class="chip chip-none">Belum ada data</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    {{-- Teruskan parameter periode ke halaman detail --}}
                                    <a href="{{ route('admin.riwayat-kegiatan.show', $kegiatan->id) }}?tanggal_dari={{ $activeDari }}&tanggal_sampai={{ $activeSampai }}"
                                       class="btn-detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach

    <div class="pagination" style="margin-top: 16px;">
        {!! $kegiatans->appends(request()->query())->links('pagination::simple-bootstrap-4') !!}
    </div>

@else
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h3>Tidak Ada Data</h3>
        <p>Tidak ada kegiatan pada periode <strong>{{ $periodeLabel }}</strong>.</p>
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