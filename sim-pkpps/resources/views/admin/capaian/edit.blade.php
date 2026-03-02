@extends('layouts.app')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit"></i> Edit Capaian Santri</h2>
</div>

<div class="form-container">
    <form action="{{ route('admin.capaian.update', $capaian) }}" method="POST" id="formCapaian">
        @csrf
        @method('PUT')

        {{-- Info Box --}}
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <strong>ID Capaian:</strong> {{ $capaian->id_capaian }}
        </div>

        {{-- Santri Info (Read Only) --}}
        <div class="detail-section">
            <h4><i class="fas fa-user"></i> Informasi Santri & Materi</h4>
            <table class="detail-table">
                <tr>
                    <th><i class="fas fa-user"></i> Santri</th>
                    <td><strong>{{ $capaian->santri->nama_lengkap }}</strong> ({{ $capaian->santri->nis }})</td>
                </tr>
                <tr>
                    <th><i class="fas fa-users"></i> Kelas</th>
                    <td><span class="badge badge-secondary">{{ $capaian->santri->kelas }}</span></td>
                </tr>
                <tr>
                    <th><i class="fas fa-book"></i> Materi</th>
                    <td><strong>{{ $capaian->materi->nama_kitab }}</strong></td>
                </tr>
                <tr>
                    <th><i class="fas fa-layer-group"></i> Kategori</th>
                    <td>{!! $capaian->materi->kategori_badge !!}</td>
                </tr>
                <tr>
                    <th><i class="fas fa-file-alt"></i> Total Halaman</th>
                    <td><strong>{{ $capaian->materi->total_halaman }}</strong> halaman ({{ $capaian->materi->halaman_mulai }} - {{ $capaian->materi->halaman_akhir }})</td>
                </tr>
                <tr>
                    <th><i class="fas fa-calendar-alt"></i> Semester</th>
                    <td>{{ $capaian->semester->nama_semester }}</td>
                </tr>
            </table>
        </div>

        {{-- METODE INPUT HALAMAN --}}
        <div class="detail-section">
            <h4><i class="fas fa-keyboard"></i> Update Halaman yang Sudah Selesai</h4>
            
            {{-- Tab Metode Input --}}
            <div style="display: flex; gap: 10px; margin-bottom: 14px; border-bottom: 2px solid var(--primary-light); padding-bottom: 10px;">
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
                           value="{{ old('halaman_selesai', $capaian->halaman_selesai) }}" required>
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
                    <div style="display: flex; align-items: center; gap: 11px;">
                        <span>Halaman {{ $capaian->materi->halaman_mulai }} sampai</span>
                        <input type="number" id="quickInputValue" class="form-control" 
                               style="width: 150px;" 
                               min="{{ $capaian->materi->halaman_mulai }}" 
                               max="{{ $capaian->materi->halaman_akhir }}"
                               placeholder="{{ $capaian->materi->halaman_akhir }}">
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
            <div id="previewResult" style="margin-top: 14px;">
                <div class="info-box" style="background: linear-gradient(135deg, #E8F7F2 0%, #D4F1E3 100%);">
                    <h4 style="margin: 0 0 10px 0; color: var(--primary-dark);">
                        <i class="fas fa-chart-pie"></i> Preview Capaian
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 11px; margin-top: 15px;">
                        <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                            <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Halaman Selesai</p>
                            <h3 style="margin: 5px 0; color: var(--primary-color);" id="previewJumlah">{{ $capaian->jumlah_halaman_selesai }}</h3>
                        </div>
                        <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                            <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Total Halaman</p>
                            <h3 style="margin: 5px 0; color: var(--text-color);" id="previewTotal">{{ $capaian->materi->total_halaman }}</h3>
                        </div>
                        <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                            <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Persentase</p>
                            <h3 style="margin: 5px 0; color: var(--success-color);" id="previewPersentase">{{ number_format($capaian->persentase, 2) }}%</h3>
                        </div>
                    </div>
                    <div style="margin-top: 15px;">
                        <div class="progress-bar">
                            <div class="progress-fill" id="progressBar" style="width: {{ $capaian->persentase }}%; background: linear-gradient(90deg, var(--primary-color), var(--success-color));"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan --}}
        <div class="form-group">
            <label><i class="fas fa-sticky-note form-icon"></i> Catatan (Optional)</label>
            <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" 
                      rows="3" placeholder="Catatan tambahan tentang capaian ini...">{{ old('catatan', $capaian->catatan) }}</textarea>
            @error('catatan')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Tanggal Input --}}
        <div class="form-group">
            <label><i class="fas fa-calendar-day form-icon"></i> Tanggal Input <span style="color: red;">*</span></label>
            <input type="date" name="tanggal_input" class="form-control @error('tanggal_input') is-invalid @enderror" 
                   value="{{ old('tanggal_input', $capaian->tanggal_input->format('Y-m-d')) }}" required>
            @error('tanggal_input')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Action Buttons --}}
        <div class="btn-group">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Update Capaian
            </button>
            <a href="{{ route('admin.capaian.show', $capaian) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<script>
let currentMetode = 1;
let totalHalaman = {{ $capaian->materi->total_halaman }};
let halamanMulai = {{ $capaian->materi->halaman_mulai }};
let halamanAkhir = {{ $capaian->materi->halaman_akhir }};
let selectedPages = new Set();

// Initialize with existing data
document.addEventListener('DOMContentLoaded', function() {
    const existingHalaman = "{{ $capaian->halaman_selesai }}";
    if (existingHalaman) {
        selectedPages = parseRangeString(existingHalaman);
        generateGrid();
        updateGridDisplay();
        updatePreview();
    } else {
        generateGrid();
    }
});

// Switch metode input
function switchMetode(metode) {
    currentMetode = metode;
    
    document.querySelectorAll('.metode-input').forEach(el => el.style.display = 'none');
    document.getElementById('metode' + metode).style.display = 'block';
    
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
    
    syncInputBetweenMetodes();
}

// Generate grid halaman
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

function quickInput() {
    const value = parseInt(document.getElementById('quickInputValue').value);
    if (!value || value < halamanMulai || value > halamanAkhir) {
        alert('Masukkan nilai yang valid!');
        return;
    }
    
    selectedPages.clear();
    for (let i = halamanMulai; i <= value; i++) {
        selectedPages.add(i);
    }
    
    updatePreview();
    syncInputBetweenMetodes();
}

function syncInputBetweenMetodes() {
    if (selectedPages.size === 0) {
        document.getElementById('halaman_selesai').value = '';
        return;
    }
    
    const pagesArray = Array.from(selectedPages).sort((a, b) => a - b);
    const rangeString = convertToRangeString(pagesArray);
    document.getElementById('halaman_selesai').value = rangeString;
}

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

function updatePreview() {
    const jumlah = selectedPages.size;
    const persentase = totalHalaman > 0 ? ((jumlah / totalHalaman) * 100).toFixed(2) : 0;
    
    document.getElementById('previewJumlah').textContent = jumlah;
    document.getElementById('previewTotal').textContent = totalHalaman;
    document.getElementById('previewPersentase').textContent = persentase + '%';
    document.getElementById('progressBar').style.width = persentase + '%';
}
</script>

<style>
.page-box:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endsection