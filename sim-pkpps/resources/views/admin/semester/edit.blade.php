@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Semester</h2>
</div>

<div class="form-container">
    <form action="{{ route('admin.semester.update', $semester) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <strong>ID Semester:</strong> {{ $semester->id_semester }}
        </div>

        <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            {{-- Tahun Ajaran --}}
            <div class="form-group">
                <label><i class="fas fa-graduation-cap form-icon"></i> Tahun Ajaran <span style="color: red;">*</span></label>
                <input type="text" name="tahun_ajaran" class="form-control @error('tahun_ajaran') is-invalid @enderror" 
                       value="{{ old('tahun_ajaran', $semester->tahun_ajaran) }}" placeholder="Contoh: 2024/2025" required>
                <small class="form-text">Format: YYYY/YYYY</small>
                @error('tahun_ajaran')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Periode --}}
            <div class="form-group">
                <label><i class="fas fa-calendar form-icon"></i> Periode <span style="color: red;">*</span></label>
                <select name="periode" class="form-control @error('periode') is-invalid @enderror" required>
                    <option value="">-- Pilih Periode --</option>
                    <option value="1" {{ old('periode', $semester->periode) == 1 ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                    <option value="2" {{ old('periode', $semester->periode) == 2 ? 'selected' : '' }}>Semester 2 (Genap)</option>
                </select>
                @error('periode')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            {{-- Tanggal Mulai --}}
            <div class="form-group">
                <label><i class="fas fa-calendar-check form-icon"></i> Tanggal Mulai <span style="color: red;">*</span></label>
                <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                       value="{{ old('tanggal_mulai', $semester->tanggal_mulai->format('Y-m-d')) }}" required>
                @error('tanggal_mulai')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Tanggal Akhir --}}
            <div class="form-group">
                <label><i class="fas fa-calendar-times form-icon"></i> Tanggal Akhir <span style="color: red;">*</span></label>
                <input type="date" name="tanggal_akhir" class="form-control @error('tanggal_akhir') is-invalid @enderror" 
                       value="{{ old('tanggal_akhir', $semester->tanggal_akhir->format('Y-m-d')) }}" required>
                @error('tanggal_akhir')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Status Aktif --}}
        <div class="form-group">
            <label style="display: flex; align-items: center; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" 
                       {{ old('is_active', $semester->is_active) ? 'checked' : '' }} 
                       style="margin-right: 10px; width: 20px; height: 20px;">
                <span><i class="fas fa-toggle-on form-icon"></i> Jadikan Semester Aktif</span>
            </label>
            <small class="form-text">Hanya 1 semester yang bisa aktif. Jika dicentang, semester lain akan otomatis non-aktif.</small>
        </div>

        {{-- Action Buttons --}}
        <div class="btn-group">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Update Semester
            </button>
            <a href="{{ route('admin.semester.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection