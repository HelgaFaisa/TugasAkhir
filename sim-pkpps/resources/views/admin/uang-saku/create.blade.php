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

        {{-- Info Card Santri (AJAX) --}}
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
document.getElementById('id_santri').addEventListener('change', function() {
    var infoBox = document.getElementById('santri-info');
    var val = this.value;
    if (!val) { infoBox.style.display = 'none'; return; }

    fetch('{{ url("admin/uang-saku/santri-info") }}/' + val)
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
            infoBox.style.display = 'block';
        })
        .catch(function() { infoBox.style.display = 'none'; });
});

// Trigger on page load if santri pre-selected
if (document.getElementById('id_santri').value) {
    document.getElementById('id_santri').dispatchEvent(new Event('change'));
}
</script>
@endsection