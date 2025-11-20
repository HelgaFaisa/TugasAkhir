@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Transaksi Uang Saku</h2>
</div>

<div class="form-container">
    <form action="{{ route('admin.uang-saku.update', $transaksi->id) }}" method="POST" id="transaksiForm">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="id_santri">
                <i class="fas fa-user form-icon"></i>
                Santri
            </label>
            <input type="text" class="form-control" value="{{ $transaksi->santri->id_santri }} - {{ $transaksi->santri->nama_lengkap }}" readonly disabled>
            <small class="form-text">Santri tidak dapat diubah</small>
        </div>

        <div class="form-group">
            <label for="jenis_transaksi">
                <i class="fas fa-exchange-alt form-icon"></i>
                Jenis Transaksi <span style="color: red;">*</span>
            </label>
            <select name="jenis_transaksi" id="jenis_transaksi" class="form-control @error('jenis_transaksi') is-invalid @enderror" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="pemasukan" {{ old('jenis_transaksi', $transaksi->jenis_transaksi) == 'pemasukan' ? 'selected' : '' }}>
                    Pemasukan (Terima Uang Saku)
                </option>
                <option value="pengeluaran" {{ old('jenis_transaksi', $transaksi->jenis_transaksi) == 'pengeluaran' ? 'selected' : '' }}>
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
                   value="{{ old('nominal', $transaksi->nominal) }}" placeholder="Contoh: 50000" min="1" step="1" required>
            @error('nominal')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="tanggal_transaksi">
                <i class="fas fa-calendar form-icon"></i>
                Tanggal Transaksi <span style="color: red;">*</span>
            </label>
            <input type="date" name="tanggal_transaksi" id="tanggal_transaksi" 
                   class="form-control @error('tanggal_transaksi') is-invalid @enderror" 
                   value="{{ old('tanggal_transaksi', $transaksi->tanggal_transaksi->format('Y-m-d')) }}" required>
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
                      rows="4" placeholder="Contoh: Uang saku bulan Januari 2025">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
            @error('keterangan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-success hover-lift">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('admin.uang-saku.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
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