@extends('layouts.app')

@section('title', 'Preview Kenaikan Kelas')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-users"></i> Preview Kenaikan Kelas - {{ $kelas->nama_kelas }}</h2>
    <p class="text-muted">Pilih santri yang akan dinaikkan kelasnya</p>
</div>

<!-- Flash Messages -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- Info Cards -->
<div class="content-box" style="margin-bottom: 20px;">
    <div class="row-cards">
        <div class="card card-info">
            <h3>Kelas Asal</h3>
            <div class="card-value-small">{{ $kelas->nama_kelas }}</div>
            <p class="text-muted" style="margin: 0;">{{ $kelas->kelompok->nama_kelompok }}</p>
        </div>
        <div class="card card-success">
            <h3>Total Santri</h3>
            <div class="card-value">{{ $santriList->count() }}</div>
            <p class="text-muted" style="margin: 0;">Santri aktif di kelas ini</p>
        </div>
        <div class="card card-warning">
            <h3>Tahun Ajaran</h3>
            <div class="card-value-small">{{ $tahunAjaranAktif }}</div>
            <div style="margin-top: 8px; font-size: 0.9rem; color: var(--text-light);">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="card-value-small" style="color: var(--success-color);">{{ $tahunAjaranBaru }}</div>
        </div>
    </div>
</div>

<!-- Form Kenaikan Kelas -->
<div class="content-box">
    <form action="{{ route('admin.kelas.kenaikan.process-selected') }}" method="POST" id="formKenaikanKelas">
        @csrf
        
        <input type="hidden" name="id_kelas_asal" value="{{ $kelas->id }}">
        
        <!-- Pilih Kelas Tujuan -->
        <div class="form-group">
            <label for="id_kelas_tujuan">
                <i class="fas fa-graduation-cap"></i> Kelas Tujuan <span class="text-danger">*</span>
            </label>
            <select class="form-control @error('id_kelas_tujuan') is-invalid @enderror" 
                    id="id_kelas_tujuan" 
                    name="id_kelas_tujuan" 
                    required>
                <option value="">-- Pilih Kelas Tujuan --</option>
                @foreach ($kelasOptions as $kelompok)
                    <optgroup label="{{ $kelompok->nama_kelompok }}">
                        @foreach ($kelompok->kelas as $kelasOption)
                            @if ($kelasOption->id != $kelas->id)
                                <option value="{{ $kelasOption->id }}">
                                    {{ $kelasOption->nama_kelas }}
                                </option>
                            @endif
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error('id_kelas_tujuan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Pilih kelas yang akan menjadi tujuan kenaikan untuk santri yang dipilih
            </small>
        </div>

        <hr>

        <!-- Daftar Santri -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h4 style="margin: 0;">
                <i class="fas fa-users"></i> Daftar Santri
                <span class="badge badge-info" id="selectedCount">0 dipilih</span>
            </h4>
            <div>
                <button type="button" class="btn btn-sm btn-info" id="btnSelectAll">
                    <i class="fas fa-check-square"></i> Pilih Semua
                </button>
                <button type="button" class="btn btn-sm btn-secondary" id="btnDeselectAll">
                    <i class="fas fa-square"></i> Batal Pilih
                </button>
            </div>
        </div>

        @if ($santriList->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="checkAll" style="width: 18px; height: 18px; cursor: pointer;">
                        </th>
                        <th style="width: 50px;">No</th>
                        <th>Foto</th>
                        <th>NIS</th>
                        <th>Nama Santri</th>
                        <th>Jenis Kelamin</th>
                        <th>Status</th>
                    </tr>
                </thead>
                    <tbody>
                        @foreach ($santriList as $index => $santri)
                            <tr>
                                <td>
                                    <input type="checkbox" 
                                           name="santri_ids[]" 
                                           value="{{ $santri->id_santri }}" 
                                           class="santri-checkbox"
                                           style="width: 18px; height: 18px; cursor: pointer;">
                                </td>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if ($santri->foto)
                                        <img src="{{ $santri->foto_url }}" 
                                             alt="{{ $santri->nama_lengkap }}" 
                                             class="santri-avatar">
                                    @else
                                        <div class="santri-avatar-initial">
                                            {{ strtoupper(substr($santri->nama_lengkap, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $santri->nis ?? '-' }}</td>
                                <td><strong>{{ $santri->nama_lengkap }}</strong></td>
                                <td>
                                    @if ($santri->jenis_kelamin === 'Laki-laki')
                                        <i class="fas fa-mars text-primary"></i> Laki-laki
                                    @else
                                        <i class="fas fa-venus" style="color: #FF8B94;"></i> Perempuan
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> {{ $santri->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            <hr>

            <!-- Action Buttons -->
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <strong>Perhatian:</strong>
                <ul class="mb-0 mt-2">
                    <li>Pastikan kelas tujuan sudah dipilih</li>
                    <li>Pilih santri yang akan dinaikkan kelasnya (minimal 1 santri)</li>
                    <li>Proses kenaikan kelas akan memindahkan santri ke tahun ajaran <strong>{{ $tahunAjaranBaru }}</strong></li>
                    <li>Santri yang dipilih akan otomatis terdaftar di kelas tujuan</li>
                </ul>
            </div>

            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="submit" class="btn btn-success" id="btnSubmit" disabled>
                    <i class="fas fa-arrow-up"></i> Naikkan Kelas yang Dipilih
                </button>
                <a href="{{ route('admin.kelas.kenaikan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada santri aktif di kelas ini</h5>
                <p class="text-muted">Kelas {{ $kelas->nama_kelas }} tidak memiliki santri aktif.</p>
                <a href="{{ route('admin.kelas.kenaikan.index') }}" class="btn btn-secondary mt-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        @endif
    </form>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.santri-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnSelectAll = document.getElementById('btnSelectAll');
    const btnDeselectAll = document.getElementById('btnDeselectAll');
    const kelasSelect = document.getElementById('id_kelas_tujuan');
    
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.santri-checkbox:checked').length;
        selectedCount.textContent = `${checked} dipilih`;
        
        // Enable submit button if kelas tujuan selected and at least 1 santri checked
        if (kelasSelect.value && checked > 0) {
            btnSubmit.disabled = false;
        } else {
            btnSubmit.disabled = true;
        }
    }
    
    // Check all functionality
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }
    
    // Individual checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Update check all status
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            if (checkAll) {
                checkAll.checked = allChecked;
            }
            updateSelectedCount();
        });
    });
    
    // Select all button
    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', function() {
            checkboxes.forEach(checkbox => checkbox.checked = true);
            if (checkAll) checkAll.checked = true;
            updateSelectedCount();
        });
    }
    
    // Deselect all button
    if (btnDeselectAll) {
        btnDeselectAll.addEventListener('click', function() {
            checkboxes.forEach(checkbox => checkbox.checked = false);
            if (checkAll) checkAll.checked = false;
            updateSelectedCount();
        });
    }
    
    // Kelas tujuan change
    if (kelasSelect) {
        kelasSelect.addEventListener('change', updateSelectedCount);
    }
    
    // Form submit confirmation
    const form = document.getElementById('formKenaikanKelas');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.santri-checkbox:checked').length;
            const kelasText = kelasSelect.options[kelasSelect.selectedIndex].text;
            
            if (!confirm(`Apakah Anda yakin ingin menaikkan ${checked} santri ke kelas "${kelasText}"?\n\nProses ini tidak dapat dibatalkan.`)) {
                e.preventDefault();
            }
        });
    }
    
    // Initial count
    updateSelectedCount();
});
</script>
@endsection