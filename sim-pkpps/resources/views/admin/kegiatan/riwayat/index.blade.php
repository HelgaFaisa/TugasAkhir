@extends('layouts.app')

@section('content')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-header h2 { margin: 0; color: var(--primary-dark); font-size: 1.5rem; display: flex; align-items: center; gap: 10px; }
.btn-back { padding: 8px 16px; background: #6B7280; color: #fff; border: none; border-radius: 8px; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; }
.btn-back:hover { background: #4B5563; color: #fff; }
.filter-box { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; }
.filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
.form-group { margin: 0; }
.form-group label { display: block; font-size: 0.85rem; margin-bottom: 5px; color: var(--text-light); font-weight: 500; }
.form-control { width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; }
.btn-filter { background: var(--primary-color); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
.btn-filter:hover { background: #059669; transform: translateY(-1px); }
.btn-reset { background: #6B7280; color: #fff; border: none; padding: 9px 12px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; }
.btn-reset:hover { background: #4B5563; }
.data-table { width: 100%; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.data-table thead { background: linear-gradient(135deg, var(--primary-color), #059669); color: #fff; }
.data-table th { padding: 14px 16px; text-align: left; font-weight: 600; font-size: 0.85rem; }
.data-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }
.data-table tbody tr:hover { background: #f8fafc; }
.data-table tbody tr:last-child td { border-bottom: none; }
.badge { padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
.badge-success { background: #D1FAE5; color: #065F46; }
.btn-detail { padding: 6px 12px; background: var(--primary-color); color: #fff; border: none; border-radius: 6px; font-size: 0.8rem; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s; }
.btn-detail:hover { background: #059669; color: #fff; transform: translateY(-1px); }
.stat-mini { font-size: 0.78rem; color: var(--text-light); margin-top: 3px; }
.empty-state { text-align: center; padding: 60px 20px; color: var(--text-light); }
.empty-state i { font-size: 4rem; margin-bottom: 16px; opacity: 0.3; }
.empty-state h3 { margin: 0 0 8px; font-size: 1.2rem; }
.empty-state p { margin: 0; }
.pagination { display: flex; justify-content: center; align-items: center; gap: 6px; margin-top: 20px; }
.pagination a, .pagination span { padding: 6px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.82rem; text-decoration: none; color: var(--text-dark); transition: all 0.2s; }
.pagination a:hover { background: var(--primary-color); color: #fff; border-color: var(--primary-color); }
.pagination .active { background: var(--primary-color); color: #fff; border-color: var(--primary-color); font-weight: 600; }
.pagination .disabled { color: #cbd5e1; cursor: not-allowed; }
</style>

<div class="page-header">
    <h2><i class="fas fa-history"></i> Riwayat Kegiatan & Absensi</h2>
    <a href="{{ route('admin.laporan-kegiatan.index') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<!-- Filter -->
<div class="filter-box">
    <form method="GET">
        <div class="filter-grid">
            <div class="form-group">
                <label for="kategori_id">Kategori</label>
                <select name="kategori_id" id="kategori_id" class="form-control">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->kategori_id }}" {{ request('kategori_id') == $k->kategori_id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
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
                @if(request()->hasAny(['kategori_id', 'tanggal_dari', 'tanggal_sampai', 'bulan']))
                    <a href="{{ route('admin.riwayat-kegiatan.index') }}" class="btn-reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Tabel Kegiatan -->
@if($kegiatans->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Nama Kegiatan</th>
                <th style="width: 150px;">Kategori</th>
                <th style="width: 200px;">Waktu & Hari</th>
                <th style="width: 180px; text-align: center;">Statistik Absensi</th>
                <th style="width: 120px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kegiatans as $index => $kegiatan)
            <tr>
                <td>{{ $kegiatans->firstItem() + $index }}</td>
                <td>
                    <strong>{{ $kegiatan->nama_kegiatan }}</strong>
                    @if($kegiatan->kelasKegiatan->count() > 0)
                        <div class="stat-mini">
                            <i class="fas fa-users"></i> 
                            {{ $kegiatan->kelasKegiatan->pluck('nama_kelas')->implode(', ') }}
                        </div>
                    @else
                        <div class="stat-mini"><i class="fas fa-globe"></i> Umum</div>
                    @endif
                </td>
                <td>
                    <span class="badge badge-success">{{ $kegiatan->kategori->nama_kategori }}</span>
                </td>
                <td>
                    <i class="fas fa-clock"></i> {{ $kegiatan->waktu_mulai }} - {{ $kegiatan->waktu_selesai }}<br>
                    <span class="stat-mini">{{ $kegiatan->hari }}</span>
                </td>
                <td style="text-align: center;">
                    @if($kegiatan->total_absensi > 0)
                        <div style="font-size: 0.8rem;">
                            <span style="color: #10B981;"><i class="fas fa-check-circle"></i> {{ $kegiatan->hadir }}</span> |
                            <span style="color: #F59E0B;"><i class="fas fa-info-circle"></i> {{ $kegiatan->izin }}</span> |
                            <span style="color: #3B82F6;"><i class="fas fa-heartbeat"></i> {{ $kegiatan->sakit }}</span> |
                            <span style="color: #EF4444;"><i class="fas fa-times-circle"></i> {{ $kegiatan->alpa }}</span>
                        </div>
                        <div class="stat-mini">Total: {{ $kegiatan->total_absensi }} record</div>
                    @else
                        <span style="color: #9CA3AF; font-size: 0.8rem;">Belum ada data</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    <a href="{{ route('admin.riwayat-kegiatan.show', $kegiatan->id) }}" class="btn-detail">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination">
        {!! $kegiatans->links('pagination::simple-bootstrap-4') !!}
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h3>Tidak Ada Data</h3>
        <p>Belum ada kegiatan yang tercatat.</p>
    </div>
@endif

@endsection
