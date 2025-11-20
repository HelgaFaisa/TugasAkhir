@extends('layouts.app')

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