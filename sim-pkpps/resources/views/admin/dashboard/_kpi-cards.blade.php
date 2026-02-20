{{-- KPI Cards --}}
<div class="row-cards row-cards-5">
    <div class="card card-info">
        <h3>Santri Aktif</h3>
        <p class="card-value">{{ $kpi['totalSantriAktif'] }}</p>
        <i class="fas fa-user-graduate card-icon"></i>
    </div>

    <div class="card {{ $kpi['belumAbsensi'] > 0 ? 'card-warning' : 'card-success' }}">
        <h3>Kegiatan Hari Ini</h3>
        <p class="card-value">{{ $kpi['totalKegiatan'] }}</p>
        <span class="card-sub">{{ $kpi['sudahAbsensi'] }} sudah absen &middot; {{ $kpi['belumAbsensi'] }} belum</span>
        <i class="fas fa-calendar-check card-icon"></i>
    </div>

    <div class="card {{ $kpi['santriSakit'] > 0 ? 'card-danger' : 'card-success' }}">
        <h3>Santri di UKP</h3>
        <p class="card-value">{{ $kpi['santriSakit'] }}</p>
        <span class="card-sub">sedang dirawat</span>
        <i class="fas fa-briefcase-medical card-icon"></i>
    </div>

    <div class="card {{ $kpi['kepulanganMenunggu'] > 0 ? 'card-warning' : 'card-success' }}">
        <h3>Menunggu Approval</h3>
        <p class="card-value">{{ $kpi['kepulanganMenunggu'] }}</p>
        <span class="card-sub">pengajuan kepulangan</span>
        <i class="fas fa-clock card-icon"></i>
    </div>

    <div class="card {{ $kpi['santriTanpaWali'] > 0 ? 'card-secondary' : 'card-success' }}">
        <h3>Belum Ada Akun Wali</h3>
        <p class="card-value">{{ $kpi['santriTanpaWali'] }}</p>
        <span class="card-sub">santri tanpa wali mobile</span>
        <i class="fas fa-user-plus card-icon"></i>
    </div>
</div>
