@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-id-card"></i> Kelola Kartu RFID Santri</h2>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<div class="content-box">
    <div style="margin-bottom: 14px;">
        <form method="GET" class="filter-form-inline">
            <select name="filter" class="form-control">
                <option value="">-- Semua Santri --</option>
                <option value="ada_rfid" {{ request('filter') == 'ada_rfid' ? 'selected' : '' }}>Sudah Punya RFID</option>
                <option value="belum_rfid" {{ request('filter') == 'belum_rfid' ? 'selected' : '' }}>Belum Punya RFID</option>
            </select>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>

            @if(request('filter'))
                <a href="{{ route('admin.kartu-rfid.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            @endif
        </form>
    </div>

    @if($santris->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">ID Santri</th>
                    <th>Nama Santri</th>
                    <th style="width: 100px;">Kelas</th>
                    <th style="width: 200px;">UID RFID</th>
                    <th style="width: 120px; text-align: center;">Status</th>
                    <th style="width: 250px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($santris as $index => $santri)
                <tr>
                    <td>{{ $santris->firstItem() + $index }}</td>
                    <td><strong>{{ $santri->id_santri }}</strong></td>
                    <td>{{ $santri->nama_lengkap }}</td>
                    <td><span class="badge badge-secondary">{{ $santri->kelasSantri->first()?->kelas?->nama_kelas ?? '-' }}</span></td>
                    <td>
                        @if($santri->rfid_uid)
                            <code style="font-size: 0.85rem;">{{ $santri->rfid_uid }}</code>
                        @else
                            <span style="color: var(--text-light);">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($santri->rfid_uid)
                            <span class="badge badge-success"><i class="fas fa-check"></i> Terdaftar</span>
                        @else
                            <span class="badge badge-warning"><i class="fas fa-exclamation"></i> Belum</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($santri->rfid_uid)
                            <a href="{{ route('admin.kartu-rfid.cetak', $santri->id_santri) }}" class="btn btn-sm btn-primary" title="Cetak Kartu" target="_blank">
                                <i class="fas fa-print"></i> Cetak
                            </a>
                            <form action="{{ route('admin.kartu-rfid.hapus', $santri->id_santri) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus RFID ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus RFID">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        @else
                            <a href="{{ route('admin.kartu-rfid.daftar', $santri->id_santri) }}" class="btn btn-sm btn-success" title="Daftarkan RFID">
                                <i class="fas fa-id-card"></i> Daftarkan RFID
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 14px;">
            {{ $santris->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-users-slash"></i>
            <h3>Tidak Ada Data Santri</h3>
            <p>Belum ada santri aktif yang terdaftar.</p>
        </div>
    @endif
</div>
@endsection