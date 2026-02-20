{{-- Alert Panel --}}
@if($alerts['santriAlpaBeruntun']->isNotEmpty() || $alerts['sppJatuhTempo']->isNotEmpty() || $alerts['kepulanganPending']->isNotEmpty())
<div class="content-section">
    <h3><i class="fas fa-exclamation-circle"></i> Peringatan & Tindak Lanjut</h3>
    <div class="dash-alerts">

        {{-- Santri Alpa Beruntun --}}
        @if($alerts['santriAlpaBeruntun']->isNotEmpty())
        <div class="alert alert-danger">
            <div class="alert-body">
                <strong><i class="fas fa-user-times"></i> Santri Alpa Beruntun (7 Hari Terakhir)</strong>
                <ul class="alert-list">
                    @foreach($alerts['santriAlpaBeruntun'] as $s)
                    <li>{{ $s->nama }} <span class="badge badge-danger badge-sm">{{ $s->total_alpa }}x alpa</span></li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- SPP Jatuh Tempo --}}
        @if($alerts['sppJatuhTempo']->isNotEmpty())
        <div class="alert alert-warning">
            <div class="alert-body">
                <strong><i class="fas fa-file-invoice-dollar"></i> SPP Jatuh Tempo</strong>
                <ul class="alert-list">
                    @foreach($alerts['sppJatuhTempo'] as $s)
                    <li>
                        {{ $s->santri->nama_lengkap ?? '-' }}
                        — Bln {{ $s->bulan }}/{{ $s->tahun }}
                        <small>(jatuh tempo {{ $s->batas_bayar->translatedFormat('d M Y') }})</small>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- Pengajuan Kepulangan Pending --}}
        @if($alerts['kepulanganPending']->isNotEmpty())
        <div class="alert alert-info">
            <div class="alert-body">
                <strong><i class="fas fa-home"></i> Pengajuan Kepulangan Menunggu Review</strong>
                <ul class="alert-list">
                    @foreach($alerts['kepulanganPending'] as $k)
                    <li>
                        {{ $k->santri->nama_lengkap ?? '-' }}
                        — {{ $k->tanggal_pulang->translatedFormat('d M') }} s.d {{ $k->tanggal_kembali->translatedFormat('d M Y') }}
                        <small>({{ $k->alasan }})</small>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

    </div>
</div>
@endif
