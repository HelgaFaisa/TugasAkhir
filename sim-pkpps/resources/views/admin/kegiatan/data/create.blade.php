{{-- views/admin/kegiatan/data/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Kegiatan Baru</h2>
</div>

<div class="form-container">
    <form action="{{ route('admin.kegiatan.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label><i class="fas fa-hashtag form-icon"></i> ID Kegiatan (Otomatis)</label>
            <input type="text" class="form-control" value="{{ $nextId }}" disabled>
            <small class="form-text">ID akan dibuat otomatis saat disimpan</small>
        </div>

        <div class="form-group">
            <label for="kategori_id">
                <i class="fas fa-list-alt form-icon"></i>
                Kategori Kegiatan <span style="color: red;">*</span>
            </label>
            <select name="kategori_id" id="kategori_id" class="form-control @error('kategori_id') is-invalid @enderror" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->kategori_id }}" {{ old('kategori_id') == $kat->kategori_id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
            @error('kategori_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="nama_kegiatan">
                <i class="fas fa-calendar-check form-icon"></i>
                Nama Kegiatan <span style="color: red;">*</span>
            </label>
            <input type="text" name="nama_kegiatan" id="nama_kegiatan"
                   class="form-control @error('nama_kegiatan') is-invalid @enderror"
                   value="{{ old('nama_kegiatan') }}" placeholder="Nama Kegiatan" required>
            @error('nama_kegiatan') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        {{-- Hari — pill/tag horizontal --}}
        <div class="form-group">
            <label>
                <i class="fas fa-calendar-day form-icon"></i>
                Hari Kegiatan <span style="color: red;">*</span>
            </label>
            <small class="text-muted d-block mb-2" style="margin-top: -6px;">
                <i class="fas fa-info-circle"></i>
                Pilih satu atau lebih hari. Kegiatan dibuat otomatis untuk setiap hari yang dipilih.
            </small>

            <div class="hari-pills-wrap">
                @foreach($hariList as $h)
                    @php $checked = in_array($h, old('hari', [])); @endphp
                    <label class="hari-pill {{ $checked ? 'active' : '' }}" for="hari{{ $loop->index }}">
                        <input type="checkbox" name="hari[]" value="{{ $h }}"
                               id="hari{{ $loop->index }}" {{ $checked ? 'checked' : '' }}>
                        {{ $h }}
                    </label>
                @endforeach
            </div>
            @error('hari') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 11px;">
            <div class="form-group">
                <label for="waktu_mulai">
                    <i class="fas fa-clock form-icon"></i> Waktu Mulai <span style="color: red;">*</span>
                </label>
                <input type="time" name="waktu_mulai" id="waktu_mulai"
                       class="form-control @error('waktu_mulai') is-invalid @enderror"
                       value="{{ old('waktu_mulai') }}" required>
                @error('waktu_mulai') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="waktu_selesai">
                    <i class="fas fa-clock form-icon"></i> Waktu Selesai <span style="color: red;">*</span>
                </label>
                <input type="time" name="waktu_selesai" id="waktu_selesai"
                       class="form-control @error('waktu_selesai') is-invalid @enderror"
                       value="{{ old('waktu_selesai') }}" required>
                @error('waktu_selesai') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="materi">
                <i class="fas fa-book form-icon"></i> Materi/Topik
            </label>
            <input type="text" name="materi" id="materi"
                   class="form-control @error('materi') is-invalid @enderror"
                   value="{{ old('materi') }}" placeholder="Contoh:Bacaan">
            @error('materi') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>
                <i class="fas fa-layer-group form-icon"></i> Kelas yang Mengikuti Kegiatan
            </label>
            <small class="text-muted d-block mb-3" style="margin-top: -8px;">
                <i class="fas fa-info-circle"></i>
                Kosongkan jika kegiatan untuk semua santri (umum).
            </small>
            @foreach($kelompokKelas as $kelompok)
                <div class="card mb-2" style="border: 1px solid #E8F7F2;">
                    <div class="card-header py-2" style="background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%);">
                        <strong style="color: var(--primary-dark);">
                            <i class="fas fa-folder-open"></i> {{ $kelompok->nama_kelompok }}
                        </strong>
                    </div>
                    <div class="card-body py-2">
                        <div class="row">
                            @forelse($kelompok->kelas as $kelas)
                                <div class="col-md-3 col-sm-4 col-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="kelas_ids[]" value="{{ $kelas->id }}"
                                               id="kelas{{ $kelas->id }}"
                                               {{ in_array($kelas->id, old('kelas_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kelas{{ $kelas->id }}">
                                            {{ $kelas->nama_kelas }}
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12"><small class="text-muted">Tidak ada kelas aktif</small></div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="form-group">
            <label for="keterangan">
                <i class="fas fa-align-left form-icon"></i> Keterangan
            </label>
            <textarea name="keterangan" id="keterangan"
                      class="form-control @error('keterangan') is-invalid @enderror"
                      rows="4" placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
            @error('keterangan') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('admin.kegiatan.jadwal') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<style>
.hari-pills-wrap {
    display: flex; flex-wrap: wrap; gap: 8px;
    padding: 12px 14px; border: 1px solid #E8F7F2;
    border-radius: 10px; background: #FAFFFE;
}
.hari-pill {
    display: inline-flex; align-items: center;
    padding: 6px 16px; border-radius: 20px; cursor: pointer;
    border: 1.5px solid #e2e8f0; background: #fff;
    font-size: 0.85rem; font-weight: 500; color: #374151;
    transition: all 0.18s; user-select: none; margin: 0;
}
.hari-pill input[type="checkbox"] { display: none; }
.hari-pill:hover { border-color: var(--primary-color); background: #f0fdf4; }
.hari-pill.active {
    border-color: var(--primary-color); background: #ECFDF5;
    color: var(--primary-dark); font-weight: 600;
}
</style>
<script>
document.querySelectorAll('.hari-pill input[type="checkbox"]').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var label = this.closest('.hari-pill');
        label.classList.toggle('active', this.checked);
    });
});
</script>
@endsection