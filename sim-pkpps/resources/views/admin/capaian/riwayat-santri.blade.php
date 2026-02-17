@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-history"></i> Riwayat Capaian Santri</h2>
</div>

{{-- Santri Info Card --}}
<div class="content-box" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 20px;">
        <div class="icon-wrapper icon-wrapper-lg">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div style="flex: 1;">
            <h3 style="margin: 0 0 5px 0;">{{ $santri->nama_lengkap }}</h3>
            <p style="margin: 0; color: var(--text-light);">
                <strong>NIS:</strong> {{ $santri->nis }} | 
                <strong>Kelas:</strong> <span class="badge badge-secondary">{{ $santri->kelas }}</span>
            </p>
        </div>
        <div style="text-align: right; display: flex; gap: 10px; justify-content: flex-end;">
            <a href="{{ route('admin.capaian.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Data Capaian
            </a>
            <a href="{{ route('admin.santri.show', $santri) }}" class="btn btn-info">
                <i class="fas fa-user"></i> Profil Santri
            </a>
        </div>
    </div>
</div>

{{-- Statistik Cards --}}
<div class="row-cards">
    <div class="card card-info">
        <h3>Total Capaian</h3>
        <div class="card-value">{{ $totalCapaian }}</div>
        <p class="text-muted">Data capaian tercatat</p>
        <i class="fas fa-clipboard-list card-icon"></i>
    </div>
    <div class="card card-success">
        <h3>Rata-rata Progress</h3>
        <div class="card-value">{{ number_format($rataRataPersentase, 1) }}%</div>
        <p class="text-muted">Progress keseluruhan</p>
        <i class="fas fa-chart-line card-icon"></i>
    </div>
    <div class="card card-primary">
        <h3>Al-Qur'an</h3>
        <div class="card-value-small">{{ number_format($statistikKategori['Al-Qur\'an'] ?? 0, 1) }}%</div>
        <p class="text-muted">Progress kategori</p>
        <i class="fas fa-book-quran card-icon"></i>
    </div>
    <div class="card card-warning">
        <h3>Hadist</h3>
        <div class="card-value-small">{{ number_format($statistikKategori['Hadist'] ?? 0, 1) }}%</div>
        <p class="text-muted">Progress kategori</p>
        <i class="fas fa-scroll card-icon"></i>
    </div>
</div>

{{-- Filter Section --}}
<div class="content-box" style="margin-bottom: 20px;">
    <form method="GET" action="{{ route('admin.capaian.riwayat-santri', $santri->id_santri) }}" class="filter-form-inline">
        <select name="id_semester" class="form-control" style="width: 250px;">
            <option value="">Semua Semester</option>
            @foreach($semesters as $semester)
                <option value="{{ $semester->id_semester }}" {{ request('id_semester') == $semester->id_semester ? 'selected' : '' }}>
                    {{ $semester->nama_semester }}
                </option>
            @endforeach
        </select>

        <input type="text" name="search" class="form-control" placeholder="Cari nama materi..." 
               value="{{ request('search') }}" style="width: 300px;">

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filter
        </button>

        @if(request()->filled('id_semester') || request()->filled('search'))
            <a href="{{ route('admin.capaian.riwayat-santri', $santri->id_santri) }}" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        @endif

        <a href="{{ route('admin.capaian.create', ['id_santri' => $santri->id_santri]) }}" class="btn btn-success" style="margin-left: auto;">
            <i class="fas fa-plus"></i> Tambah Capaian
        </a>
    </form>
</div>

{{-- Capaian Table --}}
<div class="content-box">
    @if($capaians->count() > 0)
        {{-- Group by Kategori --}}
        @php
            $groupedCapaians = $capaians->groupBy(function($item) {
                return $item->materi->kategori;
            });
        @endphp

        @foreach(['Al-Qur\'an', 'Hadist', 'Materi Tambahan'] as $kategori)
            @if(isset($groupedCapaians[$kategori]) && $groupedCapaians[$kategori]->count() > 0)
                <div style="margin-bottom: 30px;">
                    <h4 style="color: var(--primary-dark); margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid var(--primary-light);">
                        <i class="fas fa-{{ $kategori == 'Al-Qur\'an' ? 'book-quran' : ($kategori == 'Hadist' ? 'scroll' : 'book') }}"></i>
                        Kategori: {{ $kategori }}
                    </h4>
                    
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 25%;">Materi</th>
                                <th style="width: 15%;">Semester</th>
                                <th style="width: 15%;">Halaman</th>
                                <th style="width: 15%;">Progress</th>
                                <th style="width: 15%;">Tanggal Input</th>
                                <th class="text-center" style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedCapaians[$kategori] as $index => $capaian)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $capaian->materi->nama_kitab }}</strong><br>
                                        <small class="text-muted">Total: {{ $capaian->materi->total_halaman }} hal</small>
                                    </td>
                                    <td>
                                        <small>{{ $capaian->semester->nama_semester }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">
                                            {{ $capaian->jumlah_halaman_selesai }} / {{ $capaian->materi->total_halaman }}
                                        </span>
                                    </td>
                                    <td>
                                        {!! $capaian->persentase_badge !!}
                                        <div class="progress-bar" style="margin-top: 5px; height: 8px;">
                                            <div class="progress-fill" style="width: {{ $capaian->persentase }}%; background: linear-gradient(90deg, var(--primary-color), var(--success-color));"></div>
                                        </div>
                                    </td>
                                    <td>{{ $capaian->tanggal_input->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.capaian.show', $capaian) }}" 
                                               class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.capaian.edit', $capaian) }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach

        {{-- Pagination --}}
        <div style="margin-top: 20px;">
            {{ $capaians->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <h3>Belum Ada Capaian</h3>
            <p>Santri ini belum memiliki data capaian.</p>
            <a href="{{ route('admin.capaian.create', ['id_santri' => $santri->id_santri]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Capaian Pertama
            </a>
        </div>
    @endif
</div>
@endsection