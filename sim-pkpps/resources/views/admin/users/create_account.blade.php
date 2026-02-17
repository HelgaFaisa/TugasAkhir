@extends('layouts.app', ['isAdmin' => true])

@section('title', 'Buat Akun ' . ucfirst($role))

@section('content')
<div class="page-header">
    <h2>Buat Akun Login {{ $role === 'wali' ? 'Wali Santri (Mobile App)' : 'Santri' }}</h2>
</div>

<div class="content-box">
    <form action="{{ route('admin.users.' . $role . '_store') }}" method="POST" class="data-form">
        @csrf

        <div class="form-group">
            <label for="role_id">Pilih Data Santri *</label>
            <select id="role_id" name="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                <option value="" data-nama="" data-nis="">-- Pilih Santri --</option>
                @forelse ($list_data as $data)
                    <option value="{{ $data->id_santri }}" 
                            data-nama="{{ $data->nama_lengkap }}" 
                            data-nis="{{ $data->nis }}"
                            {{ old('role_id') == $data->id_santri ? 'selected' : '' }}>
                        {{ $data->id_santri }} - {{ $data->nama_lengkap }} 
                        @if($data->nis) (NIS: {{ $data->nis }}) @endif
                    </option>
                @empty
                    <option value="" disabled>Semua santri sudah memiliki akun {{ $role }}.</option>
                @endforelse
            </select>
            @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">
                @if($role === 'wali')
                    Pilih santri yang akan dibuatkan akun untuk wali/orang tuanya (login di mobile app).
                @else
                    Pilih santri yang belum memiliki akun login.
                @endif
            </small>
        </div>

        <div class="form-group">
            <label for="username">Username (untuk Login) *</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" 
                   class="form-control @error('username') is-invalid @enderror" 
                   {{ $role === 'wali' ? 'readonly' : '' }} required>
            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">
                @if($role === 'wali')
                    <i class="fas fa-info-circle"></i> Username otomatis diisi dengan <strong>nama santri</strong> (terisi saat memilih santri).
                @else
                    Username bebas untuk login santri.
                @endif
            </small>
        </div>

        <div class="form-group">
            <label for="password">Password *</label>
            <input type="text" id="password" name="password" value="{{ old('password') }}"
                   class="form-control @error('password') is-invalid @enderror" 
                   {{ $role === 'wali' ? 'readonly' : '' }} required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">
                @if($role === 'wali')
                    <i class="fas fa-info-circle"></i> Password otomatis diisi dengan <strong>NIS santri</strong> (terisi saat memilih santri).
                @else
                    Password bebas untuk login santri.
                @endif
            </small>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password *</label>
            <input type="text" id="password_confirmation" name="password_confirmation" 
                   class="form-control" {{ $role === 'wali' ? 'readonly' : '' }} required>
            <small class="form-text text-muted">
                @if($role === 'wali')
                    <i class="fas fa-check-circle text-success"></i> Otomatis sama dengan password.
                @endif
            </small>
        </div>

        @if($role === 'wali')
        <div class="alert alert-info">
            <i class="fas fa-mobile-alt"></i> <strong>Info Login Mobile App:</strong><br>
            Setelah akun dibuat, wali/orang tua dapat login di aplikasi mobile dengan:<br>
            • <strong>Username:</strong> Nama santri (terisi otomatis)<br>
            • <strong>Password:</strong> NIS santri (terisi otomatis)
        </div>
        @endif

        <button type="submit" class="btn btn-success"><i class="fas fa-user-plus"></i> Buat Akun</button>
        <a href="{{ route('admin.users.' . $role . '_accounts') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

@if($role === 'wali')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleIdSelect = document.getElementById('role_id');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    
    roleIdSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const namaSantri = selectedOption.getAttribute('data-nama') || '';
        const nisSantri = selectedOption.getAttribute('data-nis') || '';
        
        // Auto-fill username dengan nama santri
        usernameInput.value = namaSantri;
        
        // Auto-fill password dengan NIS
        passwordInput.value = nisSantri;
        passwordConfirmInput.value = nisSantri;
        
        // Jika NIS kosong, beri peringatan
        if (this.value && !nisSantri) {
            alert('Perhatian: Santri ini belum memiliki NIS. Silakan isi NIS terlebih dahulu di data santri, atau isi password secara manual.');
            passwordInput.removeAttribute('readonly');
            passwordConfirmInput.removeAttribute('readonly');
            passwordInput.value = '';
            passwordConfirmInput.value = '';
        } else {
            // Set readonly jika NIS ada
            passwordInput.setAttribute('readonly', 'readonly');
            passwordConfirmInput.setAttribute('readonly', 'readonly');
        }
    });
});
</script>
@endif
@endsection