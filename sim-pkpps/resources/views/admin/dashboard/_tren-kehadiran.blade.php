{{-- resources/views/admin/dashboard/_tren-kehadiran.blade.php --}}
<div class="content-box" style="margin-bottom:16px;">
    <h4 style="margin:0 0 12px;font-size:.88rem;font-weight:700;color:var(--text-color);display:flex;align-items:center;gap:8px;">
        <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;
                     background:linear-gradient(135deg,var(--info-color),#5FAFE0);border-radius:6px;flex-shrink:0;">
            <i class="fas fa-chart-line" style="font-size:.7rem;color:#fff;"></i>
        </span>
        Tren Kehadiran — 4 Minggu Terakhir
    </h4>
    <div class="chart-container" style="height:220px;">
        <canvas id="trenKehadiranChart"></canvas>
    </div>
</div>