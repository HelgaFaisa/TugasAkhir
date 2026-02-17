@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Input Capaian Santri</h2>
</div>

<div class="form-container">
    <form action="{{ route('admin.capaian.store') }}" method="POST" id="formCapaian">
        @csrf

        {{-- Info Box --}}
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <strong>Tips:</strong> Pilih santri terlebih dahulu untuk melihat materi yang sesuai dengan kelasnya.
        </div>

        {{-- Santri Selection --}}
        <div class="form-group">
            <label><i class="fas fa-user form-icon"></i> Pilih Santri <span style="color: red;">*</span></label>
            <select name="id_santri" id="id_santri" class="form-control @error('id_santri') is-invalid @enderror" required>
                <option value="">-- Pilih Santri --</option>
                @foreach($santris as $santri)
                    <option value="{{ $santri->id_santri }}" 
                            data-kelas="{{ $santri->kelas }}"
                            {{ old('id_santri', $selectedSantri?->id_santri) == $santri->id_santri ? 'selected' : '' }}>
                        {{ $santri->nama_lengkap }} ({{ $santri->nis }}) - Kelas: {{ $santri->kelas }}
                    </option>
                @endforeach
            </select>
            @error('id_santri')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Kelas Display (Auto) --}}
        <div class="form-group" id="kelasDisplay" style="display: none;">
            <label><i class="fas fa-users form-icon"></i> Kelas Santri</label>
            <input type="text" id="kelasSantri" class="form-control" readonly style="background-color: #f0f0f0; font-weight: bold;">
        </div>

        <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            {{-- Materi Selection --}}
            <div class="form-group">
                <label><i class="fas fa-book form-icon"></i> Pilih Materi <span style="color: red;">*</span></label>
                <select name="id_materi" id="id_materi" class="form-control @error('id_materi') is-invalid @enderror" required disabled>
                    <option value="">-- Pilih Santri Dahulu --</option>
                </select>
                <small class="form-text">Materi akan muncul sesuai kelas santri</small>
                @error('id_materi')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Semester Selection --}}
            <div class="form-group">
                <label><i class="fas fa-calendar-alt form-icon"></i> Semester <span style="color: red;">*</span></label>
                <select name="id_semester" id="id_semester" class="form-control @error('id_semester') is-invalid @enderror" required>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id_semester }}" 
                                {{ old('id_semester', $semesterAktif?->id_semester) == $semester->id_semester ? 'selected' : '' }}>
                            {{ $semester->nama_semester }} 
                            @if($semester->is_active) <strong>(Aktif)</strong> @endif
                        </option>
                    @endforeach
                </select>
                @error('id_semester')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Materi Info Display --}}
        <div id="materiInfo" style="display: none; margin-bottom: 20px;">
            <div class="info-box">
                <i class="fas fa-book-open"></i>
                <strong>Detail Materi:</strong>
                <div style="margin-top: 10px;">
                    <p style="margin: 5px 0;"><strong>Nama Kitab:</strong> <span id="infoNamaKitab"></span></p>
                    <p style="margin: 5px 0;"><strong>Kategori:</strong> <span id="infoKategori"></span></p>
                    <p style="margin: 5px 0;"><strong>Total Halaman:</strong> <span id="infoTotalHalaman"></span> halaman</p>
                    <p style="margin: 5px 0;"><strong>Range:</strong> Halaman <span id="infoHalamanMulai"></span> - <span id="infoHalamanAkhir"></span></p>
                </div>
            </div>
        </div>

        {{-- METODE INPUT HALAMAN --}}
        <div id="inputHalamanSection" style="display: none;">
            <div class="detail-section">
                <h4><i class="fas fa-keyboard"></i> Input Halaman yang Sudah Selesai</h4>
                
                {{-- Tab Metode Input --}}
                <div style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid var(--primary-light); padding-bottom: 10px;">
                    <button type="button" class="btn btn-sm btn-primary" id="btnMetode1" onclick="switchMetode(1)">
                        <i class="fas fa-keyboard"></i> Metode 1: Input Range Text
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" id="btnMetode2" onclick="switchMetode(2)">
                        <i class="fas fa-th"></i> Metode 2: Visual Grid
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" id="btnMetode3" onclick="switchMetode(3)">
                        <i class="fas fa-bolt"></i> Metode 3: Quick Input
                    </button>
                </div>

                {{-- METODE 1: INPUT RANGE TEXT --}}
                <div id="metode1" class="metode-input">
                    <div class="form-group">
                        <label><i class="fas fa-edit form-icon"></i> Input Range Halaman <span style="color: red;">*</span></label>
                        <input type="text" name="halaman_selesai" id="halaman_selesai" 
                               class="form-control @error('halaman_selesai') is-invalid @enderror" 
                               placeholder="Contoh: 1-10, 16-21, 40, 45-50"
                               value="{{ old('halaman_selesai') }}">
                        <small class="form-text">
                            <strong>Format:</strong> Gunakan tanda minus (-) untuk range dan koma (,) untuk memisahkan.<br>
                            <strong>Contoh:</strong> "1-10, 16-21, 40" artinya halaman 1 sampai 10, 16 sampai 21, dan halaman 40.
                        </small>
                        @error('halaman_selesai')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="button" class="btn btn-info" onclick="previewHalaman()">
                        <i class="fas fa-eye"></i> Preview Halaman
                    </button>
                </div>

                {{-- METODE 2: VISUAL GRID --}}
                <div id="metode2" class="metode-input" style="display: none;">
                    <div class="info-box" style="margin-bottom: 15px;">
                        <i class="fas fa-info-circle"></i>
                        <strong>Cara Pakai:</strong> Klik pada kotak halaman untuk menandai sebagai selesai. Klik lagi untuk membatalkan.
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <button type="button" class="btn btn-sm btn-success" onclick="selectAllPages()">
                            <i class="fas fa-check-square"></i> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="clearAllPages()">
                            <i class="fas fa-times"></i> Hapus Semua
                        </button>
                    </div>

                    <div id="gridHalaman" style="display: grid; grid-template-columns: repeat(10, 1fr); gap: 8px; max-height: 400px; overflow-y: auto;">
                        <!-- Grid akan di-generate oleh JavaScript -->
                    </div>

                    <p style="margin-top: 15px; color: var(--text-light);">
                        <i class="fas fa-check-circle" style="color: var(--success-color);"></i> = Sudah Selesai &nbsp;&nbsp;
                        <i class="fas fa-square" style="color: #ddd;"></i> = Belum Selesai
                    </p>
                </div>

                {{-- METODE 3: QUICK INPUT --}}
                <div id="metode3" class="metode-input" style="display: none;">
                    <div class="form-group">
                        <label><i class="fas fa-sliders-h form-icon"></i> Pilih Halaman Sampai</label>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span>Halaman 1 sampai</span>
                            <input type="number" id="quickInputValue" class="form-control" 
                                   style="width: 150px;" min="1" placeholder="400">
                            <button type="button" class="btn btn-primary" onclick="quickInput()">
                                <i class="fas fa-check"></i> Terapkan
                            </button>
                        </div>
                        <small class="form-text">Input cepat untuk halaman berurutan dari awal</small>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 15px;">
                        <button type="button" class="btn btn-success" onclick="selectAllPagesQuick()">
                            <i class="fas fa-check-double"></i> Semua Halaman
                        </button>
                        <button type="button" class="btn btn-danger" onclick="clearAllPagesQuick()">
                            <i class="fas fa-eraser"></i> Reset
                        </button>
                    </div>
                </div>

                {{-- Preview Result --}}
                <div id="previewResult" style="display: none; margin-top: 20px;">
                    <div class="info-box" style="background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%);">
                        <h4 style="margin: 0 0 10px 0; color: var(--primary-dark);">
                            <i class="fas fa-chart-pie"></i> Preview Capaian
                        </h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 15px;">
                            <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                                <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Halaman Selesai</p>
                                <h3 style="margin: 5px 0; color: var(--primary-color);" id="previewJumlah">0</h3>
                            </div>
                            <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                                <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Total Halaman</p>
                                <h3 style="margin: 5px 0; color: var(--text-color);" id="previewTotal">0</h3>
                            </div>
                            <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                                <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Persentase</p>
                                <h3 style="margin: 5px 0; color: var(--success-color);" id="previewPersentase">0%</h3>
                            </div>
                        </div>
                        <div style="margin-top: 15px;">
                            <div class="progress-bar">
                                <div class="progress-fill" id="progressBar" style="width: 0%; background: linear-gradient(90deg, var(--primary-color), var(--success-color));"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan --}}
        <div class="form-group">
            <label><i class="fas fa-sticky-note form-icon"></i> Catatan (Optional)</label>
            <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" 
                      rows="3" placeholder="Catatan tambahan tentang capaian ini...">{{ old('catatan') }}</textarea>
            @error('catatan')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Tanggal Input --}}
        <div class="form-group">
            <label><i class="fas fa-calendar-day form-icon"></i> Tanggal Input <span style="color: red;">*</span></label>
            <input type="date" name="tanggal_input" class="form-control @error('tanggal_input') is-invalid @enderror" 
                   value="{{ old('tanggal_input', date('Y-m-d')) }}" required>
            @error('tanggal_input')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Action Buttons --}}
        <div class="btn-group">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Capaian
            </button>
            <a href="{{ route('admin.capaian.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
let currentMetode = 1;
let totalHalaman = 0;
let halamanMulai = 0;
let halamanAkhir = 0;
let selectedPages = new Set();

// Initialize on page load - check for pre-selected santri
document.addEventListener('DOMContentLoaded', function() {
    const santriSelect = document.getElementById('id_santri');
    if (santriSelect.value) {
        // Trigger the materi loading for pre-selected santri
        loadMateriForSantri(santriSelect.value, santriSelect.options[santriSelect.selectedIndex].dataset.kelas);
    }
});

// Function to load materi (can be reused)
function loadMateriForSantri(idSantri, kelasSantri) {
    if (!idSantri) {
        document.getElementById('kelasDisplay').style.display = 'none';
        document.getElementById('id_materi').disabled = true;
        document.getElementById('id_materi').innerHTML = '<option value="">-- Pilih Santri Dahulu --</option>';
        document.getElementById('materiInfo').style.display = 'none';
        document.getElementById('inputHalamanSection').style.display = 'none';
        return;
    }
    
    // Show kelas
    document.getElementById('kelasDisplay').style.display = 'block';
    document.getElementById('kelasSantri').value = kelasSantri;
    
    // Load materi via AJAX
    fetch('{{ route("admin.capaian.ajax.get-materi") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id_santri: idSantri })
    })
    .then(response => response.json())
    .then(data => {
        const selectMateri = document.getElementById('id_materi');
        selectMateri.innerHTML = '<option value="">-- Pilih Materi --</option>';
        
        data.materis.forEach(materi => {
            const option = document.createElement('option');
            option.value = materi.id_materi;
            option.textContent = `${materi.nama_kitab} (${materi.kategori}) - Hal. ${materi.halaman_mulai}-${materi.halaman_akhir}`;
            option.dataset.materiData = JSON.stringify(materi);
            selectMateri.appendChild(option);
        });
        
        selectMateri.disabled = false;
    })
    .catch(error => console.error('Error:', error));
}

// Switch metode input
function switchMetode(metode) {
    currentMetode = metode;
    
    // Hide all metode
    document.querySelectorAll('.metode-input').forEach(el => el.style.display = 'none');
    
    // Show selected metode
    document.getElementById('metode' + metode).style.display = 'block';
    
    // Update button styles
    for (let i = 1; i <= 3; i++) {
        const btn = document.getElementById('btnMetode' + i);
        if (i === metode) {
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-primary');
        } else {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-secondary');
        }
    }
    
    // Sync input
    syncInputBetweenMetodes();
}

// Load materi saat santri dipilih
document.getElementById('id_santri').addEventListener('change', function() {
    const idSantri = this.value;
    const kelasSantri = this.options[this.selectedIndex].dataset.kelas;
    loadMateriForSantri(idSantri, kelasSantri);
});

// Load detail materi saat materi dipilih
document.getElementById('id_materi').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    
    if (!this.value) {
        document.getElementById('materiInfo').style.display = 'none';
        document.getElementById('inputHalamanSection').style.display = 'none';
        return;
    }
    
    const materiData = JSON.parse(selectedOption.dataset.materiData);
    
    // Update materi info
    document.getElementById('infoNamaKitab').textContent = materiData.nama_kitab;
    document.getElementById('infoKategori').textContent = materiData.kategori;
    document.getElementById('infoTotalHalaman').textContent = materiData.total_halaman;
    document.getElementById('infoHalamanMulai').textContent = materiData.halaman_mulai;
    document.getElementById('infoHalamanAkhir').textContent = materiData.halaman_akhir;
    
    document.getElementById('materiInfo').style.display = 'block';
    document.getElementById('inputHalamanSection').style.display = 'block';
    
    // Set global variables
    totalHalaman = materiData.total_halaman;
    halamanMulai = materiData.halaman_mulai;
    halamanAkhir = materiData.halaman_akhir;
    
    // Generate grid untuk metode 2
    generateGrid();
    
    // Set quick input max
    document.getElementById('quickInputValue').max = totalHalaman;
    document.getElementById('quickInputValue').placeholder = totalHalaman;
    
    // Check existing capaian
    checkExistingCapaian();
});

// Check existing capaian juga saat semester berubah
document.getElementById('id_semester').addEventListener('change', function() {
    checkExistingCapaian();
});

// Generate grid halaman (Metode 2)
function generateGrid() {
    const gridContainer = document.getElementById('gridHalaman');
    gridContainer.innerHTML = '';
    
    for (let i = halamanMulai; i <= halamanAkhir; i++) {
        const pageBox = document.createElement('div');
        pageBox.className = 'page-box';
        pageBox.textContent = i;
        pageBox.dataset.page = i;
        pageBox.style.cssText = `
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            font-weight: 600;
        `;
        
        pageBox.addEventListener('click', function() {
            togglePage(i, this);
        });
        
        gridContainer.appendChild(pageBox);
    }
}

// Toggle page selection
function togglePage(pageNum, element) {
    if (selectedPages.has(pageNum)) {
        selectedPages.delete(pageNum);
        element.style.background = 'white';
        element.style.borderColor = '#ddd';
        element.style.color = '#333';
    } else {
        selectedPages.add(pageNum);
        element.style.background = 'linear-gradient(135deg, var(--primary-color), var(--success-color))';
        element.style.borderColor = 'var(--primary-color)';
        element.style.color = 'white';
    }
    
    updatePreview();
    syncInputBetweenMetodes();
}

// Select all pages
function selectAllPages() {
    selectedPages.clear();
    for (let i = halamanMulai; i <= halamanAkhir; i++) {
        selectedPages.add(i);
    }
    updateGridDisplay();
    updatePreview();
    syncInputBetweenMetodes();
}

function selectAllPagesQuick() {
    selectedPages.clear();
    for (let i = halamanMulai; i <= halamanAkhir; i++) {
        selectedPages.add(i);
    }
    updatePreview();
    syncInputBetweenMetodes();
}

// Clear all pages
function clearAllPages() {
    selectedPages.clear();
    updateGridDisplay();
    updatePreview();
    syncInputBetweenMetodes();
}

function clearAllPagesQuick() {
    selectedPages.clear();
    document.getElementById('quickInputValue').value = '';
    updatePreview();
    syncInputBetweenMetodes();
}

// Update grid display
function updateGridDisplay() {
    document.querySelectorAll('.page-box').forEach(box => {
        const pageNum = parseInt(box.dataset.page);
        if (selectedPages.has(pageNum)) {
            box.style.background = 'linear-gradient(135deg, var(--primary-color), var(--success-color))';
            box.style.borderColor = 'var(--primary-color)';
            box.style.color = 'white';
        } else {
            box.style.background = 'white';
            box.style.borderColor = '#ddd';
            box.style.color = '#333';
        }
    });
}

// Quick input
function quickInput() {
    const value = parseInt(document.getElementById('quickInputValue').value);
    if (!value || value < 1 || value > totalHalaman) {
        alert('Masukkan nilai yang valid!');
        return;
    }
    
    selectedPages.clear();
    for (let i = halamanMulai; i <= (halamanMulai + value - 1); i++) {
        selectedPages.add(i);
    }
    
    updatePreview();
    syncInputBetweenMetodes();
}

// Sync input between metodes
function syncInputBetweenMetodes() {
    if (selectedPages.size === 0) {
        document.getElementById('halaman_selesai').value = '';
        return;
    }
    
    // Convert Set to sorted array
    const pagesArray = Array.from(selectedPages).sort((a, b) => a - b);
    
    // Convert to range string
    const rangeString = convertToRangeString(pagesArray);
    document.getElementById('halaman_selesai').value = rangeString;
}

// Convert array to range string
function convertToRangeString(pages) {
    if (pages.length === 0) return '';
    
    const ranges = [];
    let start = pages[0];
    let end = pages[0];
    
    for (let i = 1; i < pages.length; i++) {
        if (pages[i] === end + 1) {
            end = pages[i];
        } else {
            ranges.push(start === end ? `${start}` : `${start}-${end}`);
            start = pages[i];
            end = pages[i];
        }
    }
    ranges.push(start === end ? `${start}` : `${start}-${end}`);
    
    return ranges.join(',');
}

// Preview halaman (Metode 1)
function previewHalaman() {
    const rangeString = document.getElementById('halaman_selesai').value.trim();
    
    if (!rangeString) {
        alert('Masukkan range halaman terlebih dahulu!');
        return;
    }
    
    try {
        selectedPages = parseRangeString(rangeString);
        updatePreview();
        updateGridDisplay();
    } catch (error) {
        alert('Format range tidak valid! Gunakan format: 1-10,16-21,40');
    }
}

// Parse range string to Set
function parseRangeString(rangeString) {
    const pages = new Set();
    const ranges = rangeString.split(',');
    
    ranges.forEach(range => {
        range = range.trim();
        if (range.includes('-')) {
            const [start, end] = range.split('-').map(num => parseInt(num.trim()));
            for (let i = start; i <= end; i++) {
                if (i >= halamanMulai && i <= halamanAkhir) {
                    pages.add(i);
                }
            }
        } else {
            const pageNum = parseInt(range);
            if (pageNum >= halamanMulai && pageNum <= halamanAkhir) {
                pages.add(pageNum);
            }
        }
    });
    
    return pages;
}

// Update preview
function updatePreview() {
    const jumlah = selectedPages.size;
    const persentase = totalHalaman > 0 ? ((jumlah / totalHalaman) * 100).toFixed(2) : 0;
    
    document.getElementById('previewJumlah').textContent = jumlah;
    document.getElementById('previewTotal').textContent = totalHalaman;
    document.getElementById('previewPersentase').textContent = persentase + '%';
    document.getElementById('progressBar').style.width = persentase + '%';
    
    document.getElementById('previewResult').style.display = 'block';
}

// Check existing capaian
function checkExistingCapaian() {
    const idSantri = document.getElementById('id_santri').value;
    const idMateri = document.getElementById('id_materi').value;
    const idSemester = document.getElementById('id_semester').value;
    
    if (!idSantri || !idMateri || !idSemester) return;
    
    fetch('{{ route("admin.capaian.ajax.get-detail-materi") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            id_santri: idSantri,
            id_materi: idMateri,
            id_semester: idSemester
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.existing_capaian && data.existing_capaian.halaman_selesai) {
            // Tampilkan info bahwa data akan di-update
            const infoBox = document.createElement('div');
            infoBox.className = 'alert alert-info';
            infoBox.style.cssText = 'margin: 15px 0; padding: 15px; background: #e3f2fd; border-left: 4px solid #2196F3; border-radius: 4px;';
            infoBox.innerHTML = `
                <i class="fas fa-info-circle"></i> 
                <strong>Data Existing Ditemukan!</strong><br>
                Capaian untuk santri dan materi ini sudah ada. 
                Data sebelumnya akan dimuat ke form. Saat submit, data akan di-update otomatis.
            `;
            
            // Insert info box sebelum form
            const formElement = document.getElementById('formCapaian');
            if (!document.querySelector('.alert-info')) {
                formElement.insertBefore(infoBox, formElement.firstChild);
            }
            
            // Load data existing ke form
            const halamanSelesai = data.existing_capaian.halaman_selesai;
            document.getElementById('halaman_selesai').value = halamanSelesai;
            
            // Parse dan load ke selected pages
            if (halamanSelesai) {
                selectedPages = parseRangeString(halamanSelesai);
                updateGridDisplay();
                updatePreview();
            }
            
            // Load catatan jika ada
            if (data.existing_capaian.catatan) {
                document.getElementById('catatan').value = data.existing_capaian.catatan;
            }
            
            // Load tanggal input
            if (data.existing_capaian.tanggal_input) {
                document.getElementById('tanggal_input').value = data.existing_capaian.tanggal_input;
            }
        } else {
            // Hapus info box jika tidak ada data existing
            const existingAlert = document.querySelector('.alert-info');
            if (existingAlert) {
                existingAlert.remove();
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

// Form validation before submit
document.getElementById('formCapaian').addEventListener('submit', function(e) {
    const halamanSelesai = document.getElementById('halaman_selesai').value.trim();
    
    if (!halamanSelesai) {
        e.preventDefault();
        alert('Silakan input halaman yang sudah selesai!');
        return false;
    }
});
</script>

<style>
.page-box:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endsection