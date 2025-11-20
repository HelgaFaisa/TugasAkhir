@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Kegiatan</h2>
</div>

<div class="form-container">
    <form action="{{ route('admin.kegiatan.update', $kegiatan) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="kegiatan_id">
                <i class="fas fa-hashtag form-icon"></i>
                ID Kegiatan
            </label>
            <input type="text" class="form-control" value="{{ $kegiatan->kegiatan_id }}" disabled>
        </div>

        <div class="form-group">
            <label for="kategori_id">
                <i class="fas fa-list-alt form-icon"></i>
                Kategori Kegiatan <span style="color: red;">*</span>
            </label>
            <select name="kategori_id" id="kategori_id" class="form-control @error('kategori_id') is-invalid @enderror" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->kategori_id }}" 
                        {{ old('kategori_id', $kegiatan->kategori_id) == $kat->kategori_id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
            @error('kategori_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="nama_kegiatan">
                <i class="fas fa-calendar-check form-icon"></i>
                Nama Kegiatan <span style="color: red;">*</span>
            </label>
            <input type="text" 
                   name="nama_kegiatan" 
                   id="nama_kegiatan" 
                   class="form-control @error('nama_kegiatan') is-invalid @enderror" 
                   value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}" 
                   required>
            @error('nama_kegiatan')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="hari">
                <i class="fas fa-calendar-day form-icon"></i>
                Hari <span style="color: red;">*</span>
            </label>
            <select name="hari" id="hari" class="form-control @error('hari') is-invalid @enderror" required>
                <option value="">-- Pilih Hari --</option>
                @foreach($hariList as $h)
                    <option value="{{ $h }}" {{ old('hari', $kegiatan->hari) == $h ? 'selected' : '' }}>{{ $h }}</option>
                @endforeach
            </select>
            @error('hari')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label for="waktu_mulai">
                    <i class="fas fa-clock form-icon"></i>
                    Waktu Mulai <span style="color: red;">*</span>
                </label>
                <input type="time" 
                       name="waktu_mulai" 
                       id="waktu_mulai" 
                       class="form-control @error('waktu_mulai') is-invalid @enderror" 
                       value="{{ old('waktu_mulai', date('H:i', strtotime($kegiatan->waktu_mulai))) }}" 
                       required>
                @error('waktu_mulai')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="waktu_selesai">
                    <i class="fas fa-clock form-icon"></i>
                    Waktu Selesai <span style="color: red;">*</span>
                </label>
                <input type="time" 
                       name="waktu_selesai" 
                       id="waktu_selesai" 
                       class="form-control @error('waktu_selesai') is-invalid @enderror" 
                       value="{{ old('waktu_selesai', date('H:i', strtotime($kegiatan->waktu_selesai))) }}" 
                       required>
                @error('waktu_selesai')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="materi">
                <i class="fas fa-book form-icon"></i>
                Materi/Topik
            </label>
            <input type="text" 
                   name="materi" 
                   id="materi" 
                   class="form-control @error('materi') is-invalid @enderror" 
                   value="{{ old('materi', $kegiatan->materi) }}">
            @error('materi')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="keterangan">
                <i class="fas fa-align-left form-icon"></i>
                Keterangan
            </label>
            <textarea name="keterangan" 
                      id="keterangan" 
                      class="form-control @error('keterangan') is-invalid @enderror" 
                      rows="4">{{ old('keterangan', $kegiatan->keterangan) }}</textarea>
            @error('keterangan')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection