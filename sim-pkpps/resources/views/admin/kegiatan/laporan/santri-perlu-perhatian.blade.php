@extends('layouts.app')

@section('content')
<style>
.text-center { text-align: center; }
.progress-inline { display: flex; align-items: center; gap: 8px; }
.progress-bar-mini { flex: 1; background: #e9ecef; border-radius: 8px; height: 8px; overflow: hidden; max-width: 120px; }
.progress-bar-mini .fill { height: 100%; border-radius: 8px; }
</style>

<div class="page-header">
    <h2><i class="fas fa-user-clock"></i> Santri Perlu Perhatian</h2>
    <a href="{{ route('admin.laporan-kegiatan.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="content-box" style="margin-bottom: 14px;">
    <p style="margin:0; color:var(--text-light); font-size:0.85rem;">
        <i class="fas fa-info-circle"></i> Daftar santri dengan kehadiran &lt;70% dalam periode <strong>{{ $periodeLabel }}</strong>
    </p>
</div>

{{-- Filter --}}
<div class="content-box" style="margin-bottom: 14px;">
    <form method="GET" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
        <input type="hidden" name="periode" value="{{ request('periode', 'bulan_ini') }}">
        <div class="form-group" style="margin:0;">
            <label style="font-size:0.82rem;">Filter Kelas</label>
            <select name="id_kelas" class="form-control" style="min-width:180px;">
                <option value="">-- Semua Kelas --</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ request('id_kelas') == $k->id ? 'selected' : '' }}>
                        {{ $k->kelompok->nama_kelompok ?? '' }} - {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
    </form>
</div>

<div class="content-box">
    @if($santris->count() > 0)
        <div class="table-wrapper">
        <table class="data-table" style="font-size:0.85rem;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Santri</th>
                    <th>Nama</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Hadir</th>
                    <th class="text-center">Alpa</th>
                    <th class="text-center">Izin</th>
                    <th class="text-center">Sakit</th>
                    <th class="text-center" style="min-width:180px;">% Kehadiran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($santris as $i => $s)
                    <tr>
                        <td>{{ $santris->firstItem() + $i }}</td>
                        <td><code>{{ $s->id_santri }}</code></td>
                        <td><strong>{{ $s->nama_lengkap }}</strong></td>
                        <td class="text-center">{{ $s->total }}</td>
                        <td class="text-center"><span class="badge badge-success">{{ $s->hadir }}</span></td>
                        <td class="text-center"><span class="badge badge-danger">{{ $s->alpa }}</span></td>
                        <td class="text-center"><span class="badge badge-warning">{{ $s->izin }}</span></td>
                        <td class="text-center"><span class="badge badge-info">{{ $s->sakit }}</span></td>
                        <td class="text-center">
                            <div class="progress-inline" style="justify-content:center;">
                                <div class="progress-bar-mini"><div class="fill" style="width:{{ $s->persen }}%; background:#EF4444;"></div></div>
                                <strong style="color:#EF4444;">{{ $s->persen }}%</strong>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.laporan-kegiatan.detail-santri', $s->id_santri) }}" class="btn btn-sm btn-info" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        <div style="margin-top:16px;">
            {{ $santris->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-check-circle" style="color:#10B981;"></i>
            <h3>Alhamdulillah!</h3>
            <p>Tidak ada santri dengan kehadiran di bawah 70%.</p>
        </div>
    @endif
</div>
@endsection
