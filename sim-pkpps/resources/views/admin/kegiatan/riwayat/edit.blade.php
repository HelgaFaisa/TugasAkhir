{{-- resources/views/admin/kegiatan/riwayat/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Riwayat Absensi</h2>
</div>

<div class="form-container">
    <div class="info-box" style="margin-bottom: 25px;">
        <p><i class="fas fa-info-circle"></i> Hanya status kehadiran dan waktu absen yang dapat diubah.</p>
    </div>

    <form action="{{ route('admin.riwayat-kegiatan.update', $riwayat->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label><i class="fas fa-info form-icon"></i> Informasi Dasar</label>
            <table class="detail-table">
                <tr>
                    <th>ID Absensi</th>
                    <td><strong>{{ $riwayat->absensi_id }}</strong></td>
                </tr>
                <tr>
                    <th>Santri</th>
                    <td>{{ $riwayat->santri->nama_lengkap }} ({{ $riwayat->santri->id_santri }})</td>
                </tr>
                <tr>
                    <th>Kegiatan</th>
                    <td>{{ $riwayat->kegiatan->nama_kegiatan }}</td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ $riwayat->tanggal->format('d F Y') }}</td>
                </tr>
            </table>
        </div>

        <div class="form-group">
            <label for="status">
                <i class="fas fa-clipboard-check form-icon"></i>
                Status Kehadiran <span style="color: red;">*</span>
            </label>
            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="Hadir" {{ old('status', $riwayat->status) == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="Izin" {{ old('status', $riwayat->status) == 'Izin' ? 'selected' : '' }}>Izin</option>
                <option value="Sakit" {{ old('status', $riwayat->status) == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="Alpa" {{ old('status', $riwayat->status) == 'Alpa' ? 'selected' : '' }}>Alpa</option>
            </select>
            @error('status')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="waktu_absen">
                <i class="fas fa-clock form-icon"></i>
                Waktu Absen
            </label>
            <input type="time" 
                   name="waktu_absen" 
                   id="waktu_absen" 
                   class="form-control @error('waktu_absen') is-invalid @enderror" 
                   value="{{ old('waktu_absen', $riwayat->waktu_absen ? date('H:i', strtotime($riwayat->waktu_absen)) : '') }}">
            @error('waktu_absen')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
            <small class="form-text">Kosongkan jika tidak ingin mengubah waktu absen</small>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('admin.riwayat-kegiatan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection