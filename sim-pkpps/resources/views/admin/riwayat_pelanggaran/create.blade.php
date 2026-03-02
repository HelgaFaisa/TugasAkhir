@extends('layouts.app')

@section('title', 'Tambah Riwayat Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Riwayat Pelanggaran</h2>
</div>

<div class="content-box">
    <form action="{{ route('admin.riwayat-pelanggaran.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>
                <i class="fas fa-id-card form-icon"></i>
                ID Riwayat (Preview)
            </label>
            <input type="text" class="form-control" value="{{ $nextIdRiwayat }}" disabled>
            <span class="form-text">ID akan dibuat otomatis</span>
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
                    <option value="{{ $santri->id_santri }}" {{ old('id_santri') == $santri->id_santri ? 'selected' : '' }}>
                        {{ $santri->nama_lengkap }} ({{ $santri->id_santri }})
                    </option>
                @endforeach
            </select>
            @error('id_santri')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="filter_klasifikasi">
                <i class="fas fa-filter form-icon"></i>
                Filter Klasifikasi (Opsional)
            </label>
            <select id="filter_klasifikasi" class="form-control">
                <option value="">-- Semua Klasifikasi --</option>
                @foreach($klasifikasiList as $kl)
                    <option value="{{ $kl->id_klasifikasi }}">{{ $kl->nama_klasifikasi }}</option>
                @endforeach
            </select>
            <span class="form-text">Gunakan filter ini untuk mempermudah pencarian pelanggaran</span>
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
                            data-klasifikasi="{{ $kategori->id_klasifikasi }}"
                            data-poin="{{ $kategori->poin }}"
                            data-kafaroh="{{ $kategori->kafaroh }}"
                            {{ old('id_kategori') == $kategori->id_kategori ? 'selected' : '' }}>
                        [{{ $kategori->klasifikasi->nama_klasifikasi ?? '-' }}] {{ $kategori->nama_pelanggaran }} ({{ $kategori->poin }} poin)
                    </option>
                @endforeach
            </select>
            @error('id_kategori')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div id="info-pelanggaran" style="display: none; background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 14px; border-left: 4px solid var(--warning-color);">
            <h4 style="margin: 0 0 10px 0; color: var(--warning-color);">
                <i class="fas fa-info-circle"></i> Informasi Pelanggaran
            </h4>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 150px; padding: 5px 0; font-weight: 600;">Poin:</td>
                    <td style="padding: 5px 0;">
                        <span class="badge badge-danger" id="display-poin" style="font-size: 1em;"></span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; font-weight: 600; vertical-align: top;">Kafaroh:</td>
                    <td style="padding: 5px 0;">
                        <div id="display-kafaroh" style="color: var(--text-color);"></div>
                    </td>
                </tr>
            </table>
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
                   value="{{ old('tanggal', date('Y-m-d')) }}"
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
                      rows="4"
                      placeholder="Keterangan tambahan tentang pelanggaran...">{{ old('keterangan') }}</textarea>
            @error('keterangan')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="btn-group" style="margin-top: 22px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('admin.riwayat-pelanggaran.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

<script>
// Filter pelanggaran berdasarkan klasifikasi
document.getElementById('filter_klasifikasi').addEventListener('change', function() {
    const selectedKlasifikasi = this.value;
    const kategoriSelect = document.getElementById('id_kategori');
    const options = kategoriSelect.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        
        const optionKlasifikasi = option.getAttribute('data-klasifikasi');
        
        if (selectedKlasifikasi === '' || optionKlasifikasi === selectedKlasifikasi) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
    
    // Reset pilihan kategori jika tidak sesuai filter
    const selectedOption = kategoriSelect.options[kategoriSelect.selectedIndex];
    if (selectedOption && selectedKlasifikasi && selectedOption.getAttribute('data-klasifikasi') !== selectedKlasifikasi) {
        kategoriSelect.value = '';
        document.getElementById('info-pelanggaran').style.display = 'none';
    }
});

// Tampilkan info pelanggaran saat kategori dipilih
document.getElementById('id_kategori').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const infoDiv = document.getElementById('info-pelanggaran');
    
    if (this.value === '') {
        infoDiv.style.display = 'none';
        return;
    }
    
    const poin = selectedOption.getAttribute('data-poin');
    const kafaroh = selectedOption.getAttribute('data-kafaroh');
    
    document.getElementById('display-poin').textContent = poin + ' Poin';
    document.getElementById('display-kafaroh').textContent = kafaroh || 'Tidak ada kafaroh';
    
    infoDiv.style.display = 'block';
});

// Trigger info display jika ada old value
window.addEventListener('load', function() {
    const kategoriSelect = document.getElementById('id_kategori');
    if (kategoriSelect.value) {
        kategoriSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection