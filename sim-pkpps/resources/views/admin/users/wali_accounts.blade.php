@extends('layouts.app', ['isAdmin' => true])

@section('title', 'Manajemen Akun Wali Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-user-cog"></i> Manajemen Akun Wali Santri</h2>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="content-box">
    <div class="content-header-flex">
        <a href="{{ route('admin.users.wali_create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Akun Wali</a>
    </div>

    <h3>Daftar Akun Wali Santri ({{ $users->count() }})</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID Wali</th>
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
                <td colspan="4" class="text-center">Belum ada akun Wali Santri yang terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <h3 style="margin-top: 30px;">Data Wali Terdaftar ({{ $walis->count() }})</h3>
    <p>Pastikan Anda sudah mendaftarkan biodata wali santri di tabel `walis` sebelum membuat akun login.</p>
</div>
@endsection