{{-- resources/views/santri/kesehatan/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Kesehatan')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-heartbeat"></i> Riwayat Kesehatan</h2>
    <p style="margin: 4px 0 0 0; color: var(--text-light);">
        Kunjungan UKP — <strong>{{ $santri->nama_lengkap }}</strong>
    </p>
</div>

{{-- ⚠️ ALERT: SEDANG DIRAWAT SEKARANG --}}
@if($sedangDirawatSekarang)
<div style="
    background: linear-gradient(135deg, #E74C3C, #C0392B);
    color: white; padding: 16px 20px; border-radius: 12px;
    margin-bottom: 18px; display: flex; align-items: center; gap: 14px;
    box-shadow: 0 4px 18px rgba(231,76,60,0.4); animation: alertPulse 2.5s infinite;">
    <div style="background: rgba(255,255,255,0.2); width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        <i class="fas fa-procedures" style="font-size: 1.5rem;"></i>
    </div>
    <div style="flex: 1; min-width: 0;">
        <strong style="font-size: 1.05rem; display: block;">⚠️ Kamu Sedang Dalam Perawatan UKP</strong>
        <span style="font-size: 0.85rem; opacity: 0.9; display: block; margin-top: 2px;">
            Masuk sejak {{ $sedangDirawatSekarang->tanggal_masuk->locale('id')->isoFormat('D MMMM Y') }}
            &bull; Hari ke-{{ $sedangDirawatSekarang->lama_dirawat }}
            &bull; {{ Str::limit($sedangDirawatSekarang->keluhan, 55) }}
        </span>
    </div>
    <a href="{{ route('santri.kesehatan.show', $sedangDirawatSekarang->id) }}"
       style="background: rgba(255,255,255,0.2); color: white; padding: 8px 14px; border-radius: 8px;
              text-decoration: none; font-size: 0.82rem; white-space: nowrap; border: 1px solid rgba(255,255,255,0.35); flex-shrink: 0;"
       onmouseover="this.style.background='rgba(255,255,255,0.35)';"
       onmouseout="this.style.background='rgba(255,255,255,0.2)';">
        <i class="fas fa-eye"></i> Lihat Detail
    </a>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}
</div>
@endif

{{-- ── STATISTIK PERIODE ── --}}
<div class="row-cards">
    <div class="card card-info">
        <h3><i class="fas fa-notes-medical"></i> Total Kunjungan</h3>
        <div class="card-value">{{ $statistik['total_kunjungan'] }}</div>
        <div class="card-icon"><i class="fas fa-notes-medical"></i></div>
        <p style="margin-top: 8px; font-size: 0.82rem; color: var(--text-light);">Periode dipilih</p>
    </div>
    <div class="card card-danger">
        <h3><i class="fas fa-procedures"></i> Sedang Dirawat</h3>
        <div class="card-value">{{ $statistik['sedang_dirawat'] }}</div>
        <div class="card-icon"><i class="fas fa-procedures"></i></div>
        <p style="margin-top: 8px; font-size: 0.82rem; color: {{ $statistik['sedang_dirawat'] > 0 ? 'var(--danger-color)' : 'var(--text-light)' }};">
            {{ $statistik['sedang_dirawat'] > 0 ? '⚠️ Perlu perhatian' : 'Tidak ada' }}
        </p>
    </div>
    <div class="card card-success">
        <h3><i class="fas fa-check-circle"></i> Sembuh</h3>
        <div class="card-value">{{ $statistik['sembuh'] }}</div>
        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
        <p style="margin-top: 8px; font-size: 0.82rem; color: var(--text-light);">Alhamdulillah</p>
    </div>
    <div class="card card-warning">
        <h3><i class="fas fa-home"></i> Izin Sakit</h3>
        <div class="card-value">{{ $statistik['izin'] }}</div>
        <div class="card-icon"><i class="fas fa-home"></i></div>
        <p style="margin-top: 8px; font-size: 0.82rem; color: var(--text-light);">Izin pulang</p>
    </div>
</div>

{{-- ── ALL-TIME SUMMARY ── --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
    <div style="background: linear-gradient(135deg, #E8F7F2, #D4F1E3); padding: 16px; border-radius: 12px; border-left: 4px solid #6FBA9D; display: flex; align-items: center; gap: 14px;">
        <i class="fas fa-history" style="font-size: 2rem; color: #6FBA9D; flex-shrink: 0;"></i>
        <div>
            <p style="margin: 0; font-size: 0.82rem; color: var(--text-light);">Total Kunjungan Semua Waktu</p>
            <p style="margin: 0; font-size: 1.7rem; font-weight: 700; color: #2C5F4F; line-height: 1.1;">{{ $totalAllTime }} <span style="font-size: 1rem;">kali</span></p>
        </div>
    </div>
    <div style="background: linear-gradient(135deg, #FFF8E1, #FFF3CD); padding: 16px; border-radius: 12px; border-left: 4px solid #FFD56B; display: flex; align-items: center; gap: 14px;">
        <i class="fas fa-bed" style="font-size: 2rem; color: #F39C12; flex-shrink: 0;"></i>
        <div>
            <p style="margin: 0; font-size: 0.82rem; color: var(--text-light);">Total Hari Dirawat Semua Waktu</p>
            <p style="margin: 0; font-size: 1.7rem; font-weight: 700; color: #7D5A00; line-height: 1.1;">{{ $totalHariDirawat }} <span style="font-size: 1rem;">hari</span></p>
        </div>
    </div>
</div>

{{-- ── GRAFIK 6 BULAN ── --}}
@if($dataGrafik->count() > 0)
<div class="content-box" style="margin-bottom: 14px;">
    <h3 style="margin: 0 0 16px 0; color: var(--primary-color);">
        <i class="fas fa-chart-bar"></i> Kunjungan 6 Bulan Terakhir
        <span style="font-size: 0.78rem; font-weight: 400; color: var(--text-light); margin-left: 6px;">per status</span>
    </h3>
    <canvas id="chartKesehatan" style="max-height: 240px;"></canvas>
</div>
@endif

{{-- ── FILTER ── --}}
<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" action="{{ route('santri.kesehatan.index') }}" id="filterForm">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 11px; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 6px;">
                    <i class="fas fa-calendar-alt form-icon"></i> Tanggal Dari
                </label>
                <input type="date" name="tanggal_dari" class="form-control"
                       value="{{ $tanggalDari->format('Y-m-d') }}" max="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 6px;">
                    <i class="fas fa-calendar-check form-icon"></i> Tanggal Sampai
                </label>
                <input type="date" name="tanggal_sampai" class="form-control"
                       value="{{ $tanggalSampai->format('Y-m-d') }}" max="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 6px;">
                    <i class="fas fa-filter form-icon"></i> Status
                </label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary hover-lift">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('santri.kesehatan.index') }}" class="btn btn-secondary hover-lift">
                    <i class="fas fa-sync"></i>
                </a>
            </div>
        </div>
    </form>
    <p style="margin: 11px 0 0 0; color: var(--text-light); font-size: 0.82rem;">
        <i class="fas fa-info-circle"></i>
        Periode: <strong style="color: var(--primary-color);">
            {{ $tanggalDari->locale('id')->isoFormat('D MMMM Y') }} — {{ $tanggalSampai->locale('id')->isoFormat('D MMMM Y') }}
        </strong>
        ({{ $tanggalDari->diffInDays($tanggalSampai) + 1 }} hari)
    </p>
</div>

{{-- ── DAFTAR RIWAYAT ── --}}
@if($riwayatKesehatan->isEmpty())
    <div class="empty-state" style="margin-top: 14px;">
        <i class="fas fa-notes-medical"></i>
        <h3>Tidak Ada Data</h3>
        <p>Tidak ada riwayat kesehatan pada periode yang dipilih.</p>
        <a href="{{ route('santri.kesehatan.index') }}" class="btn btn-primary" style="margin-top: 14px;">
            <i class="fas fa-sync"></i> Lihat Semua Data
        </a>
    </div>
@else
    <div class="content-box" style="margin-top: 14px;">
        <h3 style="margin: 0 0 14px 0; color: var(--primary-color);">
            <i class="fas fa-list"></i> Daftar Riwayat
            <span style="color: var(--text-light); font-weight: 400; font-size: 0.9rem;">({{ $riwayatKesehatan->total() }} data)</span>
        </h3>

        <div style="display: flex; flex-direction: column; gap: 10px;">
            @foreach($riwayatKesehatan as $item)
            @php
                $bColor = match($item->status) { 'dirawat' => '#E74C3C', 'sembuh' => '#6FBA9D', default => '#F39C12' };
                $iBg    = match($item->status) { 'dirawat' => 'linear-gradient(135deg,#FFE8EA,#FFD5D8)', 'sembuh' => 'linear-gradient(135deg,#E8F7F2,#D4F1E3)', default => 'linear-gradient(135deg,#FFF8E1,#FFF3CD)' };
                $ico    = match($item->status) { 'dirawat' => 'fa-procedures', 'sembuh' => 'fa-check-circle', default => 'fa-home' };
            @endphp
            <a href="{{ route('santri.kesehatan.show', $item->id) }}"
               style="display:flex; gap:14px; padding:14px 16px; background:white; border-radius:10px; border-left:4px solid {{ $bColor }}; text-decoration:none; box-shadow:0 2px 8px rgba(0,0,0,0.06); transition:all 0.2s;"
               onmouseover="this.style.transform='translateX(5px)'; this.style.boxShadow='0 4px 16px rgba(0,0,0,0.12)';"
               onmouseout="this.style.transform=''; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.06)';">

                <div style="flex-shrink:0; width:54px; height:54px; border-radius:50%; background:{{ $iBg }}; display:flex; align-items:center; justify-content:center;">
                    <i class="fas {{ $ico }}" style="font-size:1.5rem; color:{{ $bColor }};"></i>
                </div>

                <div style="flex:1; min-width:0;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:5px;">
                        <strong style="color:var(--text-color); font-size:0.95rem;">{{ Str::limit($item->keluhan, 65) }}</strong>
                        <span class="badge badge-{{ $item->status_badge_color }}" style="margin-left:8px; flex-shrink:0;">{{ ucfirst($item->status) }}</span>
                    </div>
                    <div style="display:flex; flex-wrap:wrap; gap:10px; font-size:0.8rem; color:var(--text-light);">
                        <span><i class="fas fa-calendar-plus"></i> {{ $item->tanggal_masuk_formatted }}</span>
                        @if($item->tanggal_keluar)
                            <span><i class="fas fa-calendar-check"></i> Keluar: {{ $item->tanggal_keluar_formatted }}</span>
                        @endif
                        @if($item->status === 'dirawat')
                            <span class="badge badge-danger badge-sm"><i class="fas fa-clock"></i> Hari ke-{{ $item->lama_dirawat }}</span>
                        @else
                            <span class="badge badge-info badge-sm"><i class="fas fa-clock"></i> {{ $item->lama_dirawat }} hari</span>
                        @endif
                        <span style="color:#ccc; font-size:0.75rem;">{{ $item->id_kesehatan }}</span>
                    </div>
                </div>

                <div style="flex-shrink:0; align-self:center;">
                    <i class="fas fa-chevron-right" style="color:var(--text-light); font-size:0.8rem;"></i>
                </div>
            </a>
            @endforeach
        </div>

        <div style="margin-top: 18px;">
            {{ $riwayatKesehatan->links() }}
        </div>
    </div>
@endif

<div class="info-box" style="margin-top: 14px;">
    <i class="fas fa-info-circle"></i>
    <strong>Info:</strong> Default menampilkan data bulan berjalan. Gunakan filter untuk melihat periode lain.
</div>

{{-- Chart.js --}}
@if($dataGrafik->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const dataGrafik = @json($dataGrafik);
new Chart(document.getElementById('chartKesehatan').getContext('2d'), {
    type: 'bar',
    data: {
        labels: dataGrafik.map(d => d.label),
        datasets: [
            { label: 'Sembuh',  data: dataGrafik.map(d => d.sembuh),  backgroundColor: 'rgba(111,186,157,0.85)', borderRadius: 5, borderSkipped: false },
            { label: 'Izin',    data: dataGrafik.map(d => d.izin),    backgroundColor: 'rgba(255,213,107,0.85)', borderRadius: 5, borderSkipped: false },
            { label: 'Dirawat', data: dataGrafik.map(d => d.dirawat), backgroundColor: 'rgba(231,76,60,0.85)',   borderRadius: 5, borderSkipped: false },
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: { afterBody: items => `Total: ${dataGrafik[items[0].dataIndex].total} kunjungan` }
            }
        },
        scales: {
            x: { stacked: true, grid: { display: false } },
            y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dari   = document.querySelector('input[name="tanggal_dari"]');
    const sampai = document.querySelector('input[name="tanggal_sampai"]');
    dari.addEventListener('change', () => { if (sampai.value && sampai.value < dari.value) sampai.value = dari.value; });
    sampai.addEventListener('change', function() {
        if (dari.value && this.value < dari.value) {
            alert('Tanggal sampai tidak boleh lebih kecil dari tanggal dari!');
            this.value = dari.value;
        }
    });
});
</script>

<style>
@keyframes alertPulse {
    0%, 100% { box-shadow: 0 4px 18px rgba(231,76,60,0.4); }
    50%       { box-shadow: 0 4px 28px rgba(231,76,60,0.75); }
}
</style>
@endsection