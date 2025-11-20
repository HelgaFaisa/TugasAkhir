@extends('layouts.app')

@section('title', 'Detail Kesehatan Santri')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-file-medical-alt"></i> Detail Kesehatan Santri</h2>
</div>

<!-- Header Actions -->
<div class="content-box" style="margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 style="margin: 0; color: var(--primary-color);">
                <i class="fas fa-info-circle"></i> ID Kesehatan: <span style="color: var(--text-color);">{{ $kesehatanSantri->id_kesehatan }}</span>
            </h3>
            <p style="margin: 5px 0 0 0; color: #7F8C8D; font-size: 0.9em;">
                Terakhir diupdate: {{ $kesehatanSantri->updated_at->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB
            </p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            @if($kesehatanSantri->status == 'dirawat')
                <button type="button" 
                        class="btn btn-success" 
                        onclick="keluarUkp({{ $kesehatanSantri->id }}, '{{ $kesehatanSantri->santri->nama_lengkap }}', '{{ $kesehatanSantri->tanggal_masuk->format('Y-m-d') }}')">
                    <i class="fas fa-sign-out-alt"></i> Keluar UKP
                </button>
            @endif
            <a href="{{ route('admin.kesehatan-santri.edit', $kesehatanSantri) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.kesehatan-santri.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- Status Card dengan Badge Besar -->
<div class="content-box" style="background: linear-gradient(135deg, {{ $kesehatanSantri->status == 'dirawat' ? '#FFE8EA' : ($kesehatanSantri->status == 'sembuh' ? '#E8F7F2' : '#FFF8E1') }} 0%, {{ $kesehatanSantri->status == 'dirawat' ? '#FFD5D8' : ($kesehatanSantri->status == 'sembuh' ? '#D4F1E3' : '#FFF3CD') }} 100%); border-left: 5px solid {{ $kesehatanSantri->status == 'dirawat' ? '#E74C3C' : ($kesehatanSantri->status == 'sembuh' ? '#6FBA9D' : '#FFD56B') }}; margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <h3 style="margin: 0 0 10px 0; color: var(--text-color); font-size: 1.3em;">
                <i class="fas fa-{{ $kesehatanSantri->status == 'dirawat' ? 'bed' : ($kesehatanSantri->status == 'sembuh' ? 'check-circle' : 'home') }}"></i>
                Status Kesehatan
            </h3>
            <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                <span style="background: {{ $kesehatanSantri->status == 'dirawat' ? '#E74C3C' : ($kesehatanSantri->status == 'sembuh' ? '#6FBA9D' : '#FFD56B') }}; color: white; padding: 12px 24px; border-radius: 8px; font-size: 1.2em; font-weight: bold; box-shadow: 0 4px 8px rgba(0,0,0,0.15);">
                    {{ strtoupper($kesehatanSantri->status) }}
                </span>
                <div style="border-left: 3px solid {{ $kesehatanSantri->status == 'dirawat' ? '#E74C3C' : ($kesehatanSantri->status == 'sembuh' ? '#6FBA9D' : '#FFD56B') }}; padding-left: 15px;">
                    <p style="margin: 0; font-size: 0.9em; color: #7F8C8D;">Lama Dirawat</p>
                    <p style="margin: 0; font-size: 2em; font-weight: bold; color: {{ $kesehatanSantri->status == 'dirawat' ? '#E74C3C' : ($kesehatanSantri->status == 'sembuh' ? '#6FBA9D' : '#FFD56B') }};">
                        {{ $kesehatanSantri->lama_dirawat }} <span style="font-size: 0.5em;">Hari</span>
                    </p>
                </div>
            </div>
        </div>
        @if($kesehatanSantri->status != 'dirawat')
            <div style="text-align: center; padding: 15px; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <i class="fas fa-check-double" style="font-size: 2.5em; color: var(--success-color); margin-bottom: 5px;"></i>
                <p style="margin: 0; font-weight: bold; color: var(--text-color);">Selesai Perawatan</p>
            </div>
        @endif
    </div>
</div>

<!-- Grid Layout untuk Info Santri dan Tanggal -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-bottom: 25px;">
    
    <!-- Card: Informasi Santri -->
    <div class="content-box" style="border-top: 4px solid var(--primary-color);">
        <h3 style="color: var(--primary-color); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-user-circle" style="font-size: 1.5em;"></i>
            <span>Informasi Santri</span>
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <div class="info-item">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                    <i class="fas fa-id-badge" style="color: var(--primary-color); width: 20px;"></i>
                    <span style="color: #7F8C8D; font-size: 0.85em; font-weight: 600;">ID SANTRI</span>
                </div>
                <div style="font-size: 1.3em; font-weight: bold; color: var(--primary-color); margin-left: 30px;">
                    {{ $kesehatanSantri->santri->id_santri }}
                </div>
            </div>
            
            <div class="info-item">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                    <i class="fas fa-user" style="color: var(--primary-color); width: 20px;"></i>
                    <span style="color: #7F8C8D; font-size: 0.85em; font-weight: 600;">NAMA LENGKAP</span>
                </div>
                <div style="font-size: 1.2em; font-weight: bold; color: var(--text-color); margin-left: 30px;">
                    {{ $kesehatanSantri->santri->nama_lengkap }}
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; padding-top: 15px; border-top: 2px solid var(--primary-light);">
                <div class="info-item">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                        <i class="fas fa-id-card" style="color: var(--primary-color); font-size: 0.9em;"></i>
                        <span style="color: #7F8C8D; font-size: 0.8em;">NIS</span>
                    </div>
                    <div style="font-weight: 600; color: var(--text-color);">{{ $kesehatanSantri->santri->nis ?: '-' }}</div>
                </div>
                
                <div class="info-item">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                        <i class="fas fa-school" style="color: var(--primary-color); font-size: 0.9em;"></i>
                        <span style="color: #7F8C8D; font-size: 0.8em;">Kelas</span>
                    </div>
                    <div style="font-weight: 600; color: var(--text-color);">{{ $kesehatanSantri->santri->kelas_lengkap }}</div>
                </div>
                
                <div class="info-item">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                        <i class="fas fa-venus-mars" style="color: var(--primary-color); font-size: 0.9em;"></i>
                        <span style="color: #7F8C8D; font-size: 0.8em;">Jenis Kelamin</span>
                    </div>
                    <div style="font-weight: 600; color: var(--text-color);">{{ $kesehatanSantri->santri->jenis_kelamin }}</div>
                </div>
                
                <div class="info-item">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                        <i class="fas fa-info-circle" style="color: var(--primary-color); font-size: 0.9em;"></i>
                        <span style="color: #7F8C8D; font-size: 0.8em;">Status</span>
                    </div>
                    <div>{!! $kesehatanSantri->santri->status_badge !!}</div>
                </div>
            </div>
            
            <div style="padding-top: 15px; border-top: 2px solid var(--primary-light);">
                <div class="info-item">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                        <i class="fas fa-user-friends" style="color: var(--primary-color);"></i>
                        <span style="color: #7F8C8D; font-size: 0.85em;">Orang Tua / Wali</span>
                    </div>
                    <div style="font-weight: 600; color: var(--text-color);">{{ $kesehatanSantri->santri->nama_orang_tua ?: '-' }}</div>
                </div>
                
                <div class="info-item" style="margin-top: 10px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                        <i class="fas fa-phone" style="color: var(--primary-color);"></i>
                        <span style="color: #7F8C8D; font-size: 0.85em;">Nomor HP</span>
                    </div>
                    <div style="font-weight: 600; color: var(--text-color);">
                        @if($kesehatanSantri->santri->nomor_hp_ortu)
                            <a href="tel:{{ $kesehatanSantri->santri->nomor_hp_ortu }}" style="color: var(--primary-color); text-decoration: none;">
                                <i class="fas fa-phone-alt"></i> {{ $kesehatanSantri->santri->nomor_hp_ortu }}
                            </a>
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card: Timeline Perawatan -->
    <div class="content-box" style="border-top: 4px solid var(--secondary-color);">
        <h3 style="color: var(--secondary-color); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-calendar-alt" style="font-size: 1.5em;"></i>
            <span>Timeline Perawatan</span>
        </h3>
        
        <div style="position: relative; padding-left: 30px;">
            <!-- Timeline Line -->
            <div style="position: absolute; left: 8px; top: 0; bottom: 0; width: 3px; background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);"></div>
            
            <!-- Tanggal Masuk -->
            <div style="position: relative; margin-bottom: 30px;">
                <div style="position: absolute; left: -26px; width: 20px; height: 20px; background: var(--primary-color); border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.15);"></div>
                <div style="background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%); padding: 15px; border-radius: 8px; border-left: 4px solid var(--primary-color);">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                        <i class="fas fa-calendar-plus" style="color: var(--primary-color);"></i>
                        <span style="font-weight: 600; color: #7F8C8D; font-size: 0.85em;">MASUK UKP</span>
                    </div>
                    <div style="font-size: 1.3em; font-weight: bold; color: var(--primary-color); margin-bottom: 5px;">
                        {{ $kesehatanSantri->tanggal_masuk_formatted }}
                    </div>
                    <div style="color: #7F8C8D; font-size: 0.9em;">
                        {{ $kesehatanSantri->tanggal_masuk->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </div>
                    <div style="color: #7F8C8D; font-size: 0.85em; margin-top: 5px;">
                        <i class="fas fa-clock"></i> {{ $kesehatanSantri->tanggal_masuk->diffForHumans() }}
                    </div>
                </div>
            </div>
            
            <!-- Tanggal Keluar -->
            <div style="position: relative;">
                <div style="position: absolute; left: -26px; width: 20px; height: 20px; background: {{ $kesehatanSantri->tanggal_keluar ? 'var(--secondary-color)' : '#E0E0E0' }}; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.15);"></div>
                <div style="background: {{ $kesehatanSantri->tanggal_keluar ? 'linear-gradient(135deg, #FFE8EA 0%, #FFD5D8 100%)' : 'linear-gradient(135deg, #F5F5F5 0%, #E0E0E0 100%)' }}; padding: 15px; border-radius: 8px; border-left: 4px solid {{ $kesehatanSantri->tanggal_keluar ? 'var(--secondary-color)' : '#E0E0E0' }};">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                        <i class="fas fa-{{ $kesehatanSantri->tanggal_keluar ? 'calendar-check' : 'hourglass-half' }}" style="color: {{ $kesehatanSantri->tanggal_keluar ? 'var(--secondary-color)' : '#999' }};"></i>
                        <span style="font-weight: 600; color: #7F8C8D; font-size: 0.85em;">KELUAR UKP</span>
                    </div>
                    @if($kesehatanSantri->tanggal_keluar)
                        <div style="font-size: 1.3em; font-weight: bold; color: var(--secondary-color); margin-bottom: 5px;">
                            {{ $kesehatanSantri->tanggal_keluar_formatted }}
                        </div>
                        <div style="color: #7F8C8D; font-size: 0.9em;">
                            {{ $kesehatanSantri->tanggal_keluar->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </div>
                        <div style="color: #7F8C8D; font-size: 0.85em; margin-top: 5px;">
                            <i class="fas fa-clock"></i> {{ $kesehatanSantri->tanggal_keluar->diffForHumans() }}
                        </div>
                    @else
                        <div style="font-size: 1.1em; font-weight: bold; color: #E74C3C;">
                            <i class="fas fa-exclamation-circle"></i> Belum Keluar
                        </div>
                        <div style="color: #7F8C8D; font-size: 0.9em; margin-top: 5px;">
                            Santri masih dalam perawatan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Card: Keluhan dan Catatan -->
<div class="content-box" style="border-top: 4px solid var(--warning-color); margin-bottom: 25px;">
    <h3 style="color: var(--warning-color); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-notes-medical" style="font-size: 1.5em;"></i>
        <span>Informasi Medis</span>
    </h3>
    
    <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
        <!-- Keluhan -->
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <div style="background: var(--primary-color); color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-stethoscope"></i>
                </div>
                <div>
                    <h4 style="margin: 0; color: var(--text-color); font-size: 1.1em;">Keluhan / Diagnosa</h4>
                    <p style="margin: 0; font-size: 0.85em; color: #7F8C8D;">Gejala yang dialami</p>
                </div>
            </div>
            <div style="background: linear-gradient(135deg, #E8F7F2 0%, #F8FBF9 100%); padding: 20px; border-radius: 8px; border-left: 4px solid var(--primary-color); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                <p style="margin: 0; line-height: 1.8; color: var(--text-color); font-size: 1.05em;">
                    {{ $kesehatanSantri->keluhan }}
                </p>
            </div>
        </div>
        
        <!-- Catatan -->
        @if($kesehatanSantri->catatan)
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <div style="background: var(--warning-color); color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h4 style="margin: 0; color: var(--text-color); font-size: 1.1em;">Catatan Petugas</h4>
                    <p style="margin: 0; font-size: 0.85em; color: #7F8C8D;">Informasi tambahan dari petugas UKP</p>
                </div>
            </div>
            <div style="background: linear-gradient(135deg, #FFF8E1 0%, #FFF3CD 100%); padding: 20px; border-radius: 8px; border-left: 4px solid var(--warning-color); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                <p style="margin: 0; line-height: 1.8; color: var(--text-color); font-size: 1.05em;">
                    {{ $kesehatanSantri->catatan }}
                </p>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Action Buttons (Fixed at Bottom) -->
<div class="content-box" style="background: linear-gradient(135deg, #F8FBF9 0%, #E8F7F2 100%); border-top: 3px solid var(--primary-color);">
    <h4 style="color: var(--primary-color); margin-bottom: 15px;">
        <i class="fas fa-tools"></i> Aksi Tersedia
    </h4>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('admin.kesehatan-santri.cetak-surat', $kesehatanSantri) }}" 
           class="btn btn-secondary" 
           target="_blank">
            <i class="fas fa-print"></i> Cetak Surat Izin
        </a>
        
        <a href="{{ route('admin.kesehatan-santri.riwayat', $kesehatanSantri->id_santri) }}" 
           class="btn btn-primary">
            <i class="fas fa-history"></i> Riwayat Kesehatan
        </a>
        
        <form action="{{ route('admin.kesehatan-santri.destroy', $kesehatanSantri) }}" 
              method="POST" 
              style="display: inline;"
              onsubmit="return confirm('⚠️ Yakin ingin menghapus data kesehatan ini?\n\nData yang dihapus tidak dapat dikembalikan!')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus Data
            </button>
        </form>
    </div>
</div>

<!-- Riwayat Kesehatan Santri (5 data terakhir) -->
@if($riwayatKesehatan->count() > 0)
<div class="content-box" style="margin-top: 30px; border-top: 4px solid var(--info-color);">
    <h3 style="color: var(--info-color); margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
        <span>
            <i class="fas fa-history"></i> Riwayat Kesehatan Lainnya
        </span>
        <span style="font-size: 0.8em; font-weight: normal; color: #7F8C8D;">
            {{ $riwayatKesehatan->count() }} data terakhir
        </span>
    </h3>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal Masuk</th>
                <th>Keluhan</th>
                <th>Tanggal Keluar</th>
                <th>Status</th>
                <th>Lama</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayatKesehatan as $riwayat)
            <tr>
                <td><strong>{{ $riwayat->id_kesehatan }}</strong></td>
                <td>
                    <strong>{{ $riwayat->tanggal_masuk_formatted }}</strong><br>
                    <small style="color: #7F8C8D;">{{ $riwayat->tanggal_masuk->format('D') }}</small>
                </td>
                <td>
                    <span title="{{ $riwayat->keluhan }}">
                        {{ Str::limit($riwayat->keluhan, 40) }}
                    </span>
                </td>
                <td>
                    @if($riwayat->tanggal_keluar)
                        <strong>{{ $riwayat->tanggal_keluar_formatted }}</strong>
                    @else
                        <span style="color: #E74C3C;">-</span>
                    @endif
                </td>
                <td class="text-center">
                    <span class="btn btn-{{ $riwayat->status_badge_color }} btn-sm" 
                          style="cursor: default; padding: 5px 10px;">
                        {{ ucfirst($riwayat->status) }}
                    </span>
                </td>
                <td class="text-center"><strong>{{ $riwayat->lama_dirawat }}</strong> hari</td>
                <td>
                    <a href="{{ route('admin.kesehatan-santri.show', $riwayat) }}" 
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('admin.kesehatan-santri.riwayat', $kesehatanSantri->id_santri) }}" 
           class="btn btn-primary">
            <i class="fas fa-list"></i> Lihat Semua Riwayat
        </a>
    </div>
</div>
@endif

<!-- Modal Keluar UKP -->
<div id="keluarUkpModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; animation: fadeIn 0.3s ease;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 12px; min-width: 400px; max-width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.3); animation: slideDown 0.3s ease;">
        <h3 style="margin-bottom: 20px; color: var(--primary-color); display: flex; align-items: center; gap: 10px;">
            <div style="background: var(--primary-color); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <span>Keluar dari UKP</span>
        </h3>
        
        <form id="keluarUkpForm" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="form-group">
                <label><i class="fas fa-user form-icon"></i>Nama Santri</label>
                <input type="text" id="modalNamaSantri" readonly class="form-control" style="background-color: #F8F9FA;">
            </div>
            
            <div class="form-group">
                <label for="tanggal_keluar"><i class="fas fa-calendar form-icon"></i>Tanggal Keluar *</label>
                <input type="date" name="tanggal_keluar" id="tanggal_keluar" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="status_keluar"><i class="fas fa-check-circle form-icon"></i>Status *</label>
                <select name="status" id="status_keluar" class="form-control" required>
                    <option value="">Pilih Status</option>
                    <option value="sembuh">Sembuh</option>
                    <option value="izin">Izin Pulang</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeKeluarUkpModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideDown {
    from { 
        opacity: 0;
        transform: translate(-50%, -60%);
    }
    to { 
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}

.info-item {
    transition: all 0.3s ease;
}

.info-item:hover {
    transform: translateX(5px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #keluarUkpModal > div {
        min-width: 90%;
        padding: 20px;
    }
    
    .page-header h2 {
        font-size: 1.3rem;
    }
}
</style>

<script>
function keluarUkp(id, namaSantri, tanggalMasuk) {
    document.getElementById('modalNamaSantri').value = namaSantri;
    document.getElementById('keluarUkpForm').action = `/admin/kesehatan-santri/${id}/keluar-ukp`;
    document.getElementById('tanggal_keluar').value = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_keluar').min = tanggalMasuk;
    document.getElementById('tanggal_keluar').max = new Date().toISOString().split('T')[0];
    document.getElementById('keluarUkpModal').style.display = 'block';
}

function closeKeluarUkpModal() {
    document.getElementById('keluarUkpModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('keluarUkpModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeKeluarUkpModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeKeluarUkpModal();
    }
});
</script>

@endsection