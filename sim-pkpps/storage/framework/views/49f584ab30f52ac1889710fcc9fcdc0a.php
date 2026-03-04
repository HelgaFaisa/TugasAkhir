<?php $__env->startSection('title', 'Tambah Izin Kepulangan'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-plus-circle"></i> Tambah Izin Kepulangan</h2>
</div>


<div style="background: #E8F7F2; padding: 15px; border-radius: 8px; margin-bottom: 14px; border-left: 4px solid #6FBA9D;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 11px;">
        <div>
            <strong> Periode Kuota:</strong><br>
            <?php echo e($settings->periode_mulai->format('d M Y')); ?> - <?php echo e($settings->periode_akhir->format('d M Y')); ?>

        </div>
        <div>
            <strong> Kuota Maksimal:</strong><br>
            <?php echo e($settings->kuota_maksimal); ?> Hari / Tahun
        </div>
    </div>
</div>

<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul style="margin: 0; padding-left: 20px;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="content-box">
    <form action="<?php echo e(route('admin.kepulangan.store')); ?>" method="POST" id="kepulanganForm">
        <?php echo csrf_field(); ?>
        
        <div class="form-group">
            <label for="id_santri">
                <i class="fas fa-user form-icon"></i>
                Pilih Santri <span style="color: #dc3545;">*</span>
            </label>
            <select name="id_santri" id="id_santri" class="form-control" required>
                <option value="">-- Pilih Santri --</option>
                <?php $__currentLoopData = $santriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $santri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($santri->id_santri); ?>" <?php echo e(old('id_santri') == $santri->id_santri ? 'selected' : ''); ?>>
                        <?php echo e($santri->nama_lengkap); ?> (<?php echo e($santri->id_santri); ?> - <?php echo e($santri->kelas); ?>)
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        
        <div id="santriInfo" style="display: none; background: #f8f9fa; padding: 14px; border-radius: 8px; margin-bottom: 14px; border-left: 4px solid #6FBA9D;">
            <h4 style="margin-top: 0; color: #2C3E50;"> Informasi Santri & Kuota</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div>
                    <p style="margin: 5px 0;"><strong>Nama:</strong> <span id="santriNama">-</span></p>
                    <p style="margin: 5px 0;"><strong>Kelas:</strong> <span id="santriKelas">-</span></p>
                    <p style="margin: 5px 0;"><strong>Periode:</strong> <span id="santriPeriode">-</span></p>
                </div>
                <div>
                    <p style="margin: 5px 0;"><strong>Kuota Maksimal:</strong> <span id="kuotaMaksimal">-</span></p>
                    <p style="margin: 5px 0;"><strong>Total Terpakai:</strong> <span id="totalTerpakai" class="badge">-</span></p>
                    <p style="margin: 5px 0;"><strong>Sisa Kuota:</strong> <span id="sisaKuota" class="badge">-</span></p>
                </div>
            </div>
            
            
            <div style="margin-top: 15px;">
                <label style="font-size: 0.9rem; color: #7F8C8D; margin-bottom: 5px;">Penggunaan Kuota:</label>
                <div style="width: 100%; height: 20px; background: #E0F0EC; border-radius: 10px; overflow: hidden; position: relative;">
                    <div id="progressBar" style="height: 100%; width: 0%; background: #28a745; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; font-weight: 600;"></div>
                </div>
                <small id="progressText" style="color: #7F8C8D; margin-top: 5px; display: block;">0% dari kuota terpakai</small>
            </div>

            <div id="warningOverLimit" style="display: none; margin-top: 15px; padding: 12px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; color: #856404;">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>âš ï¸ PERHATIAN:</strong> <span id="warningText"></span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 14px;">
            <div class="form-group">
                <label for="tanggal_pulang">
                    <i class="fas fa-calendar-alt form-icon"></i>
                    Tanggal Pulang <span style="color: #dc3545;">*</span>
                </label>
                <input type="date" 
                       name="tanggal_pulang" 
                       id="tanggal_pulang" 
                       class="form-control" 
                       value="<?php echo e(old('tanggal_pulang', date('Y-m-d'))); ?>"
                       min="<?php echo e(date('Y-m-d')); ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label for="tanggal_kembali">
                    <i class="fas fa-calendar-check form-icon"></i>
                    Tanggal Kembali <span style="color: #dc3545;">*</span>
                </label>
                <input type="date" 
                       name="tanggal_kembali" 
                       id="tanggal_kembali" 
                       class="form-control" 
                       value="<?php echo e(old('tanggal_kembali')); ?>"
                       required>
            </div>
        </div>

        
        <div id="durasiInfo" style="display: none; background: #fff3e0; padding: 14px; border-radius: 8px; margin-bottom: 14px; border-left: 4px solid #ff9800;">
            <h4 style="margin-top: 0; color: #2C3E50;">â±ï¸ Detail Durasi Izin</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 11px;">
                <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #7F8C8D; margin-bottom: 5px;">Durasi Izin</div>
                    <div id="durasiHari" style="font-size: 2rem; font-weight: 700; color: #ff9800;">0</div>
                    <div style="font-size: 0.8rem; color: #7F8C8D;">hari</div>
                </div>
                <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #7F8C8D; margin-bottom: 5px;">Total Setelah Izin</div>
                    <div id="totalSetelahIzin" style="font-size: 2rem; font-weight: 700; color: #2196f3;">0</div>
                    <div style="font-size: 0.8rem; color: #7F8C8D;">hari terpakai</div>
                </div>
                <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                    <div style="font-size: 0.85rem; color: #7F8C8D; margin-bottom: 5px;">Sisa Kuota</div>
                    <div id="sisaKuotaSetelah" style="font-size: 2rem; font-weight: 700; color: #28a745;"><?php echo e($settings->kuota_maksimal); ?></div>
                    <div style="font-size: 0.8rem; color: #7F8C8D;">hari tersisa</div>
                </div>
            </div>
            <div id="warningDurasi" style="display: none; margin-top: 15px; padding: 12px; background: #ffebee; border: 1px solid #ffcdd2; border-radius: 6px; color: #c62828;">
                <i class="fas fa-exclamation-circle"></i>
                <strong>âš ï¸ PERHATIAN:</strong> <span id="warningDurasiMessage"></span>
            </div>
        </div>

        <div class="form-group">
            <label for="alasan">
                <i class="fas fa-comment-alt form-icon"></i>
                Alasan Kepulangan <span style="color: #dc3545;">*</span>
            </label>
            <textarea name="alasan" 
                      id="alasan" 
                      class="form-control" 
                      rows="4" 
                      placeholder="Jelaskan alasan kepulangan"
                      required><?php echo e(old('alasan')); ?></textarea>
            <small style="color: #7F8C8D; margin-top: 5px; display: block;">
                <span id="charCount">0</span>/500 karakter
            </small>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-save"></i> Simpan Izin Kepulangan
            </button>
            <button type="reset" class="btn btn-secondary">
                <i class="fas fa-undo"></i> Reset Form
            </button>
            <a href="<?php echo e(route('admin.kepulangan.index')); ?>" class="btn btn-danger">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>


<div class="modal fade" id="overLimitModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 14px;">
            <div style="margin-bottom: 14px;">
                <h3 style="margin: 0; color: #2C3E50;">âš ï¸ Konfirmasi Izin Melebihi Batas</h3>
            </div>
            <div style="padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; margin-bottom: 15px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #856404; margin-bottom: 10px;"></i>
                <h4 style="margin: 10px 0; color: #856404;">Peringatan!</h4>
                <p id="overLimitMessage" style="margin: 0; color: #856404;"></p>
            </div>
            <p style="margin: 15px 0;">Izin tetap bisa diproses, tetapi santri ini akan <strong>melebihi kuota maksimal</strong>.</p>
            <p style="margin: 15px 0; color: #7F8C8D; font-size: 0.9rem;">Apakah Anda yakin ingin melanjutkan pengajuan izin ini?</p>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 14px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('overLimitModal')">Batal</button>
                <button type="button" class="btn btn-warning" id="confirmOverLimit" style="background: #ff9800; border-color: #ff9800;">
                    <i class="fas fa-check"></i> Ya, Lanjutkan Tetap
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.modal.fade { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
.modal-dialog { max-width: 500px; width: 90%; margin: auto; }
.modal-content { max-height: 90vh; overflow-y: auto; }
.badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 0.9rem; font-weight: 600; }
</style>

<script>
let currentSantriData = null;
let isOverLimit = false;

// Load santri data when selected
document.getElementById('id_santri').addEventListener('change', function() {
    const santriId = this.value;
    
    if (!santriId) {
        document.getElementById('santriInfo').style.display = 'none';
        currentSantriData = null;
        calculateDurasi();
        return;
    }

    const infoDiv = document.getElementById('santriInfo');
    infoDiv.style.display = 'block';
    
    // Show loading state
    infoDiv.innerHTML = '<div style="text-align: center; padding: 14px;"><i class="fas fa-spinner fa-spin"></i> Memuat data santri...</div>';

    // PERBAIKAN: Proper error handling untuk API
    fetch(`/admin/kepulangan/api/santri/${santriId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                currentSantriData = data;
                updateSantriInfo(data);
                calculateDurasi();
            } else {
                showError(data.message || 'Gagal memuat data santri');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Terjadi kesalahan saat memuat data santri: ' + error.message);
        });
});

// PERBAIKAN: Function untuk show error
function showError(message) {
    const infoDiv = document.getElementById('santriInfo');
    infoDiv.innerHTML = `
        <div style="padding: 15px; background: #ffebee; border: 1px solid #ffcdd2; border-radius: 6px; color: #c62828;">
            <i class="fas fa-exclamation-circle"></i>
            <strong>Error:</strong> ${message}
        </div>
    `;
    currentSantriData = null;
}

function updateSantriInfo(data) {
    const santri = data.santri;
    const kuota = data.penggunaan_izin;
    
    // Rebuild HTML structure
    const infoDiv = document.getElementById('santriInfo');
    infoDiv.innerHTML = `
        <h4 style="margin-top: 0; color: #2C3E50;"> Informasi Santri & Kuota</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div>
                <p style="margin: 5px 0;"><strong>Nama:</strong> <span id="santriNama">${santri.nama_lengkap}</span></p>
                <p style="margin: 5px 0;"><strong>Kelas:</strong> <span id="santriKelas">${santri.kelas}</span></p>
                <p style="margin: 5px 0;"><strong>Periode:</strong> <span id="santriPeriode">${kuota.periode_mulai} - ${kuota.periode_akhir}</span></p>
            </div>
            <div>
                <p style="margin: 5px 0;"><strong>Kuota Maksimal:</strong> <span id="kuotaMaksimal">${kuota.kuota_maksimal} hari</span></p>
                <p style="margin: 5px 0;"><strong>Total Terpakai:</strong> <span id="totalTerpakai" class="badge" style="background: ${getBadgeColor(kuota.badge_color)}; color: ${kuota.badge_color === 'warning' ? '#000' : 'white'};">${kuota.total_terpakai} hari</span></p>
                <p style="margin: 5px 0;"><strong>Sisa Kuota:</strong> <span id="sisaKuota" class="badge" style="background: ${getBadgeColor(kuota.badge_color)}; color: ${kuota.badge_color === 'warning' ? '#000' : 'white'};">${kuota.sisa_kuota} hari</span></p>
            </div>
        </div>
        
        <div style="margin-top: 15px;">
            <label style="font-size: 0.9rem; color: #7F8C8D; margin-bottom: 5px;">Penggunaan Kuota:</label>
            <div style="width: 100%; height: 20px; background: #E0F0EC; border-radius: 10px; overflow: hidden; position: relative;">
                <div id="progressBar" style="height: 100%; width: ${Math.min(100, kuota.persentase)}%; background: ${getProgressColor(kuota.persentase)}; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; font-weight: 600;">${kuota.persentase}%</div>
            </div>
            <small id="progressText" style="color: #7F8C8D; margin-top: 5px; display: block;">${kuota.persentase}% dari kuota terpakai</small>
        </div>

        <div id="warningOverLimit" style="display: ${kuota.status === 'melebihi' ? 'block' : 'none'}; margin-top: 15px; padding: 12px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; color: #856404;">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>âš ï¸ PERHATIAN:</strong> <span id="warningText">Santri ini sudah melebihi kuota ${kuota.kuota_maksimal} hari per tahun! Total terpakai: ${kuota.total_terpakai} hari.</span>
        </div>
    `;
}

function getBadgeColor(badge) {
    const colors = {
        'success': '#28a745',
        'warning': '#ffc107',
        'danger': '#dc3545'
    };
    return colors[badge] || '#6c757d';
}

function getProgressColor(persentase) {
    if (persentase >= 100) return '#dc3545';
    if (persentase >= 80) return '#ffc107';
    return '#28a745';
}

// Calculate durasi when dates change
document.getElementById('tanggal_pulang').addEventListener('change', calculateDurasi);
document.getElementById('tanggal_kembali').addEventListener('change', calculateDurasi);

function calculateDurasi() {
    const tanggalPulang = document.getElementById('tanggal_pulang').value;
    const tanggalKembali = document.getElementById('tanggal_kembali').value;
    const durasiInfoDiv = document.getElementById('durasiInfo');
    
    if (!tanggalPulang || !tanggalKembali) {
        durasiInfoDiv.style.display = 'none';
        return;
    }
    
    const startDate = new Date(tanggalPulang);
    const endDate = new Date(tanggalKembali);
    
    if (endDate <= startDate) {
        durasiInfoDiv.style.display = 'none';
        return;
    }
    
    // Hitung durasi (termasuk hari pertama)
    const diffTime = Math.abs(endDate - startDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    
    durasiInfoDiv.style.display = 'block';
    
    // Update durasi
    document.getElementById('durasiHari').textContent = diffDays;
    
    let totalSetelah = diffDays;
    let sisaSetelah = <?php echo e($settings->kuota_maksimal); ?> - diffDays;
    let showWarning = false;
    let warningMessage = '';
    
    if (currentSantriData) {
        const currentUsage = currentSantriData.penggunaan_izin.total_terpakai;
        const kuotaMaks = currentSantriData.penggunaan_izin.kuota_maksimal;
        
        totalSetelah = currentUsage + diffDays;
        sisaSetelah = kuotaMaks - totalSetelah;
        
        // Update nilai
        document.getElementById('totalSetelahIzin').textContent = totalSetelah;
        document.getElementById('sisaKuotaSetelah').textContent = Math.max(0, sisaSetelah);
        
        // Ubah warna berdasarkan status
        const totalSetelahEl = document.getElementById('totalSetelahIzin');
        const sisaSetelahEl = document.getElementById('sisaKuotaSetelah');
        
        if (totalSetelah > kuotaMaks) {
            totalSetelahEl.style.color = '#dc3545';
            sisaSetelahEl.style.color = '#dc3545';
            showWarning = true;
            warningMessage = `Izin ini akan melebihi batas ${kuotaMaks} hari per tahun (Total setelah izin: ${totalSetelah} hari, Kelebihan: ${totalSetelah - kuotaMaks} hari)`;
            isOverLimit = true;
        } else if (totalSetelah >= kuotaMaks * 0.8) {
            totalSetelahEl.style.color = '#ff9800';
            sisaSetelahEl.style.color = '#ff9800';
            showWarning = true;
            warningMessage = `Perhatian: Kuota hampir habis! Sisa kuota setelah izin ini hanya ${sisaSetelah} hari.`;
            isOverLimit = false;
        } else {
            totalSetelahEl.style.color = '#2196f3';
            sisaSetelahEl.style.color = '#28a745';
            showWarning = false;
            isOverLimit = false;
        }
    }
    
    // Tampilkan warning durasi
    const warningDiv = document.getElementById('warningDurasi');
    const warningMessageEl = document.getElementById('warningDurasiMessage');
    if (showWarning) {
        warningDiv.style.display = 'block';
        warningMessageEl.textContent = warningMessage;
    } else {
        warningDiv.style.display = 'none';
    }
}

// Character counter (PERBAIKAN: Tidak ada validasi minimal)
document.getElementById('alasan').addEventListener('input', function() {
    const current = this.value.length;
    const counter = document.getElementById('charCount');
    counter.textContent = current;
    
    if (current > 500) {
        counter.style.color = 'red';
    } else {
        counter.style.color = '#7F8C8D';
    }
});

// Form submission with over limit confirmation
document.getElementById('kepulanganForm').addEventListener('submit', function(e) {
    if (isOverLimit) {
        e.preventDefault();
        
        const warningMessageEl = document.getElementById('warningDurasiMessage');
        const message = warningMessageEl ? warningMessageEl.textContent : 'Izin ini akan melebihi batas kuota per tahun';
        document.getElementById('overLimitMessage').textContent = message;
        
        document.getElementById('overLimitModal').style.display = 'flex';
    }
});

// Confirm over limit submission
document.getElementById('confirmOverLimit').addEventListener('click', function() {
    closeModal('overLimitModal');
    isOverLimit = false;
    document.getElementById('kepulanganForm').submit();
});

// Auto-set minimum tanggal_kembali
document.getElementById('tanggal_pulang').addEventListener('change', function() {
    const pulangDate = new Date(this.value);
    pulangDate.setDate(pulangDate.getDate() + 1);
    
    const minKembaliDate = pulangDate.toISOString().split('T')[0];
    document.getElementById('tanggal_kembali').min = minKembaliDate;
    
    const currentKembali = document.getElementById('tanggal_kembali').value;
    if (currentKembali && currentKembali <= this.value) {
        document.getElementById('tanggal_kembali').value = minKembaliDate;
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_pulang').min = today;
    
    const alasanField = document.getElementById('alasan');
    document.getElementById('charCount').textContent = alasanField.value.length;
    
    calculateDurasi();
});

// Form reset handler
document.querySelector('button[type="reset"]').addEventListener('click', function() {
    setTimeout(() => {
        document.getElementById('santriInfo').style.display = 'none';
        document.getElementById('durasiInfo').style.display = 'none';
        currentSantriData = null;
        isOverLimit = false;
        document.getElementById('charCount').textContent = '0';
    }, 100);
});

// Helper functions
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.fade').forEach(modal => modal.style.display = 'none');
    }
});

// Close modal on outside click
document.querySelectorAll('.modal.fade').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kepulangan/create.blade.php ENDPATH**/ ?>