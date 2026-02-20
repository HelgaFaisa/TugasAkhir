{{-- Jadwal Kegiatan Hari Ini --}}
<div class="content-section">
    <h3><i class="fas fa-list-alt"></i> Jadwal Kegiatan — {{ $hari }}</h3>
    <div class="content-box">
        @if($kegiatan->isEmpty())
            <p class="text-muted">Tidak ada kegiatan terjadwal hari ini.</p>
        @else
            <div class="table-responsive">
                <table class="data-table">
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
                                <strong>{{ $k->nama_kegiatan }}</strong>
                                @if($k->belum_input)
                                    <span class="badge badge-danger badge-sm">Belum input absensi!</span>
                                @endif
                            </td>
                            <td>{{ $k->kategori->nama_kategori ?? '-' }}</td>
                            <td>
                                {{ is_string($k->waktu_mulai) ? $k->waktu_mulai : $k->waktu_mulai->format('H:i') }}
                                —
                                {{ is_string($k->waktu_selesai) ? $k->waktu_selesai : $k->waktu_selesai->format('H:i') }}
                            </td>
                            <td>
                                @if($k->status_kegiatan === 'berlangsung')
                                    <span class="badge badge-info">Berlangsung</span>
                                @elseif($k->status_kegiatan === 'selesai')
                                    <span class="badge badge-success">Selesai</span>
                                @else
                                    <span class="badge badge-secondary">Belum Mulai</span>
                                @endif
                            </td>
                            <td>
                                @if($k->total_absensi > 0)
                                    <div class="progress-bar-wrap">
                                        <div class="progress-bar-fill" style="width: {{ $k->persen_kehadiran }}%"></div>
                                    </div>
                                    <small>{{ $k->persen_kehadiran }}% ({{ $k->total_absensi }} data)</small>
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
</div>
