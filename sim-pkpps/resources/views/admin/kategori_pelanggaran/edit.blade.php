@extends('layouts.app')

@section('title', 'Edit Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Pelanggaran</h2>
</div>

<div class="content-box">
    <form action="{{ route('admin.kategori-pelanggaran.update', $kategori) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>
                <i class="fas fa-id-card form-icon"></i>
                ID Pelanggaran
            </label>
            <input type="text" class="form-control" value="{{ $kategori->id_kategori }}" disabled>
        </div>

        <div class="form-group">
            <label for="id_klasifikasi">
                <i class="fas fa-layer-group form-icon"></i>
                Klasifikasi <span style="color: var(--danger-color);">*</span>
            </label>
            <select name="id_klasifikasi" 
                    id="id_klasifikasi"
                    class="form-control @error('id_klasifikasi') is-invalid @enderror"
                    required>
                <option value="">-- Pilih Klasifikasi --</option>
                @foreach($klasifikasiList as $kl)
                    <option value="{{ $kl->id_klasifikasi }}" 
                            {{ old('id_klasifikasi', $kategori->id_klasifikasi) == $kl->id_klasifikasi ? 'selected' : '' }}>
                        {{ $kl->nama_klasifikasi }}
                    </option>
                @endforeach
            </select>
            @error('id_klasifikasi')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="nama_pelanggaran">
                <i class="fas fa-exclamation-triangle form-icon"></i>
                Nama Pelanggaran <span style="color: var(--danger-color);">*</span>
            </label>
            <input type="text" 
                   name="nama_pelanggaran" 
                   id="nama_pelanggaran"
                   class="form-control @error('nama_pelanggaran') is-invalid @enderror"
                   value="{{ old('nama_pelanggaran', $kategori->nama_pelanggaran) }}"
                   required>
            @error('nama_pelanggaran')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="poin">
                <i class="fas fa-star form-icon"></i>
                Poin Pelanggaran <span style="color: var(--danger-color);">*</span>
            </label>
            <input type="number" 
                   name="poin" 
                   id="poin" 
                   min="1" 
                   max="100"
                   class="form-control @error('poin') is-invalid @enderror"
                   value="{{ old('poin', $kategori->poin) }}"
                   required>
            @error('poin')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="kafaroh">
                <i class="fas fa-hands form-icon"></i>
                Kafaroh / Taqorrub
            </label>
            <textarea name="kafaroh" 
                      id="kafaroh"
                      class="form-control @error('kafaroh') is-invalid @enderror"
                      rows="6">{{ old('kafaroh', $kategori->kafaroh) }}</textarea>
            @error('kafaroh')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>
                <i class="fas fa-toggle-on form-icon"></i>
                Status
            </label>
            <div style="margin-top: 12px;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1" 
                           {{ old('is_active', $kategori->is_active) ? 'checked' : '' }}
                           style="margin-right: 8px;">
                    <span>Aktif</span>
                </label>
            </div>
        </div>

        <div class="btn-group" style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('admin.kategori-pelanggaran.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection