@extends('layouts.app')

@section('content')
<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.page-header h2 { margin: 0; font-size: 1.5rem; display: flex; align-items: center; gap: 10px; }
.btn-back { padding: 8px 16px; background: #6B7280; color: #fff; border: none; border-radius: 8px; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; }
.btn-back:hover { background: #4B5563; color: #fff; }
.filter-box { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; }
.filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
.form-group { margin: 0; }
.form-group label { display: block; font-size: 0.85rem; margin-bottom: 5px; color: #64748b; font-weight: 500; }
.form-control { width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; }
.btn-filter { background: var(--primary-color); color: #fff; border: none; padding: 9px 16px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
.btn-filter:hover { background: #059669; }
.btn-reset { background: #6B7280; color: #fff; border: none; padding: 9px 12px; border-radius: 8px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
.btn-reset:hover { background: #4B5563; color: #fff; }
.kelas-badge-container { position: relative; display: inline-block; }
.kelas-badge-more { cursor: pointer; position: relative; }
.kelas-tooltip { display: none; position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%); margin-bottom: 8px; background: #2c3e50; color: white; padding: 10px 12px; border-radius: 8px; font-size: 0.85rem; white-space: nowrap; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
.kelas-tooltip::after { content: ''; position: absolute; top: 100%; left: 50%; transform: translateX(-50%); border: 6px solid transparent; border-top-color: #2c3e50; }
.kelas-badge-more:hover .kelas-tooltip, .kelas-badge-more.active .kelas-tooltip { display: block; animation: fadeInUp 0.2s ease; }
@keyframes fadeInUp { from { opacity: 0; transform: translateX(-50%) translateY(5px); } to { opacity: 1; transform: translateX(-50%) translateY(0); } }
.kelas-tooltip .badge { margin: 2px 3px; font-size: 0.8rem; }
</style>

<div class="page-header">
    <h2><i class="fas fa-clipboard-check"></i> Absensi Kegiatan</h2>
    <a href="{{ route('admin.kegiatan.index') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<!-- Filter -->
<div class="filter-box">
    <form method="GET">
        <div class="filter-grid">
            <div class="form-group">
                <label for="search">Cari Kegiatan</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Nama atau ID kegiatan..." value="{{ request('search') }}">
            </div>

            <div class="form-group">
                <label for="hari">Hari</label>
                <select name="hari" id="hari" class="form-control">
                    <option value="">-- Semua Hari --</option>
                    @foreach($hariList as $h)
                        <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
            </div>

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

            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" class="btn-filter" style="flex: 1;">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'hari', 'kategori_id', 'id_kelas']))
                    <a href="{{ route('admin.absensi-kegiatan.index') }}" class="btn-reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<div class="content-box">

    @if($kegiatans->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">Hari</th>
                    <th style="width: 120px;">Waktu</th>
                    <th>Nama Kegiatan</th>
                    <th style="width: 150px;">Kategori</th>
                    <th style="width: 150px;">Kelas</th>
                    <th style="width: 250px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kegiatans as $index => $kegiatan)
                <tr>
                    <td>{{ $kegiatans->firstItem() + $index }}</td>
                    <td><span class="badge badge-primary">{{ $kegiatan->hari }}</span></td>
                    <td>{{ date('H:i', strtotime($kegiatan->waktu_mulai)) }} - {{ date('H:i', strtotime($kegiatan->waktu_selesai)) }}</td>
                    <td><strong>{{ $kegiatan->nama_kegiatan }}</strong></td>
                    <td>{{ $kegiatan->kategori->nama_kategori }}</td>
                    <td>
                        @if($kegiatan->kelasKegiatan->isEmpty())
                            <span class="badge badge-info">Umum</span>
                        @else
                            @php
                                $firstKelas = $kegiatan->kelasKegiatan->first();
                                $remainingCount = $kegiatan->kelasKegiatan->count() - 1;
                            @endphp
                            <span class="badge badge-secondary">{{ $firstKelas->nama_kelas }}</span>
                            @if($remainingCount > 0)
                                <span class="kelas-badge-container">
                                    <span class="badge badge-primary kelas-badge-more" onclick="this.classList.toggle('active')">
                                        +{{ $remainingCount }} lainnya
                                        <div class="kelas-tooltip">
                                            <strong style="display: block; margin-bottom: 5px; border-bottom: 1px solid rgba(255,255,255,0.3); padding-bottom: 5px;">Semua Kelas:</strong>
                                            @foreach($kegiatan->kelasKegiatan as $kelas)
                                                <span class="badge badge-light">{{ $kelas->nama_kelas }}</span>
                                            @endforeach
                                        </div>
                                    </span>
                                </span>
                            @endif
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.absensi-kegiatan.input', $kegiatan->kegiatan_id) }}" class="btn btn-sm btn-success" title="Input Absensi">
                            <i class="fas fa-clipboard-check"></i> Input
                        </a>
                        <a href="{{ route('admin.absensi-kegiatan.rekap', $kegiatan->kegiatan_id) }}" class="btn btn-sm btn-primary" title="Rekap Absensi">
                            <i class="fas fa-chart-bar"></i> Rekap
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $kegiatans->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>Belum Ada Kegiatan</h3>
            <p>Silakan tambahkan kegiatan terlebih dahulu.</p>
            <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Kegiatan
            </a>
        </div>
    @endif
</div>

<script>
// Close tooltip when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.kelas-badge-more')) {
        document.querySelectorAll('.kelas-badge-more.active').forEach(badge => {
            badge.classList.remove('active');
        });
    }
});

// Prevent table row click when clicking badge
document.querySelectorAll('.kelas-badge-more').forEach(badge => {
    badge.addEventListener('click', function(event) {
        event.stopPropagation();
    });
});
</script>
@endsection