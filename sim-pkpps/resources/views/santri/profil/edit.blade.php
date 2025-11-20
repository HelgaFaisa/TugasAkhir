@extends('layouts.app')

@section('title', 'Edit Profil Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-user-edit"></i> Edit Profil</h2>
</div>

<div class="form-container">
    <form action="{{ route('santri.profil.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        {{-- Info Box --}}
        <div class="info-box" style="margin-bottom: 25px;">
            <p style="margin: 0;">
                <i class="fas fa-info-circle"></i>
                <strong>Info:</strong> Anda hanya dapat mengubah alamat dan nomor HP orang tua. 
                Untuk perubahan data lain, silakan hubungi admin.
            </p>
        </div>
        
        {{-- Nama Santri (Read-only) --}}
        <div class="form-group">
            <label>
                <i class="fas fa-user form-icon"></i>
                Nama Lengkap
            </label>
            <input type="text" class="form-control" value="{{ $santri->nama_lengkap }}" disabled>
            <span class="form-text">Data ini tidak dapat diubah</span>
        </div>
        
        {{-- Alamat Santri --}}
        <div class="form-group">
            <label for="alamat_santri">
                <i class="fas fa-map-marker-alt form-icon"></i>
                Alamat Lengkap
            </label>
            <textarea 
                name="alamat_santri" 
                id="alamat_santri" 
                class="form-control @error('alamat_santri') is-invalid @enderror" 
                rows="4" 
                placeholder="Masukkan alamat lengkap (nama jalan, RT/RW, kelurahan, kecamatan, kota/kabupaten)"
            >{{ old('alamat_santri', $santri->alamat_santri) }}</textarea>
            
            @error('alamat_santri')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            
            <span class="form-text">
                <i class="fas fa-lightbulb"></i> 
                Contoh: Jl. Merdeka No. 123, RT 02/RW 05, Kel. Sukamaju, Kec. Bandung Tengah, Kota Bandung
            </span>
        </div>
        
        {{-- Nomor HP Orang Tua --}}
        <div class="form-group">
            <label for="nomor_hp_ortu">
                <i class="fas fa-phone form-icon"></i>
                Nomor HP Orang Tua/Wali
            </label>
            <input 
                type="text" 
                name="nomor_hp_ortu" 
                id="nomor_hp_ortu" 
                class="form-control @error('nomor_hp_ortu') is-invalid @enderror" 
                value="{{ old('nomor_hp_ortu', $santri->nomor_hp_ortu) }}" 
                placeholder="Contoh: 0812-3456-7890 atau +62812-3456-7890"
            >
            
            @error('nomor_hp_ortu')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            
            <span class="form-text">
                <i class="fas fa-lightbulb"></i> 
                Format: 08XX-XXXX-XXXX atau +628XX-XXXX-XXXX
            </span>
        </div>
        
        {{-- Tombol Aksi --}}
        <div class="btn-group" style="margin-top: 30px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('santri.profil.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

{{-- Script untuk Auto-format Nomor HP --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nomorHpInput = document.getElementById('nomor_hp_ortu');
    
    if (nomorHpInput) {
        nomorHpInput.addEventListener('input', function(e) {
            // Hanya izinkan angka, +, -, spasi, dan tanda kurung
            let value = e.target.value.replace(/[^\d+\-\s()]/g, '');
            e.target.value = value;
        });
        
        nomorHpInput.addEventListener('blur', function(e) {
            // Auto-format saat user selesai mengetik
            let value = e.target.value.trim();
            
            // Jika dimulai dengan 0 dan panjangnya cukup
            if (value.startsWith('0') && value.length >= 10) {
                // Format: 0812-3456-7890
                value = value.replace(/\D/g, ''); // Hapus non-digit
                if (value.length >= 11) {
                    value = value.substring(0, 4) + '-' + 
                           value.substring(4, 8) + '-' + 
                           value.substring(8, 12);
                }
            }
            // Jika dimulai dengan +62
            else if (value.startsWith('+62') && value.length >= 12) {
                value = value.replace(/[^\d+]/g, ''); // Hapus non-digit kecuali +
                if (value.length >= 13) {
                    value = '+62' + value.substring(3, 6) + '-' + 
                           value.substring(6, 10) + '-' + 
                           value.substring(10, 14);
                }
            }
            
            e.target.value = value;
        });
    }
});
</script>
@endsection