{{-- resources/views/admin/pembayaran-spp/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Pembayaran SPP')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Pembayaran SPP</h2>
</div>

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<div class="content-box">
    <form action="{{ route('admin.pembayaran-spp.store') }}" method="POST">
        @csrf

        <!-- ID Preview -->
        <div class="form-group">
            <label><i class="fas fa-hashtag form-icon"></i> ID Pembayaran (Otomatis)</label>
            <input type="text" class="form-control" value="{{ $nextId }}" disabled>
            <small class="form-text">ID akan digenerate otomatis saat data disimpan.</small>
        </div>

        <!-- Pilih Santri -->
        <div class="form-group">
            <label><i class="fas fa-user form-icon"></i> Pilih Santri <span style="color: red;">*</span></label>
            <select name="id_santri" class="form-control @error('id_santri') is-invalid @enderror" required>
                <option value="">-- Pilih Santri --</option>
                @foreach($santris as $santri)
                    <option value="{{ $santri->id_santri }}" {{ old('id_santri', request('id_santri')) == $santri->id_santri ? 'selected' : '' }}>
                        {{ $santri->id_santri }} - {{ $santri->nama_lengkap }} ({{ $santri->kelas }})
                    </option>
                @endforeach
            </select>
            @error('id_santri')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Bulan -->
            <div class="form-group">
                <label><i class="fas fa-calendar form-icon"></i> Bulan <span style="color: red;">*</span></label>
                <select name="bulan" class="form-control @error('bulan') is-invalid @enderror" required>
                    <option value="">-- Pilih Bulan --</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ old('bulan', request('bulan', date('n'))) == $i ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                    @endfor
                </select>
                @error('bulan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tahun -->
            <div class="form-group">
                <label><i class="fas fa-calendar-alt form-icon"></i> Tahun <span style="color: red;">*</span></label>
                <input type="number" 
                       name="tahun" 
                       class="form-control @error('tahun') is-invalid @enderror" 
                       value="{{ old('tahun', request('tahun', date('Y'))) }}" 
                       min="2020" 
                       max="2100"
                       required>
                @error('tahun')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Nominal -->
            <div class="form-group">
                <label><i class="fas fa-money-bill-wave form-icon"></i> Nominal (Rp) <span style="color: red;">*</span></label>
                <input type="number" 
                       name="nominal" 
                       class="form-control @error('nominal') is-invalid @enderror" 
                       value="{{ old('nominal', 250000) }}" 
                       min="0" 
                       step="1000"
                       required>
                @error('nominal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text">Masukkan nominal tanpa titik atau koma.</small>
            </div>

            <!-- Batas Bayar -->
            <div class="form-group">
                <label><i class="fas fa-clock form-icon"></i> Batas Bayar <span style="color: red;">*</span></label>
                <input type="date" 
                       name="batas_bayar" 
                       class="form-control @error('batas_bayar') is-invalid @enderror" 
                       value="{{ old('batas_bayar', date('Y-m-10')) }}"
                       required>
                @error('batas_bayar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Status -->
            <div class="form-group">
                <label><i class="fas fa-info-circle form-icon"></i> Status <span style="color: red;">*</span></label>
                <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="Belum Lunas" {{ old('status') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="Lunas" {{ old('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tanggal Bayar -->
            <div class="form-group">
                <label><i class="fas fa-calendar-check form-icon"></i> Tanggal Bayar</label>
                <input type="date" 
                       name="tanggal_bayar" 
                       class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                       value="{{ old('tanggal_bayar') }}">
                @error('tanggal_bayar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text">Kosongkan jika belum dibayar.</small>
            </div>
        </div>

        <!-- Keterangan -->
        <div class="form-group">
            <label><i class="fas fa-comment form-icon"></i> Keterangan</label>
            <textarea name="keterangan" 
                      class="form-control @error('keterangan') is-invalid @enderror" 
                      rows="3" 
                      placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
            @error('keterangan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Buttons -->
        <div style="display: flex; gap: 10px; margin-top: 25px;">
            <button type="submit" class="btn btn-success hover-shadow">
                <i class="fas fa-save"></i> Simpan Data
            </button>
            <a href="{{ route('admin.pembayaran-spp.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Auto-fill tanggal bayar jika status = Lunas
document.querySelector('select[name="status"]').addEventListener('change', function() {
    const tanggalBayar = document.querySelector('input[name="tanggal_bayar"]');
    if (this.value === 'Lunas' && !tanggalBayar.value) {
        const today = new Date().toISOString().split('T')[0];
        tanggalBayar.value = today;
    }
});
</script>
@endpush
@endsection