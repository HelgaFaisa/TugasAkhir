{{-- resources/views/admin/users/santri_accounts.blade.php --}}
@extends('layouts.app', ['isAdmin' => true])

@section('title', 'Manajemen Akun Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-user-cog"></i> Manajemen Akun Santri (Web)</h2>
</div>

@if (session('success'))
    <div class="alert alert-success">{!! session('success') !!}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if (session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif

<div class="content-box">
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Info Login Santri (Web) :  </strong>
          Username = Nama Lengkap Santri &nbsp;|&nbsp; Password = NIS Santri
    </div>

    {{-- Tabel akun yang sudah ada --}}
    <h3>Daftar Akun Santri ({{ $users->count() }})</h3>
    <div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID Santri</th>
                <th>Nama Santri</th>
                <th>NIS</th>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
            <tr>
                <td>{{ $user->id_santri }}</td>
                <td>{{ $user->santri->nama_lengkap ?? '-' }}</td>
                <td>{{ $user->santri->nis ?? '-' }}</td>
                <td><code>{{ $user->username }}</code></td>
                <td>
                    <form action="{{ route('admin.users.santri_destroy', $user->id) }}"
                          method="POST" style="display:inline;"
                          onsubmit="return confirm('Yakin hapus akun santri {{ $user->santri->nama_lengkap ?? '' }}?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada akun santri.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{-- Tabel santri belum punya akun --}}
    <h3 style="margin-top:22px;">
        Santri Belum Punya Akun ({{ $santris_tanpa_akun->count() }})
    </h3>

    @if ($santris_tanpa_akun->count() > 0)
    <div style="margin-bottom:12px;">
        <form action="{{ route('admin.users.santri_buat_semua') }}" method="POST" style="display:inline;"
              onsubmit="return confirm('Buat akun untuk SEMUA {{ $santris_tanpa_akun->count() }} santri sekaligus?')">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="fas fa-users"></i> Buat Semua Sekaligus ({{ $santris_tanpa_akun->count() }})
            </button>
        </form>
    </div>
    @endif

    <div class="table-wrapper">

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
            @forelse ($santris_tanpa_akun as $santri)
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
                <td>{{ $santri->kelas ?? '-' }}</td>
                <td>
                    @if($santri->nis)
                        <form action="{{ route('admin.users.santri_buat_akun', $santri->id_santri) }}"
                              method="POST" style="display:inline;"
                              onsubmit="return confirm('Buat akun untuk {{ $santri->nama_lengkap }}?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-user-plus"></i> Buat Akun
                            </button>
                        </form>
                    @else
                        <span class="text-muted">Isi NIS dulu</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-success">
                    <i class="fas fa-check"></i> Semua santri sudah punya akun.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    </div>
</div>
@endsection