@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Transaksi Keuangan</h2>
</div>

<div class="form-container">
    <form action="{{ route('admin.keuangan.update', $transaksi->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="jenis"><i class="fas fa-exchange-alt form-icon"></i> Jenis Transaksi <span style="color:red;">*</span></label>
            <select name="jenis" id="jenis" class="form-control @error('jenis') is-invalid @enderror" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="pemasukan" {{ old('jenis', $transaksi->jenis)=='pemasukan'?'selected':'' }}>Pemasukan (Kas Masuk)</option>
                <option value="pengeluaran" {{ old('jenis', $transaksi->jenis)=='pengeluaran'?'selected':'' }}>Pengeluaran (Kas Keluar)</option>
            </select>
            @error('jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="nominal"><i class="fas fa-money-bill-wave form-icon"></i> Nominal (Rp) <span style="color:red;">*</span></label>
            <input type="number" name="nominal" id="nominal" class="form-control @error('nominal') is-invalid @enderror"
                   value="{{ old('nominal', $transaksi->nominal) }}" min="1" step="1" required>
            @error('nominal') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="tanggal"><i class="fas fa-calendar form-icon"></i> Tanggal <span style="color:red;">*</span></label>
            <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                   value="{{ old('tanggal', $transaksi->tanggal->format('Y-m-d')) }}" required>
            @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="keterangan"><i class="fas fa-sticky-note form-icon"></i> Keterangan</label>
            <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                      rows="3">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
            @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-success hover-lift"><i class="fas fa-save"></i> Simpan Perubahan</button>
            <a href="{{ route('admin.keuangan.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </form>
</div>
@endsection
