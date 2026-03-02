{{-- resources/views/admin/dashboard/_jadwal-kegiatan.blade.php --}}
<div class="content-box" style="margin-bottom:16px;">
    <h4 style="margin:0 0 12px;font-size:.88rem;font-weight:700;color:var(--text-color);display:flex;align-items:center;gap:8px;">
        <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:linear-gradient(135deg,var(--primary-color),var(--primary-dark));border-radius:6px;flex-shrink:0;">
            <i class="fas fa-calendar-day" style="font-size:.7rem;color:#fff;"></i>
        </span>
        Jadwal Kegiatan — {{ $hari }}
    </h4>

    @if($kegiatan->isEmpty())
        <div class="empty-state" style="padding:20px;">
            <i class="fas fa-calendar-times"></i>
            <p>Tidak ada kegiatan terjadwal hari ini.</p>
        </div>
    @else
    <div class="table-responsive" style="overflow-x:auto;">
        <table class="data-table" style="margin-top:0;">
            <thead>
                <tr>
                    <th>Kegiatan</th>
                    <th>Kategori</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kegiatan as $k)
                <tr class="{{ $k->belum_input ? 'row-danger' : '' }}">
                    <td>
                        <strong style="font-size:.82rem;">{{ $k->nama_kegiatan }}</strong>
                        @if($k->belum_input)
                            <span class="badge badge-danger badge-sm" style="display:inline-flex;margin-left:6px;animation:slideInDown .4s;">
                                <i class="fas fa-exclamation-triangle"></i> Belum input absensi!
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $k->kategori->nama_kategori ?? '-' }}</span>
                    </td>
                    <td style="font-size:.78rem;font-weight:600;white-space:nowrap;color:var(--text-color);">
                        {{ is_string($k->waktu_mulai) ? $k->waktu_mulai : $k->waktu_mulai->format('H:i') }}
                        <span style="color:var(--text-light);margin:0 2px;">–</span>
                        {{ is_string($k->waktu_selesai) ? $k->waktu_selesai : $k->waktu_selesai->format('H:i') }}
                    </td>
                    <td>
                        @if($k->status_kegiatan === 'berlangsung')
                            <span class="badge badge-success" style="animation:slideInDown .5s;">
                                <i class="fas fa-circle" style="font-size:.45rem;"></i> Berlangsung
                            </span>
                        @elseif($k->status_kegiatan === 'selesai')
                            <span class="badge badge-primary">
                                <i class="fas fa-check"></i> Selesai
                            </span>
                        @else
                            <span class="badge badge-secondary">
                                <i class="fas fa-clock"></i> Belum Mulai
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($k->total_absensi > 0)
                            <div class="progress-bar-wrap">
                                <div class="progress-bar-fill" style="width:{{ $k->persen_kehadiran }}%;"></div>
                            </div>
                            <small style="font-size:.68rem;color:var(--text-light);">
                                {{ $k->persen_kehadiran }}%
                                <span style="color:#bbb;">({{ $k->total_absensi }} data)</span>
                            </small>
                        @else
                            <small class="text-muted">—</small>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>