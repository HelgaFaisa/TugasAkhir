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

        {{-- Info Card Santri (AJAX on page load) --}}
        <div id="santri-info" style="display:none; margin-bottom:20px;">
            <div class="content-box" style="padding:16px; background:var(--primary-light);">
                <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; margin-bottom:12px;">
                    <div>
                        <small class="text-muted">Saldo Terakhir</small>
                        <div id="info-saldo" style="font-weight:700; font-size:1.1rem;"></div>
                    </div>
                    <div>
                        <small class="text-muted">Pemasukan Bln Ini</small>
                        <div id="info-masuk" style="font-weight:600; color:#6FBA9D;"></div>
                    </div>
                    <div>
                        <small class="text-muted">Pengeluaran Bln Ini</small>
                        <div id="info-keluar" style="font-weight:600; color:#FF8B94;"></div>
                    </div>
                </div>
                <div id="info-riwayat"></div>
            </div>
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
(function() {
    var idSantri = '{{ $transaksi->santri->id_santri }}';
    fetch('{{ url("admin/uang-saku/santri-info") }}/' + idSantri)
        .then(function(r) { return r.json(); })
        .then(function(d) {
            var saldoColor = d.saldo_raw >= 0 ? '#6FBA9D' : '#FF8B94';
            document.getElementById('info-saldo').innerHTML = '<span style="color:' + saldoColor + '">Rp ' + d.saldo_terakhir + '</span>';
            document.getElementById('info-masuk').textContent = 'Rp ' + d.total_pemasukan_bulan_ini;
            document.getElementById('info-keluar').textContent = 'Rp ' + d.total_pengeluaran_bulan_ini;

            var html = '';
            if (d.transaksi_terakhir.length > 0) {
                html = '<small class="text-muted">3 Transaksi Terakhir:</small><table class="data-table" style="margin-top:6px;font-size:.85rem;"><thead><tr><th>Tanggal</th><th>Jenis</th><th>Nominal</th><th>Ket</th></tr></thead><tbody>';
                d.transaksi_terakhir.forEach(function(t) {
                    var badge = t.jenis === 'pemasukan'
                        ? '<span class="badge badge-success">Masuk</span>'
                        : '<span class="badge badge-danger">Keluar</span>';
                    html += '<tr><td>' + t.tanggal + '</td><td>' + badge + '</td><td>Rp ' + t.nominal + '</td><td>' + t.keterangan + '</td></tr>';
                });
                html += '</tbody></table>';
            }
            document.getElementById('info-riwayat').innerHTML = html;
            document.getElementById('santri-info').style.display = 'block';
        })
        .catch(function() {});
})();
</script>
@endsection