



<?php $__env->startSection('title', 'Data Kepulangan Santri'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-home"></i> Data Kepulangan Santri</h2>
</div>


<div class="row-cards">
    <div class="card card-info">
        <h3>Total Data</h3>
        <div class="card-value"><?php echo e($stats['total_data']); ?></div>
        <i class="fas fa-clipboard-list card-icon"></i>
    </div>
    <div class="card card-warning">
        <h3>Menunggu Approval</h3>
        <div class="card-value"><?php echo e($stats['menunggu_approval']); ?></div>
        <i class="fas fa-clock card-icon"></i>
    </div>
    <div class="card card-success">
        <h3>Sedang Izin</h3>
        <div class="card-value"><?php echo e($stats['sedang_izin']); ?></div>
        <i class="fas fa-home card-icon"></i>
    </div>
    <div class="card card-danger">
        <h3>Over Limit</h3>
        <div class="card-value"><?php echo e($stats['over_limit_santri']); ?></div>
        <i class="fas fa-exclamation-triangle card-icon"></i>
    </div>
</div>


<?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>


<div class="content-box">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="<?php echo e(route('admin.kepulangan.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Izin Kepulangan
            </a>
        </div>
    </div>

    
    <form method="GET" action="<?php echo e(route('admin.kepulangan.index')); ?>" id="filterForm" style="margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Nama, ID, atau alasan..." 
                       value="<?php echo e(request('search')); ?>"
                       id="searchInput">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="status" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Semua Status</option>
                    <option value="Menunggu" <?php echo e(request('status') == 'Menunggu' ? 'selected' : ''); ?>>Menunggu</option>
                    <option value="Disetujui" <?php echo e(request('status') == 'Disetujui' ? 'selected' : ''); ?>>Disetujui</option>
                    <option value="Ditolak" <?php echo e(request('status') == 'Ditolak' ? 'selected' : ''); ?>>Ditolak</option>
                    <option value="Selesai" <?php echo e(request('status') == 'Selesai' ? 'selected' : ''); ?>>Selesai</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="tahun" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Semua Tahun</option>
                    <?php $__currentLoopData = $tahunList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($tahun); ?>" <?php echo e(request('tahun') == $tahun ? 'selected' : ''); ?>>
                            <?php echo e($tahun); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <select name="bulan" class="form-control" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Semua Bulan</option>
                    <?php for($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo e($i); ?>" <?php echo e(request('bulan') == $i ? 'selected' : ''); ?>>
                            <?php echo e(\Carbon\Carbon::create()->month($i)->format('F')); ?>

                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="<?php echo e(route('admin.kepulangan.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
    </form>

    
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Santri</th>
                    <th>Tanggal Pulang</th>
                    <th>Tanggal Kembali</th>
                    <th>Durasi</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $kepulangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr style="<?php echo e(isset($santriOverLimit[$item->id_santri]) ? 'background-color: #fff3cd;' : ''); ?>">
                        <td>
                            <strong><?php echo e($item->id_kepulangan); ?></strong>
                            <?php if(isset($santriOverLimit[$item->id_santri])): ?>
                                <span style="display: inline-block; background: #dc3545; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 5px;" 
                                      title="Over Limit: <?php echo e($santriOverLimit[$item->id_santri]); ?> hari">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div>
                                <strong><?php echo e($item->santri->nama_lengkap ?? 'N/A'); ?></strong><br>
                                <small style="color: #7F8C8D;">
                                    <?php echo e($item->santri->id_santri ?? ''); ?> | <?php echo e($item->santri->kelas ?? ''); ?>

                                </small>
                            </div>
                        </td>
                        <td><?php echo e($item->tanggal_pulang_formatted); ?></td>
                        <td><?php echo e($item->tanggal_kembali_formatted); ?></td>
                        <td>
                            <span style="display: inline-block; background: <?php echo e($item->durasi_izin_calculated > 7 ? '#ffc107' : '#6c757d'); ?>; color: <?php echo e($item->durasi_izin_calculated > 7 ? '#000' : '#fff'); ?>; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">
                                <?php echo e($item->durasi_izin_calculated); ?> hari
                            </span>
                        </td>
                        <td>
                            <span style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block;" title="<?php echo e($item->alasan); ?>">
                                <?php echo e($item->alasan); ?>

                            </span>
                        </td>
                        <td>
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;
                                <?php if($item->status == 'Menunggu'): ?> background: #ffc107; color: #000;
                                <?php elseif($item->status == 'Disetujui'): ?> background: #28a745; color: white;
                                <?php elseif($item->status == 'Ditolak'): ?> background: #dc3545; color: white;
                                <?php else: ?> background: #6c757d; color: white;
                                <?php endif; ?>">
                                <?php echo e($item->status); ?>

                            </span>
                            <?php if($item->is_aktif): ?>
                                <br><small style="color: #28a745; font-weight: 600;">Sedang Izin</small>
                            <?php elseif($item->is_terlambat): ?>
                                <br><small style="color: #dc3545; font-weight: 600;">Terlambat</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div style="display: flex; gap: 5px; justify-content: center; flex-wrap: wrap;">
                                <a href="<?php echo e(route('admin.kepulangan.show', $item->id_kepulangan)); ?>" 
                                   class="btn btn-sm btn-primary" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <?php if($item->status == 'Menunggu'): ?>
                                    <a href="<?php echo e(route('admin.kepulangan.edit', $item->id_kepulangan)); ?>" 
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-success" 
                                            onclick="approveKepulangan('<?php echo e($item->id_kepulangan); ?>')"
                                            title="Setujui">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="rejectKepulangan('<?php echo e($item->id_kepulangan); ?>')"
                                            title="Tolak">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php endif; ?>
                                
                                <?php if($item->status == 'Disetujui'): ?>
                                    <a href="<?php echo e(route('admin.kepulangan.print', $item->id_kepulangan)); ?>" 
                                       class="btn btn-sm btn-secondary" 
                                       target="_blank" title="Cetak Surat">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-success" 
                                            onclick="completeKepulangan('<?php echo e($item->id_kepulangan); ?>')"
                                            title="Selesaikan">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                <?php endif; ?>
                                
                                <?php if(in_array($item->status, ['Menunggu', 'Ditolak'])): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="deleteKepulangan('<?php echo e($item->id_kepulangan); ?>')"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                            <p style="color: #7F8C8D;">Tidak ada data kepulangan ditemukan</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <?php if($kepulangan->hasPages()): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; flex-wrap: wrap; gap: 15px;">
            <div>
                Menampilkan <?php echo e($kepulangan->firstItem() ?? 0); ?> - <?php echo e($kepulangan->lastItem() ?? 0); ?> 
                dari <?php echo e($kepulangan->total()); ?> data
            </div>
            <div>
                <?php echo e($kepulangan->appends(request()->query())->links()); ?>

            </div>
        </div>
    <?php endif; ?>
</div>


<div class="modal fade" id="approveModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 20px;">
            <form id="approveForm">
                <?php echo csrf_field(); ?>
                <div style="margin-bottom: 20px;">
                    <h3 style="margin: 0; color: #2C3E50;">Setujui Izin Kepulangan</h3>
                </div>
                <div class="form-group">
                    <label>Catatan (Opsional):</label>
                    <textarea name="catatan" class="form-control" rows="3" 
                              placeholder="Tambahkan catatan untuk persetujuan ini..."></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('approveModal')">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="rejectModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 20px;">
            <form id="rejectForm">
                <?php echo csrf_field(); ?>
                <div style="margin-bottom: 20px;">
                    <h3 style="margin: 0; color: #2C3E50;">Tolak Izin Kepulangan</h3>
                </div>
                <div class="form-group">
                    <label>Alasan Penolakan: <span style="color: #dc3545;">*</span></label>
                    <textarea name="alasan_penolakan" class="form-control" rows="3" 
                              placeholder="Jelaskan alasan penolakan..." required></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('rejectModal')">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 20px;">
            <div style="margin-bottom: 20px;">
                <h3 style="margin: 0; color: #2C3E50;">Konfirmasi Hapus</h3>
            </div>
            <p>Apakah Anda yakin ingin menghapus data kepulangan ini?</p>
            <p style="color: #dc3545; font-size: 0.9rem;">Data yang sudah dihapus tidak dapat dikembalikan.</p>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.modal.fade { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
.modal-dialog { max-width: 500px; width: 90%; margin: auto; }
.modal-content { max-height: 90vh; overflow-y: auto; }
</style>

<script>
let currentActionId = null;

// Auto submit search with debounce
let searchTimeout;
document.getElementById('searchInput')?.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('filterForm').submit();
    }, 500);
});

// Approve
function approveKepulangan(id) {
    currentActionId = id;
    document.getElementById('approveModal').style.display = 'flex';
}

document.getElementById('approveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    
    fetch(`/admin/kepulangan/${currentActionId}/approve`, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('approveModal');
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => showAlert('danger', 'Error: ' + error.message))
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Reject
function rejectKepulangan(id) {
    currentActionId = id;
    document.getElementById('rejectModal').style.display = 'flex';
}

document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    
    fetch(`/admin/kepulangan/${currentActionId}/reject`, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('rejectModal');
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => showAlert('danger', 'Error: ' + error.message))
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Complete
function completeKepulangan(id) {
    if (confirm('Apakah Anda yakin ingin menandai kepulangan ini sebagai selesai?')) {
        fetch(`/admin/kepulangan/${id}/complete`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => showAlert('danger', 'Error: ' + error.message));
    }
}

// Delete
function deleteKepulangan(id) {
    currentActionId = id;
    document.getElementById('deleteModal').style.display = 'flex';
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    const btn = this;
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
    
    fetch(`/admin/kepulangan/${currentActionId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('deleteModal');
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => showAlert('danger', 'Error: ' + error.message))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});

// Helper functions
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i> ${message}`;
    
    const pageHeader = document.querySelector('.page-header');
    pageHeader.insertAdjacentElement('afterend', alertDiv);
    
    setTimeout(() => alertDiv.remove(), 5000);
}

// Close modals on ESC
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
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/admin/kepulangan/index.blade.php ENDPATH**/ ?>