@extends('layouts.app', ['isAdmin' => true])

@section('title', $admin ? 'Edit Akun Admin' : 'Tambah Akun Admin')

@section('content')
<div class="page-header">
    <h2>
        <i class="fas fa-user-shield"></i>
        {{ $admin ? 'Edit Akun Admin: ' . $admin->name : 'Tambah Akun Admin' }}
    </h2>
</div>

<div class="content-box" style="max-width: 540px;">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin:0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $action }}" method="POST" class="data-form">
        @csrf
        @if ($method === 'PUT')
            @method('PUT')
        @endif

        <div class="form-group">
            <label for="name">Nama Lengkap *</label>
            <input type="text" id="name" name="name"
                   value="{{ old('name', $admin->name ?? '') }}"
                   class="form-control @error('name') is-invalid @enderror"
                   required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="email">Email (digunakan untuk login) *</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email', $admin->email ?? '') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="role">Role *</label>
            <select id="role" name="role"
                    class="form-control @error('role') is-invalid @enderror"
                    required>
                <option value="">-- Pilih Role --</option>
                <option value="akademik" {{ old('role', $admin->role ?? '') === 'akademik' ? 'selected' : '' }}>
                    Akademik (Data santri, kegiatan, pelanggaran, absensi, rekap)
                </option>
                <option value="pamong" {{ old('role', $admin->role ?? '') === 'pamong' ? 'selected' : '' }}>
                    Pamong (Uang saku, absensi RFID, capaian, kesehatan, kepulangan)
                </option>
            </select>
            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="password">
                Password {{ $admin ? '(kosongkan jika tidak ingin mengganti)' : '*' }}
            </label>
            <input type="password" id="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   {{ $admin ? '' : 'required' }}>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">Minimal 8 karakter.</small>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password {{ $admin ? '' : '*' }}</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="form-control"
                   {{ $admin ? '' : 'required' }}>
        </div>

        <div style="display:flex;gap:10px;margin-top:16px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $admin ? 'Simpan Perubahan' : 'Buat Akun' }}
            </button>
            <a href="{{ route('admin.users.admin_accounts') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection
