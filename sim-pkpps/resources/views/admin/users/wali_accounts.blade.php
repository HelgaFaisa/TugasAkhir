{{-- resources/views/admin/users/wali_accounts.blade.php --}}
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
@if (session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif

<div class="content-box">
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Info Login Wali (Mobile):</strong><br>
        <strong>Username:</strong> Nama Orang Tua
        <small class="text-muted">(jika ada nama orang tua yang sama, otomatis menjadi "Nama Orang Tua - Nama Santri")</small><br>
        <strong>Password:</strong> NIS Santri
    </div>

    {{-- Tabel akun wali yang sudah ada --}}
    <h3>Daftar Akun Wali ({{ $users->count() }})</h3>
    <div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID Santri</th>
                <th>Nama Santri</th>
                <th>Nama Orang Tua</th>
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
                <td>{{ $user->santri->nama_orang_tua ?? '-' }}</td>
                <td>{{ $user->santri->nis ?? '-' }}</td>
                <td><code>{{ $user->username }}</code></td>
                <td>
                    <form action="{{ route('admin.users.wali_destroy', $user->id) }}"
                          method="POST" style="display:inline;"
                          onsubmit="return confirm('Yakin hapus akun wali {{ $user->santri->nama_lengkap ?? '' }}?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada akun wali.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{-- Tabel santri belum punya akun wali --}}
    <h3 style="margin-top:22px;">
        Santri Belum Punya Akun Wali ({{ $santris_tanpa_wali->count() }})
    </h3>

    @if ($santris_tanpa_wali->count() > 0)
    <div style="margin-bottom:12px;">
        <form action="{{ route('admin.users.wali_buat_semua') }}" method="POST" style="display:inline;"
              onsubmit="return confirm('Buat akun wali untuk SEMUA {{ $santris_tanpa_wali->count() }} santri sekaligus?')">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="fas fa-users"></i> Buat Semua Sekaligus ({{ $santris_tanpa_wali->count() }})
            </button>
        </form>
    </div>
    @endif

    @php
        // Kumpulkan nama ortu yang sudah dipakai di akun existing
        // untuk preview username yang akan dibuat
        $namaOrtuSudahAda = \App\Models\SantriAccount::where('role', 'wali')
            ->pluck('username')
            ->toArray();
        $namaOrtuPreviewDipakai = [];
    @endphp

    <div class="table-wrapper">

    <table class="data-table">
        <thead>
            <tr>
                <th>ID Santri</th>
                <th>NIS</th>
                <th>Nama Santri</th>
                <th>Nama Orang Tua</th>
                <th>Kelas</th>
                <th>Username (Preview)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($santris_tanpa_wali as $santri)
            @php
                // Preview username: sama persis dgn logika resolveUsernameWali() di controller
                $previewUsername = null;
                if ($santri->nama_orang_tua) {
                    $usernameDefault = $santri->nama_orang_tua;
                    $sudahDiDb       = in_array($usernameDefault, $namaOrtuSudahAda);
                    $sudahDiMemori   = in_array($usernameDefault, $namaOrtuPreviewDipakai);

                    if ($sudahDiDb || $sudahDiMemori) {
                        $previewUsername = $usernameDefault . ' - ' . $santri->nama_lengkap;
                    } else {
                        $previewUsername = $usernameDefault;
                    }

                    $namaOrtuPreviewDipakai[] = $previewUsername;
                }
            @endphp
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
                <td>{{ $santri->nama_orang_tua ?? '-' }}</td>
                <td>{{ $santri->kelas ?? '-' }}</td>
                <td>
                    @if($previewUsername)
                        <code style="font-size:.78rem;">{{ $previewUsername }}</code>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if($santri->nis && $santri->nama_orang_tua)
                        <form action="{{ route('admin.users.wali_buat_akun', $santri->id_santri) }}"
                              method="POST" style="display:inline;"
                              onsubmit="return confirm('Buat akun wali untuk {{ $santri->nama_lengkap }}?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-user-plus"></i> Buat Akun
                            </button>
                        </form>
                    @elseif(!$santri->nis)
                        <span class="text-muted">Isi NIS dulu</span>
                    @else
                        <span class="text-muted">Isi nama orang tua dulu</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-success">
                    <i class="fas fa-check"></i> Semua santri sudah punya akun wali.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    </div>
</div>
@endsection