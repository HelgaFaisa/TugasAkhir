{{-- resources/views/admin/kepulangan/over-limit.blade.php --}}

@extends('layouts.app')

@section('title', 'Santri Over Limit Kuota')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-exclamation-triangle"></i> Santri Over Limit Kuota</h2>
</div>

{{-- Info Periode --}}
<div style="background: linear-gradient(135deg, #ff5252 0%, #f48fb1 100%); color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: center;">
        <div>
            <h4 style="margin: 0 0 5px 0; opacity: 0.9;">⚠️ Total Santri Over Limit</h4>
            <p style="margin: 0; font-size: 2rem; font-weight: 700;">{{ $santriList->count() }}</p>
        </div>
        <div>
            <h4 style="margin: 0 0 5px 0; opacity: 0.9;">📅 Periode Kuota</h4>
            <p style="margin: 0; font-size: 1.1rem; font-weight: 600;">
                {{ $settings->periode_mulai->format('d M Y') }} - {{ $settings->periode_akhir->format('d M Y') }}
            </p>
        </div>
        <div>
            <h4 style="margin: 0 0 5px 0; opacity: 0.9;">📊 Kuota Maksimal</h4>
            <p style="margin: 0; font-size: 1.1rem; font-weight: 600;">{{ $settings->kuota_maksimal }} Hari / Tahun</p>
        </div>
        <div style="text-align: right;">
            <a href="{{ route('admin.kepulangan.index') }}" class="btn btn-light" style="background: white; color: #dc3545; font-weight: 600;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

{{-- Alert Info --}}
<div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ffc107;">
    <strong>ℹ️ Informasi:</strong>
    <p style="margin: 10px 0 0 0;">
        Berikut adalah daftar santri yang telah melebihi kuota maksimal <strong>{{ $settings->kuota_maksimal }} hari</strong> dalam periode ini. 
        Santri tetap bisa mengajukan izin, namun akan mendapat peringatan visual.
    </p>
</div>

<div class="content-box">
    @if($santriList->count() > 0)
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Santri</th>
                        <th>Nama Santri</th>
                        <th>Kelas</th>
                        <th>Total Hari Terpakai</th>
                        <th>Kuota Maksimal</th>
                        <th>Kelebihan</th>
                        <th>Persentase</th>
                        <th>Jumlah Izin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($santriList as $index => $santri)
                        @php
                            $kelebihan = $santri->total_hari_izin - $settings->kuota_maksimal;
                        @endphp
                        <tr style="background-color: rgba(220, 53, 69, 0.05);">
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $santri->id_santri }}</strong></td>
                            <td>
                                <div>
                                    <strong>{{ $santri->nama_lengkap }}</strong><br>
                                    <small style="color: #7F8C8D;">NIS: {{ $santri->nis ?? '-' }}</small>
                                </div>
                            </td>
                            <td>{{ $santri->kelas }}</td>
                            <td>
                                <span style="display: inline-block; background: #dc3545; color: white; padding: 6px 12px; border-radius: 6px; font-size: 1rem; font-weight: 700;">
                                    {{ $santri->total_hari_izin }} hari
                                </span>
                            </td>
                            <td>
                                <span style="color: #7F8C8D;">{{ $settings->kuota_maksimal }} hari</span>
                            </td>
                            <td>
                                <span style="display: inline-block; background: #721c24; color: white; padding: 4px 10px; border-radius: 4px; font-size: 0.9rem; font-weight: 600;">
                                    +{{ $kelebihan }} hari
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="flex: 1; height: 20px; background: #ffebee; border-radius: 10px; overflow: hidden; position: relative;">
                                        <div style="height: 100%; width: {{ min(200, $santri->kuota_info['persentase']) }}%; background: linear-gradient(90deg, #dc3545, #c62828); transition: width 0.3s ease; display: flex; align-items: center; justify-content: center;">
                                            <span style="font-size: 0.75rem; font-weight: 600; color: white; z-index: 1;">
                                                {{ $santri->kuota_info['persentase'] }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="display: inline-block; background: #6c757d; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">
                                    {{ $santri->kepulangan->count() }} kali izin
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                    <button type="button" 
                                            class="btn btn-sm btn-primary" 
                                            onclick="showDetailSantri('{{ $santri->id_santri }}')"
                                            title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-warning" 
                                            onclick="resetKuotaSantri('{{ $santri->id_santri }}', '{{ $santri->nama_lengkap }}')"
                                            title="Reset Kuota">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Summary Statistics --}}
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h4 style="margin: 0 0 15px 0; color: #2C3E50;">📊 Ringkasan Statistik</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div style="text-align: center; padding: 15px; background: white; border-radius: 8px; border: 2px solid #dc3545;">
                    <div style="font-size: 0.85rem; color: #7F8C8D; margin-bottom: 5px;">Total Santri Over Limit</div>
                    <div style="font-size: 2rem; font-weight: 700; color: #dc3545;">{{ $santriList->count() }}</div>
                </div>
                <div style="text-align: center; padding: 15px; background: white; border-radius: 8px; border: 2px solid #ff9800;">
                    <div style="font-size: 0.85rem; color: #7F8C8D; margin-bottom: 5px;">Rata-rata Kelebihan</div>
                    <div style="font-size: 2rem; font-weight: 700; color: #ff9800;">
                        {{ round($santriList->avg(function($s) use ($settings) { return $s->total_hari_izin - $settings->kuota_maksimal; }), 1) }} hari
                    </div>
                </div>
                <div style="text-align: center; padding: 15px; background: white; border-radius: 8px; border: 2px solid #f44336;">
                    <div style="font-size: 0.85rem; color: #7F8C8D; margin-bottom: 5px;">Tertinggi</div>
                    <div style="font-size: 2rem; font-weight: 700; color: #f44336;">
                        {{ $santriList->max('total_hari_izin') }} hari
                    </div>
                </div>
                <div style="text-align: center; padding: 15px; background: white; border-radius: 8px; border: 2px solid #9c27b0;">
                    <div style="font-size: 0.85rem; color: #7F8C8D; margin-bottom: 5px;">Total Kelebihan</div>
                    <div style="font-size: 2rem; font-weight: 700; color: #9c27b0;">
                        {{ $santriList->sum(function($s) use ($settings) { return $s->total_hari_izin - $settings->kuota_maksimal; }) }} hari
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" class="btn btn-danger" onclick="showResetSemuaOverLimit()">
                <i class="fas fa-sync-alt"></i> Reset Semua Santri Over Limit
            </button>
            <a href="{{ route('admin.kepulangan.settings') }}" class="btn btn-primary">
                <i class="fas fa-cog"></i> Pengaturan Kuota
            </a>
        </div>
    @else
        <div style="text-align: center; padding: 60px; color: #28a745;">
            <i class="fas fa-check-circle" style="font-size: 4rem; margin-bottom: 20px; display: block;"></i>
            <h3 style="margin: 0 0 10px 0;">Tidak Ada Santri Over Limit!</h3>
            <p style="color: #7F8C8D;">Semua santri masih dalam batas kuota yang ditentukan.</p>
            <a href="{{ route('admin.kepulangan.index') }}" class="btn btn-primary" style="margin-top: 20px;">
                <i class="fas fa-arrow-left"></i> Kembali ke Kepulangan
            </a>
        </div>
    @endif
</div>

{{-- Modal Detail Santri --}}
<div class="modal fade" id="detailSantriModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 20px;">
            <div style="margin-bottom: 20px;">
                <h3 style="margin: 0; color: #2C3E50;">Detail Riwayat Izin Santri</h3>
            </div>
            <div id="detailSantriContent">
                <div style="text-align: center; padding: 40px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem;"></i>
                    <p style="margin-top: 10px;">Memuat data...</p>
                </div>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('detailSantriModal')">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Reset Kuota --}}
<div class="modal fade" id="resetKuotaModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 20px;">
            <form id="resetKuotaForm">
                @csrf
                <div style="margin-bottom: 20px;">
                    <h3 style="margin: 0; color: #2C3E50;">Reset Kuota Santri</h3>
                </div>
                <div style="padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; margin-bottom: 15px;">
                    <p style="margin: 0; color: #856404;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Anda akan mereset kuota untuk santri: <strong id="resetSantriName"></strong>
                    </p>
                </div>
                <div class="form-group">
                    <label>Catatan Reset (Opsional):</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Alasan reset kuota..."></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('resetKuotaModal')">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Reset Kuota</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.modal.fade { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
.modal-dialog { max-width: 500px; width: 90%; margin: auto; }
.modal-dialog.modal-lg { max-width: 800px; }
.modal-content { max-height: 90vh; overflow-y: auto; }
</style>

<script>
let currentResetSantriId = null;

function showDetailSantri(idSantri) {
    document.getElementById('detailSantriModal').style.display = 'flex';
    
    // Load detail via AJAX
    fetch(`/admin/api/kepulangan/santri/${idSantri}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const santri = data.santri;
                const kuota = data.penggunaan_izin;
                
                document.getElementById('detailSantriContent').innerHTML = `
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h4 style="margin: 0 0 15px 0;">${santri.nama_lengkap}</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                            <div>
                                <div style="font-size: 0.85rem; color: #7F8C8D;">ID Santri</div>
                                <div style="font-weight: 600;">${santri.id_santri}</div>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; color: #7F8C8D;">Kelas</div>
                                <div style="font-weight: 600;">${santri.kelas}</div>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; color: #7F8C8D;">Total Terpakai</div>
                                <div style="font-weight: 600; color: #dc3545;">${kuota.total_terpakai} hari</div>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; color: #7F8C8D;">Persentase</div>
                                <div style="font-weight: 600; color: #dc3545;">${kuota.persentase}%</div>
                            </div>
                        </div>
                    </div>
                    <div style="padding: 15px; background: #ffebee; border-radius: 8px;">
                        <p style="margin: 0; color: #c62828;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Over Limit!</strong> Santri ini telah melebihi kuota maksimal ${kuota.kuota_maksimal} hari.
                        </p>
                    </div>
                `;
            } else {
                document.getElementById('detailSantriContent').innerHTML = `
                    <div class="alert alert-danger">${data.message}</div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('detailSantriContent').innerHTML = `
                <div class="alert alert-danger">Error: ${error.message}</div>
            `;
        });
}

function resetKuotaSantri(idSantri, namaSantri) {
    currentResetSantriId = idSantri;
    document.getElementById('resetSantriName').textContent = namaSantri;
    document.getElementById('resetKuotaModal').style.display = 'flex';
}

document.getElementById('resetKuotaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mereset...';
    
    fetch(`/admin/kepulangan/reset/santri/${currentResetSantriId}`, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('resetKuotaModal');
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => showAlert('danger', 'Error: ' + error.message))
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

function showResetSemuaOverLimit() {
    if (confirm('Apakah Anda yakin ingin mereset kuota SEMUA santri yang over limit ({{ $santriList->count() }} santri)?')) {
        window.location.href = '{{ route("admin.kepulangan.settings") }}#reset-section';
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i> ${message}`;
    const pageHeader = document.querySelector('.page-header');
    pageHeader.insertAdjacentElement('afterend', alertDiv);
    setTimeout(() => alertDiv.remove(), 5000);
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.fade').forEach(modal => modal.style.display = 'none');
    }
});

document.querySelectorAll('.modal.fade').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});
</script>
@endsection