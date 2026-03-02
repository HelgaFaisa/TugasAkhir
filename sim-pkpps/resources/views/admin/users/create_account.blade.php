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
            <label for="id_santri">Pilih Data Santri *</label>
            <select id="id_santri" name="id_santri" class="form-control @error('id_santri') is-invalid @enderror" required>
                <option value="">-- Pilih Santri --</option>
                @forelse ($list_data as $data)
                    <option value="{{ $data->id_santri }}" {{ old('id_santri') == $data->id_santri ? 'selected' : '' }}>
                        {{ $data->id_santri }} - {{ $data->nama_lengkap }}
                        @if($data->nis) (NIS: {{ $data->nis }}) @endif
                    </option>
                @empty
                    <option value="" disabled>Semua santri sudah memiliki akun {{ $role }}.</option>
                @endforelse
            </select>
            @error('id_santri')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">
                @if($role === 'wali')
                    Pilih santri yang akan dibuatkan akun untuk wali/orang tuanya (login di mobile app).
                @else
                    Pilih santri yang belum memiliki akun login.
                @endif
            </small>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> <strong>Info:</strong><br>
            Username dan password akan dibuat otomatis:<br>
            • <strong>Username:</strong> Nama lengkap santri<br>
            • <strong>Password:</strong> NIS santri
        </div>

        <button type="submit" class="btn btn-success"><i class="fas fa-user-plus"></i> Buat Akun</button>
        <a href="{{ route('admin.users.' . $role . '_accounts') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection