@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Transaksi Uang Saku</h2>
</div>

<div class="form-container">
    <form action="{{ route('admin.uang-saku.store') }}" method="POST" id="transaksiForm">
        @csrf

        <div class="form-group">
            <label for="id_santri">
                <i class="fas fa-user form-icon"></i>
                Pilih Santri <span style="color: red;">*</span>
            </label>
            <select name="id_santri" id="id_santri" class="form-control @error('id_santri') is-invalid @enderror" required>
                <option value="">-- Pilih Santri --</option>
                @foreach($santriList as $santri)
                    <option value="{{ $santri->id_santri }}" 
                        {{ (old('id_santri', request('id_santri')) == $santri->id_santri) ? 'selected' : '' }}>
                        {{ $santri->id_santri }} - {{ $santri->nama_lengkap }}
                    </option>
                @endforeach
            </select>
            @error('id_santri')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="jenis_transaksi">
                <i class="fas fa-exchange-alt form-icon"></i>
                Jenis Transaksi <span style="color: red;">*</span>
            </label>
            <select name="jenis_transaksi" id="jenis_transaksi" class="form-control @error('jenis_transaksi') is-invalid @enderror" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="pemasukan" {{ old('jenis_transaksi') == 'pemasukan' ? 'selected' : '' }}>
                    Pemasukan (Terima Uang Saku)
                </option>
                <option value="pengeluaran" {{ old('jenis_transaksi') == 'pengeluaran' ? 'selected' : '' }}>
                    Pengeluaran (Gunakan Uang Saku)
                </option>
            </select>
            @error('jenis_transaksi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="nominal">
                <i class="fas fa-money-bill-wave form-icon"></i>
                Nominal (Rp) <span style="color: red;">*</span>
            </label>
            <input type="number" name="nominal" id="nominal" class="form-control @error('nominal') is-invalid @enderror" 
                   value="{{ old('nominal') }}" placeholder="Contoh: 50000" min="1" step="1" required>
            @error('nominal')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text">Masukkan nominal tanpa titik atau koma</small>
        </div>

        <div class="form-group">
            <label for="tanggal_transaksi">
                <i class="fas fa-calendar form-icon"></i>
                Tanggal Transaksi <span style="color: red;">*</span>
            </label>
            <input type="date" name="tanggal_transaksi" id="tanggal_transaksi" 
                   class="form-control @error('tanggal_transaksi') is-invalid @enderror" 
                   value="{{ old('tanggal_transaksi', date('Y-m-d')) }}" required>
            @error('tanggal_transaksi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="keterangan">
                <i class="fas fa-sticky-note form-icon"></i>
                Keterangan
            </label>
            <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                      rows="4" placeholder="Contoh: Uang saku bulan Januari 2025">{{ old('keterangan') }}</textarea>
            @error('keterangan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text">Opsional - Jelaskan detail transaksi</small>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-success hover-lift">
                <i class="fas fa-save"></i> Simpan Transaksi
            </button>
            <a href="{{ route('admin.uang-saku.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
    // Format nominal input (tambah separator ribuan saat blur)
    document.getElementById('nominal').addEventListener('blur', function(e) {
        if (this.value) {
            const value = parseInt(this.value.replace(/\D/g, ''));
            if (!isNaN(value)) {
                this.value = value;
            }
        }
    });

    // Validasi form sebelum submit
    document.getElementById('transaksiForm').addEventListener('submit', function(e) {
        const nominal = document.getElementById('nominal').value;
        
        if (nominal && parseInt(nominal) < 1) {
            e.preventDefault();
            alert('Nominal harus lebih dari 0');
            return false;
        }
    });
</script>
@endsection