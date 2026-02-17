@extends('layouts.app')

@section('title', 'Kenaikan Kelas Massal')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
    <div>
        <h2><i class="fas fa-graduation-cap"></i> Kenaikan Kelas Massal</h2>
        <p class="text-muted" style="margin: 0;">Kelola kenaikan kelas santri per tahun ajaran</p>
    </div>
    <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali ke Kelola Kelas
    </a>
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

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- Info Card -->
<div class="content-box" style="margin-bottom: 20px;">
    <div class="row-cards">
        <div class="card card-info">
            <h3>Tahun Ajaran Aktif</h3>
            <div class="card-value">{{ $tahunAjaranAktif }}</div>
            <p class="text-muted" style="margin: 0;">Tahun ajaran saat ini</p>
        </div>
        <div class="card card-success">
            <h3>Total Santri Aktif</h3>
            <div class="card-value">{{ $totalSantriAktif }}</div>
            <p class="text-muted" style="margin: 0;">Santri dengan status aktif</p>
        </div>
        <div class="card card-warning">
            <h3>Tahun Ajaran Baru</h3>
            <div class="card-value" style="font-size: 1.8rem;">{{ $tahunAjaranBaru }}</div>
            <p class="text-muted" style="margin: 0;">Target kenaikan kelas</p>
        </div>
    </div>
</div>

<!-- Filter by Kelompok -->
<div class="content-box" style="margin-bottom: 20px;">
    <form method="GET" action="{{ route('admin.kelas.kenaikan.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
        <label style="margin: 0; font-weight: 600; color: var(--primary-dark);">
            <i class="fas fa-filter"></i> Pilih Kelompok Kelas:
        </label>
        <select name="kelompok" class="form-control" style="max-width: 300px;" onchange="this.form.submit()">
            @foreach ($kelompokKelas as $kelompok)
                <option value="{{ $kelompok->id_kelompok }}" 
                        {{ $selectedKelompok == $kelompok->id_kelompok ? 'selected' : '' }}>
                    {{ $kelompok->nama_kelompok }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary" style="display: none;">
            <i class="fas fa-filter"></i> Filter
        </button>
    </form>
</div>

<!-- Kenaikan Kelas Table -->
@if ($kelasList->isNotEmpty())
    @php
        $currentKelompok = $kelompokKelas->where('id_kelompok', $selectedKelompok)->first();
    @endphp
    
    <div class="content-box" style="margin-bottom: 25px;">
        <div style="background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: var(--shadow-sm);">
            <h3 style="margin: 0 0 8px 0; color: var(--primary-dark); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-layer-group"></i>
                {{ $currentKelompok->nama_kelompok ?? 'Kelas' }}
            </h3>
            <p style="margin: 0; color: var(--text-light); font-size: 0.95rem;">
                <i class="fas fa-info-circle"></i> 
                {{ $currentKelompok->deskripsi ?? 'Kelola kenaikan kelas untuk kelompok ini' }}
            </p>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kelas Asal</th>
                    <th style="width: 120px; text-align: center;">Total Santri</th>
                    <th style="width: 280px;">Naik ke Kelas</th>
                    <th style="width: 200px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kelasList as $index => $kelas)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong style="color: var(--primary-dark);">{{ $kelas->nama_kelas }}</strong>
                            <br>
                            <span class="text-muted" style="font-size: 0.85rem;">
                                <i class="fas fa-tag"></i> {{ $kelas->kode_kelas }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-info" style="font-size: 0.95rem; padding: 8px 14px;">
                                <i class="fas fa-users"></i> {{ $kelas->santri_aktif_count }} santri
                            </span>
                        </td>
                        <td>
                            <select class="form-control form-control-sm target-kelas-select" 
                                    data-kelas-id="{{ $kelas->id }}"
                                    style="font-size: 0.9rem;">
                                <option value="">-- Pilih Kelas Tujuan --</option>
                                @foreach ($allKelasList as $targetKelas)
                                    @if ($targetKelas->id != $kelas->id)
                                        <option value="{{ $targetKelas->id }}">
                                            {{ $targetKelas->kelompok->nama_kelompok }} - {{ $targetKelas->nama_kelas }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td style="text-align: center;">
                            @if ($kelas->santri_aktif_count > 0)
                                <div style="display: flex; gap: 5px; justify-content: center;">
                                    <button type="button" 
                                            class="btn btn-sm btn-success btn-naikkan"
                                            data-kelas-id="{{ $kelas->id }}"
                                            data-kelas-nama="{{ $kelas->nama_kelas }}"
                                            data-santri-count="{{ $kelas->santri_aktif_count }}"
                                            disabled
                                            title="Pilih kelas tujuan terlebih dahulu">
                                        <i class="fas fa-arrow-up"></i> Naikkan
                                    </button>
                                    <a href="{{ route('admin.kelas.kenaikan.preview', $kelas->id) }}" 
                                       class="btn btn-sm btn-info"
                                       title="Lihat & Pilih Santri">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </div>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-user-slash"></i> Tidak ada santri
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <!-- Empty State -->
    <div class="content-box">
        <div class="text-center py-5">
            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Tidak ada kelas ditemukan</h5>
            <p class="text-muted">
                Belum ada kelas aktif di kelompok yang dipilih.
            </p>
        </div>
    </div>
@endif

<!-- Info Alert -->
<div class="alert alert-info" style="border-left: 4px solid var(--info-color); background: #E3F7FC;">
    <div style="display: flex; gap: 15px;">
        <div style="font-size: 2rem; color: var(--info-color);">
            <i class="fas fa-info-circle"></i>
        </div>
        <div style="flex: 1;">
            <strong style="color: var(--primary-dark); font-size: 1.1rem;">Cara Menggunakan Kenaikan Kelas:</strong>
            <ol style="margin: 10px 0 0 0; padding-left: 20px; color: var(--text-color);">
                <li style="margin-bottom: 8px;">
                    <strong>Pilih Kelompok Kelas</strong> dari dropdown di atas untuk menampilkan daftar kelas
                </li>
                <li style="margin-bottom: 8px;">
                    <strong>Pilih Kelas Tujuan</strong> untuk setiap kelas yang akan dinaikkan (bisa ke kelompok manapun)
                </li>
                <li style="margin-bottom: 8px;">
                    Klik tombol <span class="badge badge-success"><i class="fas fa-arrow-up"></i> Naikkan</span> untuk memproses kenaikan kelas semua santri
                </li>
                <li style="margin-bottom: 8px;">
                    Atau klik tombol <span class="badge badge-info"><i class="fas fa-eye"></i> Lihat</span> untuk memilih santri secara individual
                </li>
                <li style="margin-bottom: 8px;">
                    Santri akan dipindahkan ke <strong>Tahun Ajaran {{ $tahunAjaranBaru }}</strong>
                </li>
            </ol>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enable/disable button based on target kelas selection
    const selects = document.querySelectorAll('.target-kelas-select');
    
    selects.forEach(select => {
        const kelasId = select.dataset.kelasId;
        const button = document.querySelector(`.btn-naikkan[data-kelas-id="${kelasId}"]`);
        
        if (!button) return;
        
        select.addEventListener('change', function() {
            if (this.value) {
                button.disabled = false;
                button.classList.remove('btn-secondary');
                button.classList.add('btn-success');
                button.title = 'Klik untuk menaikkan kelas semua santri';
            } else {
                button.disabled = true;
                button.classList.remove('btn-success');
                button.classList.add('btn-secondary');
                button.title = 'Pilih kelas tujuan terlebih dahulu';
            }
        });
    });
    
    // Handle kenaikan kelas button with improved UX
    document.querySelectorAll('.btn-naikkan').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const kelasId = this.dataset.kelasId;
            const kelasNama = this.dataset.kelasNama;
            const santriCount = this.dataset.santriCount;
            const select = document.querySelector(`.target-kelas-select[data-kelas-id="${kelasId}"]`);
            
            if (!select) {
                alert('Terjadi kesalahan: Dropdown kelas tidak ditemukan!');
                return;
            }
            
            const targetKelasId = select.value;
            const targetKelasText = select.options[select.selectedIndex].text;
            
            if (!targetKelasId) {
                // Show styled alert
                select.focus();
                select.style.border = '2px solid #FF8B94';
                setTimeout(() => {
                    select.style.border = '';
                }, 2000);
                alert('⚠️ Silakan pilih kelas tujuan terlebih dahulu!');
                return;
            }
            
            // Detailed confirmation
            const confirmMessage = `🎓 KONFIRMASI KENAIKAN KELAS\n\n` +
                `Kelas Asal: ${kelasNama}\n` +
                `Kelas Tujuan: ${targetKelasText}\n` +
                `Jumlah Santri: ${santriCount} orang\n` +
                `Tahun Ajaran Baru: {{ $tahunAjaranBaru }}\n\n` +
                `Apakah Anda yakin ingin melanjutkan?\n` +
                `Proses ini akan memindahkan SEMUA santri aktif ke kelas dan tahun ajaran baru.`;
            
            if (confirm(confirmMessage)) {
                // Disable button to prevent double click
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.kelas.kenaikan.process") }}';
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                
                const kelasInput = document.createElement('input');
                kelasInput.type = 'hidden';
                kelasInput.name = 'id_kelas_asal';
                kelasInput.value = kelasId;
                
                const targetInput = document.createElement('input');
                targetInput.type = 'hidden';
                targetInput.name = 'id_kelas_tujuan';
                targetInput.value = targetKelasId;
                
                form.appendChild(csrfInput);
                form.appendChild(kelasInput);
                form.appendChild(targetInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
    
    // Add hover effect to rows
    document.querySelectorAll('.data-table tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.transition = 'all 0.2s ease';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
@endsection