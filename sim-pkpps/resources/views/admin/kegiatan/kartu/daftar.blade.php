@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-id-card"></i> Daftarkan Kartu RFID</h2>
</div>

<div class="form-container">
    <div class="info-box" style="margin-bottom: 25px;">
        <p><i class="fas fa-info-circle"></i> Tempelkan kartu RFID ke reader, UID akan otomatis terdeteksi pada kolom di bawah.</p>
    </div>

    <form action="{{ route('admin.kartu-rfid.simpan', $santri->id_santri) }}" method="POST">
        @csrf

        <div class="form-group">
            <label><i class="fas fa-user form-icon"></i> Data Santri</label>
            <table class="detail-table">
                <tr>
                    <th>ID Santri</th>
                    <td><strong>{{ $santri->id_santri }}</strong></td>
                </tr>
                <tr>
                    <th>Nama Lengkap</th>
                    <td>{{ $santri->nama_lengkap }}</td>
                </tr>
                <tr>
                    <th>Kelas</th>
                    <td><span class="badge badge-secondary">{{ $santri->kelas }}</span></td>
                </tr>
            </table>
        </div>

        <div class="form-group">
            <label for="rfid_uid">
                <i class="fas fa-barcode form-icon"></i>
                UID RFID <span style="color: red;">*</span>
            </label>
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="text" 
                       name="rfid_uid" 
                       id="rfid_uid" 
                       class="form-control @error('rfid_uid') is-invalid @enderror" 
                       value="{{ old('rfid_uid') }}" 
                       placeholder="Tempelkan kartu ke reader..."
                       required
                       autofocus>
                <button type="button" class="btn btn-warning" onclick="document.getElementById('rfid_uid').value = ''; document.getElementById('rfid_uid').focus();">
                    <i class="fas fa-redo"></i>
                </button>
            </div>
            @error('rfid_uid')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
            <small class="form-text">UID akan otomatis terisi saat kartu ditempelkan ke reader.</small>
        </div>

        <div id="scanStatus" style="padding: 15px; border-radius: var(--border-radius-sm); text-align: center; margin-bottom: 14px; display: none;"></div>

        <div class="btn-group">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan & Daftarkan
            </button>
            <a href="{{ route('admin.kartu-rfid.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
const rfidInput = document.getElementById('rfid_uid');
const scanStatus = document.getElementById('scanStatus');

rfidInput.addEventListener('input', function() {
    if (this.value.length > 5) {
        scanStatus.style.display = 'block';
        scanStatus.style.background = 'linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%)';
        scanStatus.style.color = 'var(--success-color)';
        scanStatus.innerHTML = '<i class="fas fa-check-circle"></i> RFID Terdeteksi: <strong>' + this.value + '</strong>';
    } else {
        scanStatus.style.display = 'none';
    }
});

// Auto-focus ke input
setInterval(() => {
    if (document.activeElement !== rfidInput) {
        rfidInput.focus();
    }
}, 1000);
</script>
@endsection