@extends('layouts.app')

@section('title', 'Data Kesehatan Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-heartbeat"></i> Data Kesehatan Santri</h2>
</div>

<!-- Flash Messages -->
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

<!-- Content Box -->
<div class="content-box">
    <!-- Header Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.kesehatan-santri.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Data Kesehatan
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <form method="GET" action="{{ route('admin.kesehatan-santri.index') }}" id="filterForm" style="margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Nama santri, keluhan..." 
                       value="{{ request('search') }}"
                       id="searchInput">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="status" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="month" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Semua Bulan</option>
                    @foreach($monthOptions as $num => $name)
                        <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="year" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Semua Tahun</option>
                    @foreach($yearOptions as $year)
                        <option value="{{ $year }}" {{ (request('year') ?: date('Y')) == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.kesehatan-santri.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>


    <!-- Data Table -->
    @if($kesehatanSantri->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 9%;">ID Santri</th>
                    <th style="width: 15%;">Nama Santri</th>
                    <th style="width: 10%;">Tgl Masuk</th>
                    <th style="width: 18%;">Keluhan</th>
                    <th style="width: 10%;">Tgl Keluar</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 5%;">Lama</th>
                    <th style="width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kesehatanSantri as $index => $data)
                <tr>
                    <td class="text-center">{{ $kesehatanSantri->firstItem() + $index }}</td> 
                    <td><strong>{{ $data->id_santri }}</strong></td>
                    <td>
                        <strong>{{ $data->santri->nama_lengkap }}</strong><br>
                        <small style="color: #7F8C8D;">{{ $data->santri->kelas }}</small>
                    </td>
                    <td>{{ $data->tanggal_masuk_formatted }}</td>
                    <td>
                        <span title="{{ $data->keluhan }}">
                            {{ Str::limit($data->keluhan, 50) }}
                        </span>
                    </td>
                    <td>
                        @if($data->tanggal_keluar)
                            {{ $data->tanggal_keluar_formatted }}
                        @else
                            <span style="color: #E74C3C; font-weight: bold;">Belum keluar</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="btn btn-{{ $data->status_badge_color }} btn-sm" 
                              style="cursor: default; padding: 5px 10px;">
                            {{ ucfirst($data->status) }}
                        </span>
                    </td>
                    <td class="text-center"><strong>{{ $data->lama_dirawat }}</strong> hari</td>
                    <td>
                        <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <a href="{{ route('admin.kesehatan-santri.show', $data) }}" 
                               class="btn btn-primary btn-sm"
                               title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <a href="{{ route('admin.kesehatan-santri.edit', $data) }}" 
                               class="btn btn-warning btn-sm"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            @if($data->status == 'dirawat')
                                <button type="button" 
                                        class="btn btn-success btn-sm" 
                                        title="Keluar UKP"
                                        onclick="keluarUkp({{ $data->id }}, '{{ $data->santri->nama_lengkap }}', '{{ $data->tanggal_masuk->format('Y-m-d') }}')">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>
                            @endif
                            
                            <a href="{{ route('admin.kesehatan-santri.cetak-surat', $data) }}" 
                               class="btn btn-secondary btn-sm"
                               title="Cetak Surat"
                               target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                            
                            <form action="{{ route('admin.kesehatan-santri.destroy', $data) }}" 
                                  method="POST" 
                                  style="display: inline;"
                                  onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-danger btn-sm"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $kesehatanSantri->appends(request()->query())->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 50px; color: #7F8C8D;">
            <i class="fas fa-search" style="font-size: 3em; margin-bottom: 15px; color: #BDC3C7;"></i>
            <h3>Tidak ada data kesehatan santri</h3>
            <p>Belum ada data kesehatan santri yang tercatat atau sesuai dengan filter yang dipilih.</p>
            <a href="{{ route('admin.kesehatan-santri.create') }}" class="btn btn-primary" style="margin-top: 15px;">
                <i class="fas fa-plus"></i> Tambah Data Kesehatan
            </a>
        </div>
    @endif
</div>

<!-- Modal Keluar UKP -->
<div id="keluarUkpModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 12px; min-width: 400px; max-width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <h3 style="margin-bottom: 20px; color: var(--primary-color);">
            <i class="fas fa-sign-out-alt"></i> Keluar dari UKP
        </h3>
        
        <form id="keluarUkpForm" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="form-group">
                <label><i class="fas fa-user form-icon"></i>Nama Santri</label>
                <input type="text" id="modalNamaSantri" readonly class="form-control" style="background-color: #F8F9FA;">
            </div>
            
            <div class="form-group">
                <label for="tanggal_keluar"><i class="fas fa-calendar form-icon"></i>Tanggal Keluar *</label>
                <input type="date" name="tanggal_keluar" id="tanggal_keluar" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="status_keluar"><i class="fas fa-check-circle form-icon"></i>Status *</label>
                <select name="status" id="status_keluar" class="form-control" required>
                    <option value="">Pilih Status</option>
                    <option value="sembuh">Sembuh</option>
                    <option value="izin">Izin Pulang</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeKeluarUkpModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function keluarUkp(id, namaSantri, tanggalMasuk) {
    document.getElementById('modalNamaSantri').value = namaSantri;
    document.getElementById('keluarUkpForm').action = `/admin/kesehatan-santri/${id}/keluar-ukp`;
    document.getElementById('tanggal_keluar').value = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_keluar').min = tanggalMasuk;
    document.getElementById('tanggal_keluar').max = new Date().toISOString().split('T')[0];
    document.getElementById('keluarUkpModal').style.display = 'block';
}

function closeKeluarUkpModal() {
    document.getElementById('keluarUkpModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('keluarUkpModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeKeluarUkpModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeKeluarUkpModal();
    }
});
</script>

@endsection