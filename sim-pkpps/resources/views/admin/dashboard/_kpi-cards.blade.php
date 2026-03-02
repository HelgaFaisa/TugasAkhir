{{-- resources/views/admin/dashboard/_kpi-cards.blade.php --}}
<div class="row-cards row-cards-5" style="margin-bottom:16px;">

    <div class="card card-info">
        <h3>Santri Aktif</h3>
        <div class="card-value">{{ $kpi['totalSantriAktif'] }}</div>
        <span class="card-sub">terdaftar &amp; aktif</span>
        <i class="fas fa-user-graduate card-icon"></i>
    </div>

    <div class="card {{ $kpi['belumAbsensi'] > 0 ? 'card-warning' : 'card-success' }}">
        <h3>Kegiatan Hari Ini</h3>
        <div class="card-value">{{ $kpi['totalKegiatan'] }}</div>
        <span class="card-sub">
            <span style="color:#27ae60;font-weight:700;">{{ $kpi['sudahAbsensi'] }} absen</span>
            &nbsp;·&nbsp;
            <span style="{{ $kpi['belumAbsensi'] > 0 ? 'color:#e67e22;font-weight:700;' : '' }}">{{ $kpi['belumAbsensi'] }} belum</span>
        </span>
        <i class="fas fa-calendar-check card-icon"></i>
    </div>

    <div class="card {{ $kpi['santriSakit'] > 0 ? 'card-danger' : 'card-success' }}">
        <h3>Santri di UKP</h3>
        <div class="card-value">{{ $kpi['santriSakit'] }}</div>
        <span class="card-sub">sedang dirawat</span>
        <i class="fas fa-briefcase-medical card-icon"></i>
    </div>

    <div class="card {{ $kpi['kepulanganMenunggu'] > 0 ? 'card-warning' : 'card-success' }}">
        <h3>Menunggu Approval</h3>
        <div class="card-value">{{ $kpi['kepulanganMenunggu'] }}</div>
        <span class="card-sub">pengajuan kepulangan</span>
        <i class="fas fa-clock card-icon"></i>
    </div>

    @if(auth()->user()->isSuperAdmin())
    <div class="card {{ $kpi['santriTanpaWali'] > 0 ? 'card-secondary' : 'card-success' }}">
        <h3>Belum Ada Akun Wali</h3>
        <div class="card-value">{{ $kpi['santriTanpaWali'] }}</div>
        <span class="card-sub">santri tanpa wali mobile</span>
        <i class="fas fa-user-plus card-icon"></i>
    </div>
    @endif

</div>