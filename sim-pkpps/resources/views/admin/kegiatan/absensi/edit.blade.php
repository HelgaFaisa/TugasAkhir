{{-- views/admin/kegiatan/absensi/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Absensi</h2>
</div>

<div class="content-box" style="max-width: 600px;">
    <div style="margin-bottom: 20px;">
        <h3 style="margin: 0 0 8px 0; color: var(--primary-color);">{{ $absensi->kegiatan->nama_kegiatan }}</h3>
        <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">
            <i class="fas fa-tag"></i> {{ $absensi->kegiatan->kategori->nama_kategori ?? '-' }} |
            <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($absensi->tanggal)->format('d M Y') }} |
            <i class="fas fa-clock"></i> {{ $absensi->waktu_absen ? date('H:i', strtotime($absensi->waktu_absen)) : '-' }}
        </p>
    </div>

    <div style="background: var(--primary-light); padding: 14px; border-radius: 8px; margin-bottom: 20px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 120px; padding: 4px 0; color: var(--text-light);"><i class="fas fa-id-badge"></i> ID Santri</td>
                <td style="padding: 4px 0;"><strong>{{ $absensi->santri->id_santri }}</strong></td>
            </tr>
            <tr>
                <td style="padding: 4px 0; color: var(--text-light);"><i class="fas fa-user"></i> Nama</td>
                <td style="padding: 4px 0;"><strong>{{ $absensi->santri->nama_lengkap }}</strong></td>
            </tr>
            <tr>
                <td style="padding: 4px 0; color: var(--text-light);"><i class="fas fa-chalkboard"></i> Kelas</td>
                <td style="padding: 4px 0;">{{ $absensi->santri->kelas_name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 0; color: var(--text-light);"><i class="fas fa-info-circle"></i> Status Saat Ini</td>
                <td style="padding: 4px 0;">
                    {!! $absensi->status_badge !!}
                </td>
            </tr>
        </table>
    </div>

    <form action="{{ route('admin.absensi-kegiatan.update', $absensi->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="font-weight: 600; margin-bottom: 10px; display: block;">
                <i class="fas fa-exchange-alt"></i> Ubah Status Absensi:
            </label>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                @php
                    $statusOptions = [
                        'Hadir' => ['badge' => 'badge-success', 'icon' => 'fa-check'],
                        'Terlambat' => ['badge' => '', 'icon' => 'fa-clock', 'style' => 'background: #FF9800; color: white;'],
                        'Izin' => ['badge' => 'badge-warning', 'icon' => 'fa-envelope'],
                        'Sakit' => ['badge' => 'badge-info', 'icon' => 'fa-medkit'],
                        'Alpa' => ['badge' => 'badge-danger', 'icon' => 'fa-times'],
                        'Pulang' => ['badge' => '', 'icon' => 'fa-home', 'style' => 'background: #FFF3E0; color: #E65100;'],
                    ];
                @endphp
                @foreach($statusOptions as $status => $opt)
                <label style="cursor: pointer; margin: 0;">
                    <input type="radio" name="status" value="{{ $status }}" {{ $absensi->status == $status ? 'checked' : '' }} style="display: none;" class="status-radio">
                    <span class="badge {{ $opt['badge'] ?? '' }} status-option" style="{{ $opt['style'] ?? '' }} padding: 8px 16px; font-size: 0.9rem; border: 2px solid transparent; border-radius: 6px; transition: all 0.2s;">
                        <i class="fas {{ $opt['icon'] }}"></i> {{ $status }}
                    </span>
                </label>
                @endforeach
            </div>
            @error('status')
                <div class="text-danger" style="margin-top: 6px; font-size: 0.85rem;">{{ $message }}</div>
            @enderror
        </div>

        <div class="btn-group" style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('admin.absensi-kegiatan.rekap', $absensi->kegiatan_id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Rekap
            </a>
        </div>
    </form>
</div>

<style>
    .status-radio:checked + .status-option {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 76, 110, 245), 0.25);
        transform: scale(1.05);
    }
    .status-option:hover {
        transform: scale(1.05);
        opacity: 0.9;
    }
</style>
@endsection
