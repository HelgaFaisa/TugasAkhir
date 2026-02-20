{{-- Ringkasan SPP Bulan Ini --}}
<div class="content-box dash-chart-box">
    <h4><i class="fas fa-wallet"></i> SPP Bulan Ini</h4>

    @php
        $total = $spp['lunas'] + $spp['belum'];
        $persenLunas = $total > 0 ? round(($spp['lunas'] / $total) * 100) : 0;
    @endphp

    <div class="chart-container chart-container-sm">
        <canvas id="sppDonutChart"></canvas>
    </div>

    <div class="spp-summary">
        <div class="spp-stat">
            <span class="spp-label">Lunas</span>
            <strong class="text-success">{{ $spp['lunas'] }} santri ({{ $persenLunas }}%)</strong>
        </div>
        <div class="spp-stat">
            <span class="spp-label">Belum Lunas</span>
            <strong class="text-danger">{{ $spp['belum'] }} santri</strong>
        </div>
        <div class="spp-stat">
            <span class="spp-label">Terkumpul</span>
            <strong>Rp {{ number_format($spp['terkumpul'], 0, ',', '.') }}</strong>
        </div>
        <div class="spp-stat">
            <span class="spp-label">Total Tagihan</span>
            <strong>Rp {{ number_format($spp['totalTagihan'], 0, ',', '.') }}</strong>
        </div>
    </div>
</div>
