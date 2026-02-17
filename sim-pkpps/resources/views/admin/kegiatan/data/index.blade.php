@extends('layouts.app')

@section('content')
<style>
/* Tooltip for class badges */
.kelas-badge-container {
    position: relative;
    display: inline-block;
}

.kelas-badge-more {
    cursor: pointer;
    position: relative;
}

.kelas-tooltip {
    display: none;
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-bottom: 8px;
    background: #2c3e50;
    color: white;
    padding: 10px 12px;
    border-radius: 8px;
    font-size: 0.85rem;
    white-space: nowrap;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.kelas-tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 6px solid transparent;
    border-top-color: #2c3e50;
}

.kelas-badge-more:hover .kelas-tooltip,
.kelas-badge-more.active .kelas-tooltip {
    display: block;
    animation: fadeInUp 0.2s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(5px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

.kelas-tooltip .badge {
    margin: 2px 3px;
    font-size: 0.8rem;
}
</style>

<div class="page-header">
    <h2><i class="fas fa-calendar-alt"></i> Jadwal Kegiatan Santri</h2>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="content-box">
    {{-- Filter dalam 1 Baris --}}
    <form method="GET" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            {{-- Filter Hari --}}
            <select name="hari" class="form-control" style="max-width: 150px;">
                <option value="">Semua Hari</option>
                @foreach($hariList as $h)
                    <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                @endforeach
            </select>

            {{-- Filter Kategori --}}
            <select name="kategori_id" class="form-control" style="max-width: 180px;">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->kategori_id }}" {{ request('kategori_id') == $kat->kategori_id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Kelas --}}
            <select name="kelas_id" class="form-control" style="max-width: 180px;">
                <option value="">Semua Kelas</option>
                <option value="umum" {{ request('kelas_id') === 'umum' ? 'selected' : '' }}>Kegiatan Umum</option>
                @foreach($kelasList->groupBy('kelompok.nama_kelompok') as $kelompokNama => $kelasGroup)
                    <optgroup label="{{ $kelompokNama }}">
                        @foreach($kelasGroup as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>

            {{-- Search --}}
            <input type="text" name="search" class="form-control" placeholder="Cari kegiatan..." 
                   value="{{ request('search') }}" style="min-width: 200px; flex: 1;">

            {{-- Buttons --}}
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>

            @if(request()->hasAny(['hari', 'kategori_id', 'kelas_id', 'search']))
                <a href="{{ route('admin.kegiatan.jadwal') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif

            <div style="margin-left: auto; display: flex; gap: 10px;">
                <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-info">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('admin.kategori-kegiatan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list-alt"></i> Kategori
                </a>
                <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah
                </a>
            </div>
        </div>
    </form>

    @if($kegiatans->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">ID</th>
                    <th style="width: 100px;">Hari</th>
                    <th style="width: 120px;">Waktu</th>
                    <th>Nama Kegiatan</th>
                    <th style="width: 150px;">Kategori</th>
                    <th style="width: 150px;">Kelas</th>
                    <th>Materi</th>
                    <th style="width: 180px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kegiatans as $index => $kegiatan)
                <tr>
                    <td>{{ $kegiatans->firstItem() + $index }}</td>
                    <td><strong>{{ $kegiatan->kegiatan_id }}</strong></td>
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
                    <td>{{ Str::limit($kegiatan->materi, 40) ?? '-' }}</td>
                    <td class="text-center">
                        <div style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                            <a href="{{ route('admin.kegiatan.show', $kegiatan) }}" class="btn btn-sm btn-primary" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.kegiatan.edit', $kegiatan) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.kegiatan.destroy', $kegiatan) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
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
            <p>Silakan tambahkan jadwal kegiatan santri.</p>
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