@extends('layouts.app', ['isAdmin' => true])

@section('title', 'Detail Santri: ' . $santri->nama_lengkap)

@section('content')
<div class="page-header">
    <h2><i class="fas fa-user"></i> Detail Santri</h2>
</div>

<div class="content-box">
    <div class="detail-header">
        <div>
            <h3>{{ $santri->nama_lengkap }}</h3>
            <p style="color: #7F8C8D; margin: 5px 0 0 0;">
                <i class="fas fa-id-badge"></i> {{ $santri->id_santri }}
                @if($santri->nis)
                    | <i class="fas fa-barcode"></i> NIS: {{ $santri->nis }}
                @endif
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.santri.edit', $santri) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.santri.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <hr style="border: none; border-top: 2px solid #E8F7F2; margin: 25px 0;">

    <div class="detail-section">
        <h4><i class="fas fa-id-card"></i> Informasi Dasar</h4>
        <table class="detail-table">
            <tr>
                <th width="200">ID Santri</th>
                <td><strong>{{ $santri->id_santri }}</strong></td>
            </tr>
            <tr>
                <th>NIS</th>
                <td>{{ $santri->nis ?? '-' }}</td>
            </tr>
            <tr>
                <th>Nama Lengkap</th>
                <td><strong>{{ $santri->nama_lengkap }}</strong></td>
            </tr>
            <tr>
                <th>Jenis Kelamin</th>
                <td>
                    @if($santri->jenis_kelamin == 'Laki-laki')
                        <i class="fas fa-mars" style="color: #81C6E8;"></i> {{ $santri->jenis_kelamin }}
                    @else
                        <i class="fas fa-venus" style="color: #FF8B94;"></i> {{ $santri->jenis_kelamin }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Kelas</th>
                <td>
                    <strong style="color: #6FBA9D; font-size: 1.1rem;">{{ $santri->kelas }}</strong>
                    @if($santri->kelas == 'PB')
                        <span style="color: #7F8C8D; font-size: 0.85rem;">(Pembinaan)</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($santri->status == 'Aktif')
                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); color: #2C5F4F; display: inline-block;">
                            <i class="fas fa-check-circle"></i> {{ $santri->status }}
                        </span>
                    @elseif($santri->status == 'Lulus')
                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; background: linear-gradient(135deg, #E3F2FD 0%, #D1E9F9 100%); color: #2D4A7C; display: inline-block;">
                            <i class="fas fa-graduation-cap"></i> {{ $santri->status }}
                        </span>
                    @else
                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; background: linear-gradient(135deg, #E8ECF0 0%, #D1D8E0 100%); color: #555; display: inline-block;">
                            <i class="fas fa-times-circle"></i> {{ $santri->status }}
                        </span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-map-marker-alt"></i> Alamat & Asal</h4>
        <table class="detail-table">
            <tr>
                <th width="200">Alamat Santri</th>
                <td>{{ $santri->alamat_santri ?? '-' }}</td>
            </tr>
            <tr>
                <th>Daerah Asal</th>
                <td>
                    @if($santri->daerah_asal)
                        <i class="fas fa-map-pin" style="color: #6FBA9D;"></i> {{ $santri->daerah_asal }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-users"></i> Data Orang Tua / Wali</h4>
        <table class="detail-table">
            <tr>
                <th width="200">Nama Orang Tua</th>
                <td>{{ $santri->nama_orang_tua ?? '-' }}</td>
            </tr>
            <tr>
                <th>Nomor HP Orang Tua</th>
                <td>
                    @if($santri->nomor_hp_ortu)
                        <i class="fas fa-phone" style="color: #6FBA9D;"></i> 
                        <a href="tel:{{ $santri->nomor_hp_ortu }}" style="color: #6FBA9D; text-decoration: none;">
                            {{ $santri->nomor_hp_ortu }}
                        </a>
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="detail-section">
        <h4><i class="fas fa-clock"></i> Informasi Sistem</h4>
        <table class="detail-table">
            <tr>
                <th width="200">Tanggal Dibuat</th>
                <td>
                    <i class="fas fa-calendar-plus" style="color: #81C6E8;"></i> 
                    {{ $santri->created_at->format('d M Y, H:i') }} WIB
                    <span style="color: #7F8C8D; font-size: 0.85rem;">
                        ({{ $santri->created_at->diffForHumans() }})
                    </span>
                </td>
            </tr>
            <tr>
                <th>Terakhir Diupdate</th>
                <td>
                    <i class="fas fa-calendar-check" style="color: #FFD56B;"></i> 
                    {{ $santri->updated_at->format('d M Y, H:i') }} WIB
                    <span style="color: #7F8C8D; font-size: 0.85rem;">
                        ({{ $santri->updated_at->diffForHumans() }})
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #F8FBF9 0%, #E8F7F2 100%); border-radius: 8px; border-left: 4px solid #6FBA9D;">
        <p style="margin: 0; color: #2C5F4F; font-size: 0.9rem;">
            <i class="fas fa-info-circle"></i> 
            <strong>Informasi:</strong> Data santri ini dapat diedit atau dihapus melalui halaman index atau menggunakan tombol Edit di atas.
        </p>
    </div>
</div>
@endsection