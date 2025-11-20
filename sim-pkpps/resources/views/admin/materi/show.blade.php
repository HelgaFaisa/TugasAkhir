@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-book-open"></i> Detail Materi</h2>
</div>

<div class="content-box">
    {{-- Header Section --}}
    <div class="detail-header">
        <div>
            <h3>{{ $materi->nama_kitab }}</h3>
            <p class="text-muted">ID: {{ $materi->id_materi }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.materi.edit', $materi) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.materi.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Detail Section --}}
    <div class="detail-section">
        <h4><i class="fas fa-info-circle"></i> Informasi Materi</h4>
        <table class="detail-table">
            <tr>
                <th><i class="fas fa-fingerprint"></i> ID Materi</th>
                <td><strong>{{ $materi->id_materi }}</strong></td>
            </tr>
            <tr>
                <th><i class="fas fa-layer-group"></i> Kategori</th>
                <td>{!! $materi->kategori_badge !!}</td>
            </tr>
            <tr>
                <th><i class="fas fa-users"></i> Kelas</th>
                <td>{!! $materi->kelas_badge !!}</td>
            </tr>
            <tr>
                <th><i class="fas fa-book"></i> Nama Kitab</th>
                <td><strong>{{ $materi->nama_kitab }}</strong></td>
            </tr>
            <tr>
                <th><i class="fas fa-file-alt"></i> Halaman Mulai</th>
                <td>{{ $materi->halaman_mulai }}</td>
            </tr>
            <tr>
                <th><i class="fas fa-file-alt"></i> Halaman Akhir</th>
                <td>{{ $materi->halaman_akhir }}</td>
            </tr>
            <tr>
                <th><i class="fas fa-calculator"></i> Total Halaman</th>
                <td>
                    <span class="badge badge-lg badge-primary">
                        <i class="fas fa-book-open"></i> {{ $materi->total_halaman }} Halaman
                    </span>
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-align-left"></i> Deskripsi</th>
                <td>{{ $materi->deskripsi ?? '-' }}</td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar-plus"></i> Dibuat Pada</th>
                <td>{{ $materi->created_at->format('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar-edit"></i> Terakhir Diupdate</th>
                <td>{{ $materi->updated_at->format('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    {{-- Statistik Section (untuk nanti di Langkah 2-3) --}}
    <div class="detail-section">
        <h4><i class="fas fa-chart-bar"></i> Statistik Capaian</h4>
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <p style="margin: 0;">
                Fitur statistik capaian santri akan tersedia setelah implementasi <strong>Langkah 2: Input Capaian per Santri</strong>.
            </p>
        </div>
        
        {{-- Uncomment ini setelah Langkah 2 selesai
        <div class="row-cards">
            <div class="card card-info">
                <h3>Total Santri</h3>
                <div class="card-value">0</div>
                <p class="text-muted">Santri yang ada capaian</p>
                <i class="fas fa-users card-icon"></i>
            </div>
            <div class="card card-success">
                <h3>Selesai 100%</h3>
                <div class="card-value">0</div>
                <p class="text-muted">Santri yang menyelesaikan</p>
                <i class="fas fa-check-circle card-icon"></i>
            </div>
            <div class="card card-warning">
                <h3>Rata-rata Progress</h3>
                <div class="card-value">0%</div>
                <p class="text-muted">Progress keseluruhan</p>
                <i class="fas fa-percentage card-icon"></i>
            </div>
        </div>
        --}}
    </div>

    {{-- Action Buttons --}}
    <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: flex-end;">
        <a href="{{ route('admin.materi.edit', $materi) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Materi
        </a>
        <form action="{{ route('admin.materi.destroy', $materi) }}" method="POST" style="display: inline-block;"
              onsubmit="return confirm('Yakin ingin menghapus materi {{ $materi->nama_kitab }}? Data capaian santri yang terkait juga akan terhapus!')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus Materi
            </button>
        </form>
    </div>
</div>
@endsection