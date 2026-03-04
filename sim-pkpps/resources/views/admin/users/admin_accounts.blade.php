@extends('layouts.app', ['isAdmin' => true])

@section('title', 'Manajemen Akun Admin')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-user-shield"></i> Manajemen Akun Admin</h2>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="content-box">
    <div class="content-header-flex">
        <a href="{{ route('admin.users.admin_create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Akun Admin
        </a>
    </div>

    <p class="text-muted" style="margin-top: 8px;">
        Kelola akun untuk role <strong>Akademik</strong> dan <strong>Pamong</strong>.
        Akun <strong>Super Admin</strong> tidak dapat dihapus dari halaman ini.
    </p>

    <div class="table-wrapper">

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($admins as $i => $admin)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $admin->name }}</td>
                <td>{{ $admin->email }}</td>
                <td>
                    @if ($admin->role === 'super_admin')
                        <span class="badge" style="background:#6f42c1;color:#fff;padding:3px 8px;border-radius:4px;">Super Admin</span>
                    @elseif ($admin->role === 'akademik')
                        <span class="badge" style="background:#0d6efd;color:#fff;padding:3px 8px;border-radius:4px;">Akademik</span>
                    @else
                        <span class="badge" style="background:#198754;color:#fff;padding:3px 8px;border-radius:4px;">Pamong</span>
                    @endif
                </td>
                <td>{{ $admin->created_at->format('d/m/Y') }}</td>
                <td>
                    @if ($admin->role !== 'super_admin')
                        <a href="{{ route('admin.users.admin_edit', $admin->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.users.admin_destroy', $admin->id) }}" method="POST" style="display:inline;"
                              onsubmit="return confirm('Yakin hapus akun {{ $admin->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    @else
                        <span class="text-muted"><i class="fas fa-lock"></i> Protected</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada akun admin terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    </div>
</div>
@endsection