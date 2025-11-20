@extends('layouts.app', ['isAdmin' => true])

@section('title', 'Buat Akun ' . ucfirst($role))

@section('content')
<div class="page-header">
    <h2>Buat Akun Login Baru ({{ ucfirst($role) }})</h2>
</div>

<div class="content-box">
    <form action="{{ route('admin.users.' . $role . '_store') }}" method="POST" class="data-form">
        @csrf

        <div class="form-group">
            <label for="role_id">Pilih Data Induk {{ ucfirst($role) }} *</label>
            <select id="role_id" name="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                <option value="">-- Pilih Data {{ ucfirst($role) }} (ID - Nama) --</option>
                @forelse ($list_data as $data)
                    @php
                        // Sesuaikan field nama dan ID berdasarkan role
                        $id_field = $role === 'santri' ? 'id_santri' : 'id_wali';
                        $name_field = $role === 'santri' ? 'nama_lengkap' : 'nama_wali';
                    @endphp
                    <option value="{{ $data->$id_field }}" {{ old('role_id') == $data->$id_field ? 'selected' : '' }}>
                        {{ $data->$id_field }} - {{ $data->$name_field }}
                    </option>
                @empty
                    <option value="" disabled>Semua data {{ $role }} sudah memiliki akun.</option>
                @endforelse
            </select>
            @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">Hanya menampilkan data {{ $role }} yang belum memiliki akun login.</small>
        </div>

        <div class="form-group">
            <label for="username">Username (untuk Login) *</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" class="form-control @error('username') is-invalid @enderror" required>
            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">Contoh: {{ $role == 'santri' ? 'S001' : 'WS001' }}</small>
        </div>

        <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password *</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success"><i class="fas fa-user-plus"></i> Buat Akun</button>
        <a href="{{ route('admin.users.' . $role . '_accounts') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection