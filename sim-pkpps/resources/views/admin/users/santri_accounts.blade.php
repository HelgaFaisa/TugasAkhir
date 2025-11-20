@extends('layouts.app', ['isAdmin' => true])

@section('title', 'Manajemen Akun Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-user-cog"></i> Manajemen Akun Santri</h2>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="content-box">
    <div class="content-header-flex">
        <a href="{{ route('admin.users.santri_create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Akun Santri</a>
    </div>

    <h3>Daftar Akun Santri ({{ $users->count() }})</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID Santri</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
            <tr>
                <td>{{ $user->role_id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->username }}</td>
                <td>
                    <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-key"></i> Reset Password</a>
                    <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Hapus Akun</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Belum ada akun Santri yang terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h3 style="margin-top: 30px;">Data Santri Tanpa Akun ({{ $santris_tanpa_akun->count() }})</h3>
    <p>Berikut adalah data santri yang sudah terdaftar di Data Santri namun belum memiliki akun login. Mereka dapat dipilih saat Anda membuat akun baru.</p>
    {{-- Tampilkan daftar santri yang belum punya akun (opsional) --}}
</div>
@endsection