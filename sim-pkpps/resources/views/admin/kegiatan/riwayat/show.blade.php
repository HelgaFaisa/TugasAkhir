@extends('layouts.app')

@section('content')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-header h2 { margin: 0; color: var(--primary-dark); font-size: 1.5rem; display: flex; align-items: center; gap: 10px; }
.btn-back { padding: 8px 16px; background: #6B7280; color: #fff; border: none; border-radius: 8px; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; }
.btn-back:hover { background: #4B5563; color: #fff; }
.info-box { background: linear-gradient(135deg, var(--primary-color), #059669); color: #fff; padding: 24px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(16,185,129,0.2); }
.info-box h3 { margin: 0 0 8px; font-size: 1.4rem; display: flex; align-items: center; gap: 10px; }
.info-box .meta { opacity: 0.9; font-size: 0.9rem; margin-top: 8px; }
.stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px; }
.stat-card { background: #fff; padding: 16px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; align-items: center; gap: 14px; }
.stat-card .icon { font-size: 2rem; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 10px; }
.stat-card.success .icon { background: #D1FAE5; color: #065F46; }
.stat-card.warning .icon { background: #FEF3C7; color: #92400E; }
.stat-card.info .icon { background: #DBEAFE; color: #1E40AF; }
.stat-card.danger .icon { background: #FEE2E2; color: #991B1B; }
.stat-card .content { flex: 1; }
.stat-card .label { font-size: 0.82rem; color: var(--text-light); margin-bottom: 4px; }
.stat-card .value { font-size: 1.6rem; font-weight: 700; color: var(--primary-dark); }
.filter-box { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; }
.filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
.form-group { margin: 0; }
.form-group label { display: block; font-size: 0.85rem; margin-bottom: 5px; color: var(--text-light); font-weight: 500; }
.form-control { width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; }
.btn-filter { background: var(--primary-color); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
.btn-filter:hover { background: #059669; }
.btn-reset { background: #6B7280; color: #fff; border: none; padding: 9px 12px; border-radius: 8px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; }
.data-table { width: 100%; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.data-table thead { background: linear-gradient(135deg, var(--primary-color), #059669); color: #fff; }
.data-table th { padding: 12px 14px; text-align: left; font-weight: 600; font-size: 0.85rem; }
.data-table td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }
.data-table tbody tr:hover { background: #f8fafc; }
.data-table tbody tr:last-child td { border-bottom: none; }
.empty-state { text-align: center; padding: 50px 20px; color: var(--text-light); background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.empty-state i { font-size: 3.5rem; margin-bottom: 14px; opacity: 0.3; }
.pagination { display: flex; justify-content: center; align-items: center; gap: 6px; margin-top: 20px; }
.pagination a, .pagination span { padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.82rem; text-decoration: none; color: var(--text-dark); transition: all 0.2s; }
.pagination a:hover { background: var(--primary-color); color: #fff; border-color: var(--primary-color); }
.pagination .active { background: var(--primary-color); color: #fff; border-color: var(--primary-color); font-weight: 600; }
.pagination .disabled { color: #cbd5e1; cursor: not-allowed; }
</style>

<div class="page-header">
    <h2><i class="fas fa-file-alt"></i> Detail Riwayat: {{ $kegiatan->nama_kegiatan }}</h2>
    <a href="{{ route('admin.riwayat-kegiatan.index') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="info-box">
    <h3><i class="fas fa-clipboard-check"></i> {{ $kegiatan->nama_kegiatan }}</h3>
    <div class="meta">
        <span><i class="fas fa-tag"></i> {{ $kegiatan->kategori->nama_kategori }}</span> |
        <span><i class="fas fa-clock"></i> {{ $kegiatan->waktu_mulai }} - {{ $kegiatan->waktu_selesai }}</span> |
        <span><i class="fas fa-calendar-day"></i> {{ $kegiatan->hari }}</span>
        @if($kegiatan->kelasKegiatan->count() > 0)
            | <span><i class="fas fa-users"></i> {{ $kegiatan->kelasKegiatan->pluck('nama_kelas')->implode(', ') }}</span>
        @else
            | <span><i class="fas fa-globe"></i> Umum</span>
        @endif
    </div>
</div>

<div class="stats-row">
    <div class="stat-card success">
        <div class="icon"><i class="fas fa-check-circle"></i></div>
        <div class="content">
            <div class="label">Hadir</div>
            <div class="value">{{ $stats['Hadir'] ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card warning">
        <div class="icon"><i class="fas fa-info-circle"></i></div>
        <div class="content">
            <div class="label">Izin</div>
            <div class="value">{{ $stats['Izin'] ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card info">
        <div class="icon"><i class="fas fa-heartbeat"></i></div>
        <div class="content">
            <div class="label">Sakit</div>
            <div class="value">{{ $stats['Sakit'] ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card danger">
        <div class="icon"><i class="fas fa-times-circle"></i></div>
        <div class="content">
            <div class="label">Alpa</div>
            <div class="value">{{ $stats['Alpa'] ?? 0 }}</div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="filter-box">
    <form method="GET">
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
                    <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                    <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="Alpa" {{ request('status') == 'Alpa' ? 'selected' : '' }}>Alpa</option>
                </select>
            </div>

            <div class="form-group">
                <label for="tanggal_dari">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" id="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
            </div>

            <div class="form-group">
                <label for="tanggal_sampai">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" id="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
            </div>

            <div class="form-group">
                <label for="bulan">Atau Pilih Bulan</label>
                <input type="month" name="bulan" id="bulan" class="form-control" value="{{ request('bulan') }}">
            </div>

            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" class="btn-filter" style="flex: 1;">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['id_santri', 'id_kelas', 'status', 'tanggal_dari', 'tanggal_sampai', 'bulan']))
                    <a href="{{ route('admin.riwayat-kegiatan.show', $kegiatan->id) }}" class="btn-reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Tabel Riwayat -->
@if($riwayats->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th style="width: 100px;">Tanggal</th>
                <th style="width: 100px;">ID Santri</th>
                <th>Nama Santri</th>
                <th style="width: 150px;">Kelas</th>
                <th style="width: 100px; text-align: center;">Status</th>
                <th style="width: 100px; text-align: center;">Waktu</th>
                <th style="width: 90px;">Metode</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayats as $index => $riwayat)
            <tr>
                <td>{{ $riwayats->firstItem() + $index }}</td>
                <td>{{ $riwayat->tanggal->format('d/m/Y') }}</td>
                <td><strong>{{ $riwayat->id_santri }}</strong></td>
                <td>
                    <a href="{{ route('admin.riwayat-kegiatan.detail-santri', $riwayat->id_santri) }}" 
                       style="color: var(--primary-color); text-decoration: none; font-weight: 500;">
                        {{ $riwayat->santri->nama_lengkap }}
                    </a>
                </td>
                <td>
                    @if($riwayat->santri->kelasSantri->first())
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

    <div class="pagination">
        {!! $riwayats->links('pagination::simple-bootstrap-4') !!}
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h3>Tidak Ada Riwayat</h3>
        <p>Belum ada data riwayat absensi untuk kegiatan ini.</p>
    </div>
@endif

@endsection


@section('content')
<div class="page-header">
    <h2><i class="fas fa-file-alt"></i> Detail Riwayat Absensi</h2>
</div>

<div class="content-box">
    <div class="detail-header">
        <h3>Riwayat Absensi #{{ $riwayat->absensi_id }}</h3>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.riwayat-kegiatan.edit', $riwayat->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="{{ route('admin.riwayat-kegiatan.destroy', $riwayat->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus riwayat ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
            <a href="{{ route('admin.riwayat-kegiatan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-user"></i> Informasi Santri</h4>
        <table class="detail-table">
            <tr>
                <th>ID Santri</th>
                <td><strong>{{ $riwayat->santri->id_santri }}</strong></td>
            </tr>
            <tr>
                <th>Nama Lengkap</th>
                <td>{{ $riwayat->santri->nama_lengkap }}</td>
            </tr>
            <tr>
                <th>Kelas</th>
                <td><span class="badge badge-secondary badge-lg">{{ $riwayat->santri->kelas }}</span></td>
            </tr>
            <tr>
                <th>Status Santri</th>
                <td><span class="badge badge-success badge-lg">{{ $riwayat->santri->status }}</span></td>
            </tr>
        </table>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-calendar-alt"></i> Informasi Kegiatan</h4>
        <table class="detail-table">
            <tr>
                <th>ID Kegiatan</th>
                <td><strong>{{ $riwayat->kegiatan->kegiatan_id }}</strong></td>
            </tr>
            <tr>
                <th>Nama Kegiatan</th>
                <td>{{ $riwayat->kegiatan->nama_kegiatan }}</td>
            </tr>
            <tr>
                <th>Kategori</th>
                <td><span class="badge badge-primary badge-lg">{{ $riwayat->kegiatan->kategori->nama_kategori }}</span></td>
            </tr>
            <tr>
                <th>Hari</th>
                <td><span class="badge badge-info badge-lg">{{ $riwayat->kegiatan->hari }}</span></td>
            </tr>
            <tr>
                <th>Waktu Pelaksanaan</th>
                <td>
                    <i class="fas fa-clock" style="color: var(--primary-color);"></i>
                    {{ date('H:i', strtotime($riwayat->kegiatan->waktu_mulai)) }} - 
                    {{ date('H:i', strtotime($riwayat->kegiatan->waktu_selesai)) }} WIB
                </td>
            </tr>
        </table>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-clipboard-check"></i> Detail Absensi</h4>
        <table class="detail-table">
            <tr>
                <th>ID Absensi</th>
                <td><strong>{{ $riwayat->absensi_id }}</strong></td>
            </tr>
            <tr>
                <th>Tanggal Absensi</th>
                <td>{{ $riwayat->tanggal->format('d F Y') }}</td>
            </tr>
            <tr>
                <th>Status Kehadiran</th>
                <td>{!! $riwayat->status_badge !!}</td>
            </tr>
            <tr>
                <th>Metode Absensi</th>
                <td>
                    @if($riwayat->metode_absen == 'RFID')
                        <span class="badge badge-primary badge-lg">
                            <i class="fas fa-id-card"></i> RFID
                        </span>
                    @else
                        <span class="badge badge-secondary badge-lg">
                            <i class="fas fa-hand-pointer"></i> Manual
                        </span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Waktu Absen</th>
                <td>
                    @if($riwayat->waktu_absen)
                        <i class="fas fa-clock" style="color: var(--primary-color);"></i>
                        {{ date('H:i:s', strtotime($riwayat->waktu_absen)) }} WIB
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th>Dicatat Pada</th>
                <td>{{ $riwayat->created_at->format('d F Y, H:i:s') }} WIB</td>
            </tr>
            <tr>
                <th>Terakhir Diubah</th>
                <td>{{ $riwayat->updated_at->format('d F Y, H:i:s') }} WIB</td>
            </tr>
        </table>
    </div>
</div>
@endsection