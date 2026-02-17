@extends('layouts.app', ['isAdmin' => true])

@section('title', 'Manajemen Akun Wali Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-mobile-alt"></i> Manajemen Akun Wali Santri (Mobile App)</h2>
</div>

@if (session('success'))
    <div class="alert alert-success">{!! session('success') !!}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="content-box">
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Info:</strong> Akun wali digunakan oleh orang tua/wali untuk login di aplikasi mobile dan melihat data santri (anaknya).<br>
        <strong>Format Login:</strong> Username = Nama Santri, Password = NIS Santri
    </div>

    <div class="content-header-flex">
        <a href="{{ route('admin.users.wali_create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Akun Wali</a>
    </div>

    <h3>Daftar Akun Wali Santri ({{ $users->count() }})</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID Santri</th>
                <th>Nama Santri</th>
                <th>NIS</th>
                <th>Username (Login)</th>
                <th>Password</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
            <tr>
                <td>{{ $user->role_id }}</td>
                <td>{{ $user->santri->nama_lengkap ?? '-' }}</td>
                <td>{{ $user->santri->nis ?? '-' }}</td>
                <td><code>{{ $user->username }}</code></td>
                <td><span class="text-muted">NIS: {{ $user->santri->nis ?? '-' }}</span></td>
                <td>
                    <form action="{{ route('admin.users.wali_reset_password', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Reset password akun {{ $user->name }} ke NIS?')">
                            <i class="fas fa-key"></i> Reset
                        </button>
                    </form>
                    <form action="{{ route('admin.users.wali_destroy', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus akun wali {{ $user->name }}?')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada akun Wali Santri yang terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <h3 style="margin-top: 30px;">Santri Belum Memiliki Akun Wali ({{ $santris_tanpa_wali->count() }})</h3>
    <p>Daftar santri yang belum dibuatkan akun wali untuk login di aplikasi mobile.</p>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID Santri</th>
                <th>NIS</th>
                <th>Nama Santri</th>
                <th>Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($santris_tanpa_wali as $santri)
            <tr>
                <td>{{ $santri->id_santri }}</td>
                <td>
                    @if($santri->nis)
                        {{ $santri->nis }}
                    @else
                        <span class="text-danger">Belum ada NIS</span>
                    @endif
                </td>
                <td>{{ $santri->nama_lengkap }}</td>
                <td>{{ $santri->kelas }}</td>
                <td>
                    @if($santri->nis)
                        <a href="{{ route('admin.users.wali_create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-user-plus"></i> Buat Akun
                        </a>
                    @else
                        <span class="text-muted">Isi NIS dulu</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-success"><i class="fas fa-check"></i> Semua santri sudah memiliki akun wali.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection