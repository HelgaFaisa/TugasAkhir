@extends('layouts.app')

@section('title', 'Detail Riwayat Pelanggaran')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-eye"></i> Detail Riwayat Pelanggaran</h2>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="content-box">
    <div style="margin-bottom: 30px;">
        <h3 style="color: var(--primary-color); margin-bottom: 15px;">Informasi Riwayat</h3>
        
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="width: 200px; padding: 10px 0; font-weight: 600;">ID Riwayat</td>
                <td style="padding: 10px 0;">
                    <span class="badge badge-secondary" style="font-size: 1em;">{{ $riwayatPelanggaran->id_riwayat }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Tanggal</td>
                <td style="padding: 10px 0;">{{ $riwayatPelanggaran->tanggal_format }}</td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Santri</td>
                <td style="padding: 10px 0;">
                    @if($riwayatPelanggaran->santri)
                        <strong>{{ $riwayatPelanggaran->santri->nama_lengkap }}</strong><br>
                        <small style="color: var(--text-light);">{{ $riwayatPelanggaran->id_santri }}</small>
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Klasifikasi</td>
                <td style="padding: 10px 0;">
                    @if($riwayatPelanggaran->kategori && $riwayatPelanggaran->kategori->klasifikasi)
                        <span class="badge badge-info">{{ $riwayatPelanggaran->kategori->klasifikasi->nama_klasifikasi }}</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Kategori Pelanggaran</td>
                <td style="padding: 10px 0;">
                    @if($riwayatPelanggaran->kategori)
                        <strong>{{ $riwayatPelanggaran->kategori->nama_pelanggaran }}</strong><br>
                        <small style="color: var(--text-light);">{{ $riwayatPelanggaran->id_kategori }}</small>
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Poin Asli</td>
                <td style="padding: 10px 0;">
                    <span class="badge badge-danger" style="font-size: 1em;">{{ $riwayatPelanggaran->poin_asli }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Poin Saat Ini</td>
                <td style="padding: 10px 0;">
                    <span class="badge badge-danger" style="font-size: 1.1em; padding: 8px 12px;">
                        <i class="fas fa-fire"></i> {{ $riwayatPelanggaran->poin }}
                    </span>
                    @if($riwayatPelanggaran->is_kafaroh_selesai)
                        <small style="color: var(--success-color); margin-left: 10px;">
                            <i class="fas fa-check-circle"></i> Poin telah dilebur
                        </small>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600; vertical-align: top;">Kafaroh</td>
                <td style="padding: 10px 0;">
                    @if($riwayatPelanggaran->kategori && $riwayatPelanggaran->kategori->kafaroh)
                        <div style="background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid var(--warning-color);">
                            {{ $riwayatPelanggaran->kategori->kafaroh }}
                        </div>
                    @else
                        <span style="color: var(--text-light);">Tidak ada kafaroh</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Status Kafaroh</td>
                <td style="padding: 10px 0;">
                    @if($riwayatPelanggaran->is_kafaroh_selesai)
                        <span class="badge badge-success">Selesai</span><br>
                        <small style="color: var(--text-light);">
                            Diselesaikan: {{ $riwayatPelanggaran->tanggal_kafaroh_selesai ? $riwayatPelanggaran->tanggal_kafaroh_selesai->format('d M Y H:i') : '-' }}<br>
                            Oleh: {{ $riwayatPelanggaran->adminKafaroh->name ?? '-' }}
                        </small>
                    @else
                        <span class="badge badge-warning">Belum Selesai</span>
                    @endif
                </td>
            </tr>
            @if($riwayatPelanggaran->catatan_kafaroh)
                <tr>
                    <td style="padding: 10px 0; font-weight: 600; vertical-align: top;">Catatan Kafaroh</td>
                    <td style="padding: 10px 0;">
                        <div style="background: #d1ecf1; padding: 15px; border-radius: 8px;">
                            {{ $riwayatPelanggaran->catatan_kafaroh }}
                        </div>
                    </td>
                </tr>
            @endif
            <tr>
                <td style="padding: 10px 0; font-weight: 600;">Status Publish ke Wali</td>
                <td style="padding: 10px 0;">
                    @if($riwayatPelanggaran->is_published_to_parent)
                        <span class="badge badge-success">Terkirim</span><br>
                        <small style="color: var(--text-light);">
                            Dikirim: {{ $riwayatPelanggaran->tanggal_published ? $riwayatPelanggaran->tanggal_published->format('d M Y H:i') : '-' }}<br>
                            Oleh: {{ $riwayatPelanggaran->adminPublished->name ?? '-' }}
                        </small>
                    @else
                        <span class="badge badge-secondary">Belum Terkirim</span>
                    @endif
                </td>
            </tr>
            @if($riwayatPelanggaran->keterangan)
                <tr>
                    <td style="padding: 10px 0; font-weight: 600; vertical-align: top;">Keterangan</td>
                    <td style="padding: 10px 0;">{{ $riwayatPelanggaran->keterangan }}</td>
                </tr>
            @endif
        </table>
    </div>

    <!-- Tombol Aksi -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h4 style="margin-bottom: 15px; color: var(--primary-color);">
            <i class="fas fa-cogs"></i> Aksi
        </h4>
        
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            @if(!$riwayatPelanggaran->is_kafaroh_selesai && $riwayatPelanggaran->kategori && $riwayatPelanggaran->kategori->kafaroh)
                <button type="button" 
                        class="btn btn-success"
                        onclick="document.getElementById('modal-kafaroh').style.display='block'">
                    <i class="fas fa-check-circle"></i> Selesaikan Kafaroh
                </button>
            @endif

            @if(!$riwayatPelanggaran->is_published_to_parent)
                <form action="{{ route('admin.riwayat-pelanggaran.publish-to-parent', $riwayatPelanggaran) }}" 
                      method="POST" 
                      style="display: inline;"
                      onsubmit="return confirm('Yakin ingin mengirim pelanggaran ini ke wali santri?');">
                    @csrf
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-paper-plane"></i> Kirim ke Wali Santri
                    </button>
                </form>
            @else
                <form action="{{ route('admin.riwayat-pelanggaran.unpublish-from-parent', $riwayatPelanggaran) }}" 
                      method="POST" 
                      style="display: inline;"
                      onsubmit="return confirm('Yakin ingin membatalkan pengiriman ke wali santri?');">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-undo"></i> Batalkan Kirim ke Wali
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Riwayat Lainnya -->
    @if($riwayatLainnya->isNotEmpty())
        <h3 style="color: var(--primary-color); margin-bottom: 15px;">
            <i class="fas fa-list"></i> Riwayat Pelanggaran Lainnya (Santri yang Sama)
        </h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 100px;">ID</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Pelanggaran</th>
                    <th style="width: 80px; text-align: center;">Poin</th>
                    <th style="width: 120px; text-align: center;">Status Kafaroh</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayatLainnya as $item)
                    <tr>
                        <td><span class="badge badge-secondary">{{ $item->id_riwayat }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td>{{ $item->kategori->nama_pelanggaran ?? '-' }}</td>
                        <td style="text-align: center;">
                            <span class="badge badge-danger">{{ $item->poin }}</span>
                        </td>
                        <td style="text-align: center;">
                            @if($item->is_kafaroh_selesai)
                                <span class="badge badge-success">Selesai</span>
                            @else
                                <span class="badge badge-warning">Belum</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="btn-group" style="margin-top: 30px;">
        <a href="{{ route('admin.riwayat-pelanggaran.edit', $riwayatPelanggaran) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('admin.riwayat-pelanggaran.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<!-- Modal Selesaikan Kafaroh -->
<div id="modal-kafaroh" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 10% auto; padding: 30px; border-radius: 10px; width: 90%; max-width: 500px;">
        <h3 style="margin-bottom: 20px; color: var(--primary-color);">
            <i class="fas fa-check-circle"></i> Selesaikan Kafaroh
        </h3>
        
        <form action="{{ route('admin.riwayat-pelanggaran.selesaikan-kafaroh', $riwayatPelanggaran) }}" method="POST">
            @csrf
            
            <p style="margin-bottom: 20px; color: var(--text-color);">
                Dengan menyelesaikan kafaroh, poin pelanggaran akan <strong>dilebur menjadi 0</strong>.
            </p>
            
            <div class="form-group">
                <label for="catatan_kafaroh">Catatan (Opsional)</label>
                <textarea name="catatan_kafaroh" 
                          id="catatan_kafaroh"
                          class="form-control"
                          rows="4"
                          placeholder="Contoh: Santri telah menyelesaikan kafaroh dengan baik..."></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <button type="button" 
                        class="btn btn-secondary"
                        onclick="document.getElementById('modal-kafaroh').style.display='none'">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('modal-kafaroh');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// Auto hide alerts
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>
@endsection