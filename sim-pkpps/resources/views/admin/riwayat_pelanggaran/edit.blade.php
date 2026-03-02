@extends('layouts.app')

@section('title', 'Edit Riwayat Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Riwayat Pelanggaran</h2>
</div>

<div class="content-box">
    <form action="{{ route('admin.riwayat-pelanggaran.update', $riwayatPelanggaran) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>
                <i class="fas fa-id-card form-icon"></i>
                ID Riwayat
            </label>
            <input type="text" class="form-control" value="{{ $riwayatPelanggaran->id_riwayat }}" disabled>
        </div>

        <div class="form-group">
            <label for="id_santri">
                <i class="fas fa-user form-icon"></i>
                Santri <span style="color: var(--danger-color);">*</span>
            </label>
            <select name="id_santri" 
                    id="id_santri"
                    class="form-control @error('id_santri') is-invalid @enderror"
                    required>
                <option value="">-- Pilih Santri --</option>
                @foreach($santriList as $santri)
                    <option value="{{ $santri->id_santri }}" 
                            {{ old('id_santri', $riwayatPelanggaran->id_santri) == $santri->id_santri ? 'selected' : '' }}>
                        {{ $santri->nama_lengkap }} ({{ $santri->id_santri }})
                    </option>
                @endforeach
            </select>
            @error('id_santri')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="id_kategori">
                <i class="fas fa-exclamation-triangle form-icon"></i>
                Kategori Pelanggaran <span style="color: var(--danger-color);">*</span>
            </label>
            <select name="id_kategori" 
                    id="id_kategori"
                    class="form-control @error('id_kategori') is-invalid @enderror"
                    required>
                <option value="">-- Pilih Pelanggaran --</option>
                @foreach($kategoriList as $kategori)
                    <option value="{{ $kategori->id_kategori }}" 
                            {{ old('id_kategori', $riwayatPelanggaran->id_kategori) == $kategori->id_kategori ? 'selected' : '' }}>
                        [{{ $kategori->klasifikasi->nama_klasifikasi ?? '-' }}] {{ $kategori->nama_pelanggaran }} ({{ $kategori->poin }} poin)
                    </option>
                @endforeach
            </select>
            @error('id_kategori')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="tanggal">
                <i class="fas fa-calendar form-icon"></i>
                Tanggal <span style="color: var(--danger-color);">*</span>
            </label>
            <input type="date" 
                   name="tanggal" 
                   id="tanggal"
                   class="form-control @error('tanggal') is-invalid @enderror"
                   value="{{ old('tanggal', $riwayatPelanggaran->tanggal->format('Y-m-d')) }}"
                   required>
            @error('tanggal')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="keterangan">
                <i class="fas fa-comment form-icon"></i>
                Keterangan (Opsional)
            </label>
            <textarea name="keterangan" 
                      id="keterangan"
                      class="form-control @error('keterangan') is-invalid @enderror"
                      rows="4">{{ old('keterangan', $riwayatPelanggaran->keterangan) }}</textarea>
            @error('keterangan')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        @if($riwayatPelanggaran->is_kafaroh_selesai)
            <div style="background: #d4edda; padding: 15px; border-radius: 8px; margin-bottom: 14px; border-left: 4px solid var(--success-color);">
                <i class="fas fa-info-circle"></i> 
                <strong>Info:</strong> Kafaroh sudah diselesaikan pada {{ $riwayatPelanggaran->tanggal_kafaroh_selesai->format('d M Y H:i') }}
            </div>
        @endif

        @if($riwayatPelanggaran->is_published_to_parent)
            <div style="background: #d1ecf1; padding: 15px; border-radius: 8px; margin-bottom: 14px; border-left: 4px solid var(--info-color);">
                <i class="fas fa-info-circle"></i> 
                <strong>Info:</strong> Riwayat ini sudah dikirim ke wali santri pada {{ $riwayatPelanggaran->tanggal_published->format('d M Y H:i') }}
            </div>
        @endif

        <div class="btn-group" style="margin-top: 22px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('admin.riwayat-pelanggaran.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection