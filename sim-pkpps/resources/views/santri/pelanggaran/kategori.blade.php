{{-- resources/views/santri/pelanggaran/kategori.blade.php --}}
@extends('layouts.app')

@section('title', 'Daftar Kategori Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-list-ul"></i> Daftar Kategori Pelanggaran & Poin</h2>
</div>

<div class="content-box">
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <p class="text-muted">
            <i class="fas fa-info-circle"></i> 
            Berikut adalah daftar kategori pelanggaran beserta poin yang berlaku di pondok.
        </p>
        <a href="{{ route('santri.pelanggaran.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
        </a>
    </div>

    @if($kategoriList->count() > 0)
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">No</th>
                        <th style="width: 15%;">Kode</th>
                        <th style="width: 57%;">Jenis Pelanggaran</th>
                        <th style="width: 20%; text-align: center;">Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kategoriList as $index => $kategori)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $kategori->id_kategori }}</strong></td>
                        <td>{{ $kategori->nama_pelanggaran }}</td>
                        <td class="text-center">
                            @if($kategori->poin <= 5)
                                <span class="badge badge-warning badge-lg">
                                    <i class="fas fa-star"></i> {{ $kategori->poin }} Poin
                                </span>
                            @elseif($kategori->poin <= 15)
                                <span class="badge badge-danger badge-lg">
                                    <i class="fas fa-star"></i> {{ $kategori->poin }} Poin
                                </span>
                            @else
                                <span class="badge badge-danger badge-lg" style="background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);">
                                    <i class="fas fa-star"></i> {{ $kategori->poin }} Poin
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-list"></i>
            <h3>Belum Ada Kategori</h3>
            <p>Daftar kategori pelanggaran belum tersedia.</p>
        </div>
    @endif
</div>
@endsection