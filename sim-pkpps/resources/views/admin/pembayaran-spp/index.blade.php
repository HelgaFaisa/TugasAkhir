{{-- resources/views/admin/pembayaran-spp/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pembayaran SPP')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-money-bill-wave"></i> Pembayaran SPP</h2>
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

<div class="content-box">
    <!-- Filter Section -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('admin.pembayaran-spp.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            
            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">
                    <i class="fas fa-search"></i> Cari Santri
                </label>
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Nama, NIS, atau ID Santri..." 
                       value="{{ request('search') }}">
            </div>
            
            <div style="min-width: 150px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">
                    <i class="fas fa-calendar-alt"></i> Bulan
                </label>
                <select name="bulan" class="form-control">
                    @php
                        $bulanIndo = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                            4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                    @endphp
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                            {{ $bulanIndo[$i] }}
                        </option>
                    @endfor
                </select>
            </div>

            <div style="min-width: 120px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">
                    <i class="fas fa-calendar"></i> Tahun
                </label>
                <select name="tahun" class="form-control">
                    @foreach($tahunList as $thn)
                        <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>
                            {{ $thn }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($tab === 'belum-bayar')
            <div style="min-width: 180px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">
                    <i class="fas fa-filter"></i> Filter Status
                </label>
                <select name="filter_status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="Belum Lunas" {{ request('filter_status') === 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="Telat" {{ request('filter_status') === 'Telat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="Belum Ada Tagihan" {{ request('filter_status') === 'Belum Ada Tagihan' ? 'selected' : '' }}>Belum Ada Tagihan</option>
                </select>
            </div>
            @endif

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="height: 38px;">
                    <i class="fas fa-search"></i> Cari
                </button>
                
                @if(request()->hasAny(['search', 'filter_status']) || $bulan != date('n') || $tahun != date('Y'))
                    <a href="{{ route('admin.pembayaran-spp.index', ['tab' => $tab]) }}" 
                       class="btn btn-secondary" 
                       style="height: 38px;">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 8px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total Santri</div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $totalSantri }}</div>
                </div>
                <i class="fas fa-users" style="font-size: 40px; opacity: 0.3;"></i>
            </div>
        </div>

        <div style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding: 20px; border-radius: 8px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Sudah Bayar</div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $totalLunas }}</div>
                    <div style="font-size: 12px; opacity: 0.8;">Rp {{ number_format($nominalLunas, 0, ',', '.') }}</div>
                </div>
                <i class="fas fa-check-circle" style="font-size: 40px; opacity: 0.3;"></i>
            </div>
        </div>

        <div style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); padding: 20px; border-radius: 8px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Belum Bayar</div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $totalBelumBayar }}</div>
                    <div style="font-size: 12px; opacity: 0.8;">Rp {{ number_format($nominalBelumLunas, 0, ',', '.') }}</div>
                </div>
                <i class="fas fa-exclamation-circle" style="font-size: 40px; opacity: 0.3;"></i>
            </div>
        </div>

        <div style="background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%); padding: 20px; border-radius: 8px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Terlambat</div>
                    <div style="font-size: 28px; font-weight: bold;">{{ $totalTelat }}</div>
                    <div style="font-size: 12px; opacity: 0.8;">Melewati batas waktu</div>
                </div>
                <i class="fas fa-clock" style="font-size: 40px; opacity: 0.3;"></i>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #e0e0e0;">
        <a href="{{ route('admin.pembayaran-spp.index', array_merge(request()->except('tab'), ['tab' => 'belum-bayar'])) }}" 
           class="btn btn-sm {{ $tab === 'belum-bayar' ? 'btn-danger' : 'btn-outline-danger' }}"
           style="border-radius: 8px 8px 0 0; border-bottom: none; padding: 12px 24px; font-weight: 600; {{ $tab === 'belum-bayar' ? 'border-bottom: 3px solid #dc3545;' : '' }}">
            <i class="fas fa-times-circle"></i> Belum Bayar
            @if($totalBelumBayar > 0)
                <span class="badge" style="background: white; color: #dc3545; margin-left: 8px;">{{ $totalBelumBayar }}</span>
            @endif
        </a>
        
        <a href="{{ route('admin.pembayaran-spp.index', array_merge(request()->except('tab'), ['tab' => 'sudah-bayar'])) }}" 
           class="btn btn-sm {{ $tab === 'sudah-bayar' ? 'btn-success' : 'btn-outline-success' }}"
           style="border-radius: 8px 8px 0 0; border-bottom: none; padding: 12px 24px; font-weight: 600; {{ $tab === 'sudah-bayar' ? 'border-bottom: 3px solid #28a745;' : '' }}">
            <i class="fas fa-check-circle"></i> Sudah Bayar
            @if($totalLunas > 0)
                <span class="badge" style="background: white; color: #28a745; margin-left: 8px;">{{ $totalLunas }}</span>
            @endif
        </a>
    </div>

    <!-- Action Buttons -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.pembayaran-spp.generate') }}" class="btn btn-warning btn-sm hover-shadow">
                <i class="fas fa-cogs"></i> Generate SPP
            </a>
            <a href="{{ route('admin.pembayaran-spp.create') }}" class="btn btn-success btn-sm hover-shadow">
                <i class="fas fa-plus-circle"></i> Tambah Data
            </a>
            <a href="{{ route('admin.pembayaran-spp.laporan') }}" class="btn btn-info btn-sm hover-shadow">
                <i class="fas fa-file-pdf"></i> Cetak Laporan
            </a>
        </div>

        <div style="font-size: 14px; color: #666;">
            <i class="fas fa-info-circle"></i> 
            Menampilkan data periode: <strong>{{ $bulanIndo[$bulan] ?? '' }} {{ $tahun }}</strong>
        </div>
    </div>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>ID Santri</th>
                    <th>Nama Santri</th>
                    @if($tab === 'sudah-bayar')
                        <th>Nominal</th>
                        <th>Tanggal Bayar</th>
                        <th>Status</th>
                    @else
                        <th>Nominal Tagihan</th>
                        <th>Batas Bayar</th>
                        <th>Status</th>
                    @endif
                    <th class="text-center" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($santriPaginated as $index => $item)
                    <tr style="{{ $item['is_telat'] ? 'background-color: #fff5f5;' : '' }}">
                        <td>{{ ($currentPage - 1) * 20 + $index + 1 }}</td>
                        <td><strong>{{ $item['id_santri'] }}</strong><br><small class="text-muted">{{ $item['nis'] }}</small></td>
                        <td>
                            <strong>{{ $item['nama_lengkap'] }}</strong>
                            @if($item['is_telat'])
                                <br><span class="badge badge-danger" style="font-size: 11px;">
                                    <i class="fas fa-exclamation-triangle"></i> TERLAMBAT
                                </span>
                            @endif
                        </td>
                        
                        @if($tab === 'sudah-bayar')
                            <td><strong style="color: #28a745;">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</strong></td>
                            <td>
                                @if($item['tanggal_bayar'])
                                    {{ \Carbon\Carbon::parse($item['tanggal_bayar'])->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Lunas
                                </span>
                            </td>
                        @else
                            <td>
                                @if($item['pembayaran'])
                                    <strong style="color: #dc3545;">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</strong>
                                @else
                                    <span class="text-muted">Belum ada tagihan</span>
                                @endif
                            </td>
                            <td>
                                @if($item['batas_bayar'])
                                    {{ \Carbon\Carbon::parse($item['batas_bayar'])->format('d/m/Y') }}
                                    @if($item['is_telat'])
                                        <br><small style="color: #dc3545; font-weight: 600;">
                                            <i class="fas fa-clock"></i> Telat {{ \Carbon\Carbon::parse($item['batas_bayar'])->diffInDays(now()) }} hari
                                        </small>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($item['is_telat'])
                                    <span class="badge badge-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Terlambat
                                    </span>
                                @elseif($item['status'] === 'Belum Lunas')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock"></i> Belum Lunas
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-info-circle"></i> Belum Ada Tagihan
                                    </span>
                                @endif
                            </td>
                        @endif
                        
                        <td class="text-center">
                            @if($item['pembayaran'])
                                <a href="{{ route('admin.pembayaran-spp.riwayat', $item['id_santri']) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="{{ $tab === 'sudah-bayar' ? 'Lihat Riwayat' : 'Lihat Tagihan' }}">
                                    <i class="fas fa-history"></i>
                                </a>
                                <a href="{{ route('admin.pembayaran-spp.edit', $item['pembayaran']->id) }}" 
                                   class="btn btn-sm btn-warning" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($tab === 'sudah-bayar')
                                    <a href="{{ route('admin.pembayaran-spp.cetak-bukti', $item['pembayaran']->id) }}" 
                                       class="btn btn-sm btn-success" 
                                       title="Cetak Bukti"
                                       target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('admin.pembayaran-spp.create', ['id_santri' => $item['id_santri'], 'bulan' => $bulan, 'tahun' => $tahun]) }}" 
                                   class="btn btn-sm btn-primary" 
                                   title="Buat Tagihan">
                                    <i class="fas fa-plus"></i> Buat
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 40px;">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; display: block; margin-bottom: 15px;"></i>
                            <p style="color: #999;">
                                @if($tab === 'sudah-bayar')
                                    Belum ada santri yang melunasi SPP untuk periode ini.
                                @else
                                    Tidak ada tagihan yang belum dibayar untuk periode ini.
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Manual Pagination -->
    @if($totalPages > 1)
        <div style="margin-top: 20px; display: flex; justify-content: center; align-items: center; gap: 10px;">
            @if($currentPage > 1)
                <a href="{{ route('admin.pembayaran-spp.index', array_merge(request()->all(), ['page' => $currentPage - 1])) }}" 
                   class="btn btn-sm btn-secondary">
                    <i class="fas fa-chevron-left"></i> Sebelumnya
                </a>
            @endif

            <span style="padding: 8px 15px; background: #f8f9fa; border-radius: 4px; font-weight: 600;">
                Halaman {{ $currentPage }} dari {{ $totalPages }}
            </span>

            @if($currentPage < $totalPages)
                <a href="{{ route('admin.pembayaran-spp.index', array_merge(request()->all(), ['page' => $currentPage + 1])) }}" 
                   class="btn btn-sm btn-secondary">
                    Selanjutnya <i class="fas fa-chevron-right"></i>
                </a>
            @endif
        </div>
    @endif
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 12px;
}

.badge-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.badge-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.badge-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
    animation: pulse 2s infinite;
}

.badge-secondary {
    background: #6c757d;
    color: white;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.data-table tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}
</style>
@endsection
