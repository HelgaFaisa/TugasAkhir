@php
    $isEdit = isset($santri);
@endphp

<form action="{{ $isEdit ? route('admin.santri.update', $santri) : route('admin.santri.store') }}" method="POST" class="data-form">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="id_santri">ID Santri</label>
        <input type="text" id="id_santri" name="id_santri" value="{{ $isEdit ? $santri->id_santri : $nextIdSantri ?? 'Otomatis Dibuat' }}" class="form-control" disabled>
        <small class="form-text text-muted">{{ $isEdit ? 'ID Santri tidak dapat diubah.' : 'ID akan otomatis di-generate (Contoh: ' . ($nextIdSantri ?? 'S001') . ')' }}</small>
    </div>

    <div class="form-group">
        <label for="nis">NIS (Nomor Induk Santri)</label>
        <input type="text" id="nis" name="nis" value="{{ old('nis', $isEdit ? $santri->nis : '') }}" class="form-control @error('nis') is-invalid @enderror" placeholder="Masukkan NIS">
        @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="nama_lengkap">Nama Lengkap *</label>
        <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $isEdit ? $santri->nama_lengkap : '') }}" class="form-control @error('nama_lengkap') is-invalid @enderror" required placeholder="Masukkan nama lengkap">
        @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="jenis_kelamin">Jenis Kelamin *</label>
        <select id="jenis_kelamin" name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
            <option value="">Pilih Jenis Kelamin</option>
            <option value="Laki-laki" {{ old('jenis_kelamin', $isEdit ? $santri->jenis_kelamin : '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
            <option value="Perempuan" {{ old('jenis_kelamin', $isEdit ? $santri->jenis_kelamin : '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
        </select>
        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="kelas">Kelas *</label>
        <select id="kelas" name="kelas" class="form-control @error('kelas') is-invalid @enderror" required>
            <option value="">Pilih Kelas</option>
            <option value="PB" {{ old('kelas', $isEdit ? $santri->kelas : '') == 'PB' ? 'selected' : '' }}>PB (Pembinaan)</option>
            <option value="Lambatan" {{ old('kelas', $isEdit ? $santri->kelas : '') == 'Lambatan' ? 'selected' : '' }}>Lambatan</option>
            <option value="Cepatan" {{ old('kelas', $isEdit ? $santri->kelas : '') == 'Cepatan' ? 'selected' : '' }}>Cepatan</option>
        </select>
        @error('kelas')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="status">Status *</label>
        <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
            <option value="">Pilih Status</option>
            <option value="Aktif" {{ old('status', $isEdit ? $santri->status : 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Lulus" {{ old('status', $isEdit ? $santri->status : '') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
            <option value="Tidak Aktif" {{ old('status', $isEdit ? $santri->status : '') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="alamat_santri">Alamat Santri</label>
        <textarea id="alamat_santri" name="alamat_santri" class="form-control @error('alamat_santri') is-invalid @enderror" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat_santri', $isEdit ? $santri->alamat_santri : '') }}</textarea>
        @error('alamat_santri')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="daerah_asal">Daerah Asal</label>
        <input type="text" id="daerah_asal" name="daerah_asal" value="{{ old('daerah_asal', $isEdit ? $santri->daerah_asal : '') }}" class="form-control @error('daerah_asal') is-invalid @enderror" placeholder="Contoh: Yogyakarta">
        @error('daerah_asal')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <hr>
    <h4><i class="fas fa-users"></i> Data Orang Tua / Wali</h4>

    <div class="form-group">
        <label for="nama_orang_tua">Nama Orang Tua</label>
        <input type="text" id="nama_orang_tua" name="nama_orang_tua" value="{{ old('nama_orang_tua', $isEdit ? $santri->nama_orang_tua : '') }}" class="form-control @error('nama_orang_tua') is-invalid @enderror" placeholder="Masukkan nama orang tua">
        @error('nama_orang_tua')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="nomor_hp_ortu">Nomor HP Orang Tua</label>
        <input type="text" id="nomor_hp_ortu" name="nomor_hp_ortu" value="{{ old('nomor_hp_ortu', $isEdit ? $santri->nomor_hp_ortu : '') }}" class="form-control @error('nomor_hp_ortu') is-invalid @enderror" placeholder="Contoh: 08123456789">
        @error('nomor_hp_ortu')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div style="margin-top: 30px; display: flex; gap: 10px;">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> {{ $isEdit ? 'Update Data' : 'Simpan Santri' }}
        </button>
        <a href="{{ route('admin.santri.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> Batal
        </a>
    </div>
</form>