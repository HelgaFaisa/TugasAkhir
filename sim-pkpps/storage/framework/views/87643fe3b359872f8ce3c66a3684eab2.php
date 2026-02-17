



<?php $__env->startSection('title', 'Data Kepulangan Santri'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-home"></i> Data Kepulangan Santri</h2>
</div>


<?php
    $pendingPengajuan = \App\Models\PengajuanKepulangan::where('status', 'Menunggu')->count();
?>

<?php if($pendingPengajuan > 0): ?>
    <div class="alert alert-warning" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: none; color: #000;">
        <i class="fas fa-bell" style="font-size: 2rem;"></i>
        <div style="flex: 1;">
            <strong style="font-size: 1.1rem;">Ada <?php echo e($pendingPengajuan); ?> pengajuan kepulangan dari mobile yang menunggu review!</strong>
            <p style="margin: 5px 0 0 0; opacity: 0.8;">Klik tombol di bawah untuk melihat dan meninjau pengajuan.</p>
        </div>
        <a href="<?php echo e(route('admin.kepulangan.pengajuan')); ?>" class="btn btn-dark" style="white-space: nowrap;">
            <i class="fas fa-mobile-alt"></i> Lihat Pengajuan
        </a>
    </div>
<?php endif; ?>


<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: center;">
        <div>
            <h4 style="margin: 0 0 5px 0; opacity: 0.9;">📅 Periode Kuota</h4>
            <p style="margin: 0; font-size: 1.1rem; font-weight: 600;">
                <?php echo e($settings->periode_mulai->format('d M Y')); ?> - <?php echo e($settings->periode_akhir->format('d M Y')); ?>

            </p>
        </div>
        <div>
            <h4 style="margin: 0 0 5px 0; opacity: 0.9;">📊 Kuota Maksimal</h4>
            <p style="margin: 0; font-size: 1.1rem; font-weight: 600;"><?php echo e($settings->kuota_maksimal); ?> Hari / Tahun</p>
        </div>
        <div>
            <h4 style="margin: 0 0 5px 0; opacity: 0.9;">🔄 Terakhir Reset</h4>
            <p style="margin: 0; font-size: 1.1rem; font-weight: 600;">
                <?php echo e($settings->terakhir_reset ? $settings->terakhir_reset->format('d M Y') : 'Belum Pernah'); ?>

            </p>
        </div>
        <div style="text-align: right;">
            <a href="<?php echo e(route('admin.kepulangan.settings')); ?>" class="btn btn-light" style="background: white; color: #667eea; font-weight: 600;">
                <i class="fas fa-cog"></i> Kelola Pengaturan
            </a>
        </div>
    </div>
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
        <h3>Over Limit (><?php echo e($settings->kuota_maksimal); ?> Hari)</h3>
        <div class="card-value"><?php echo e($stats['over_limit_santri']); ?></div>
        <i class="fas fa-exclamation-triangle card-icon"></i>
        <?php if($stats['over_limit_santri'] > 0): ?>
            <a href="<?php echo e(route('admin.kepulangan.over-limit')); ?>" style="font-size: 0.85rem; color: #dc3545; text-decoration: underline; margin-top: 5px; display: block;">
                Lihat Detail
            </a>
        <?php endif; ?>
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
            
            
            <a href="<?php echo e(route('admin.kepulangan.pengajuan')); ?>" class="btn btn-warning">
                <i class="fas fa-mobile-alt"></i> Pengajuan izin
                <?php if($pendingPengajuan > 0): ?>
                    <span class="badge" style="background: #dc3545; color: white; margin-left: 5px; padding: 3px 8px; border-radius: 10px; font-size: 0.75rem;">
                        <?php echo e($pendingPengajuan); ?>

                    </span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    
    <form method="GET" action="<?php echo e(route('admin.kepulangan.index')); ?>" id="filterForm" style="margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Cari nama, ID, atau alasan..." 
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
                    <th>Total Kuota Terpakai</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $kepulangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $isOverLimit = isset($santriOverLimit[$item->id_santri]);
                        $totalHariTerpakai = $isOverLimit ? $santriOverLimit[$item->id_santri] : 0;
                    ?>
                    <tr style="<?php echo e($isOverLimit ? 'background-color: rgba(220, 53, 69, 0.1); border-left: 4px solid #dc3545;' : ''); ?>">
                        <td>
                            <strong><?php echo e($item->id_kepulangan); ?></strong>
                            <?php if($isOverLimit): ?>
                                <span style="display: inline-block; background: #dc3545; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 5px; animation: pulse 2s infinite;" 
                                      title="Over Limit: <?php echo e($totalHariTerpakai); ?> hari">
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
                            <span style="display: inline-block; background: <?php echo e($item->durasi_izin > 7 ? '#ffc107' : '#6c757d'); ?>; color: <?php echo e($item->durasi_izin > 7 ? '#000' : '#fff'); ?>; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
                                <?php echo e($item->durasi_izin); ?> hari
                            </span>
                        </td>
                        <td>
                            <?php
                                $kuotaSantri = \App\Models\Kepulangan::getSisaKuotaSantri($item->id_santri);
                                $badgeColor = $kuotaSantri['badge_color'];
                                $badgeColors = [
                                    'success' => '#28a745',
                                    'warning' => '#ffc107',
                                    'danger' => '#dc3545'
                                ];
                                $bgColor = $badgeColors[$badgeColor] ?? '#6c757d';
                                $textColor = $badgeColor == 'warning' ? '#000' : '#fff';
                            ?>
                            <div style="text-align: center;">
                                <span style="display: inline-block; background: <?php echo e($bgColor); ?>; color: <?php echo e($textColor); ?>; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
                                    <?php echo e($kuotaSantri['total_terpakai']); ?> / <?php echo e($kuotaSantri['kuota_maksimal']); ?> hari
                                </span>
                                <div style="margin-top: 5px; font-size: 0.75rem; color: #7F8C8D;">
                                    <?php if($kuotaSantri['status'] === 'melebihi'): ?>
                                        <strong style="color: #dc3545;">OVER LIMIT</strong>
                                    <?php else: ?>
                                        Sisa: <?php echo e($kuotaSantri['sisa_kuota']); ?> hari (<?php echo e($kuotaSantri['persentase']); ?>%)
                                    <?php endif; ?>
                                </div>
                            </div>
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
                                <br><small style="color: #28a745; font-weight: 600;">🏠 Sedang Izin</small>
                            <?php elseif($item->is_terlambat): ?>
                                <br><small style="color: #dc3545; font-weight: 600;">⏰ Terlambat</small>
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
                                            onclick="completeKepulangan('<?php echo e($item->id_kepulangan); ?>', '<?php echo e($item->santri->nama_lengkap); ?>', '<?php echo e($item->tanggal_pulang->format('Y-m-d')); ?>', '<?php echo e($item->tanggal_kembali->format('Y-m-d')); ?>', <?php echo e($item->durasi_izin); ?>)"
                                            title="Selesaikan">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                <?php endif; ?>
                                
                                <?php if(in_array($item->status, ['Menunggu', 'Ditolak', 'Selesai'])): ?>
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
                        <td colspan="9" style="text-align: center; padding: 40px;">
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


<div class="modal fade" id="completeModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; padding: 20px;">
            <form id="completeForm">
                <?php echo csrf_field(); ?>
                <div style="margin-bottom: 20px;">
                    <h3 style="margin: 0; color: #2C3E50;">
                        <i class="fas fa-check-circle" style="color: #28a745;"></i> 
                        Selesaikan Kepulangan
                    </h3>
                </div>
                
                <div style="background: #E8F7F2; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #6FBA9D;">
                    <p style="margin: 5px 0;"><strong>ID Kepulangan:</strong> <span id="completeIdKepulangan"></span></p>
                    <p style="margin: 5px 0;"><strong>Santri:</strong> <span id="completeNamaSantri"></span></p>
                    <p style="margin: 5px 0;"><strong>Tanggal Pulang:</strong> <span id="completeTanggalPulang"></span></p>
                    <p style="margin: 5px 0;"><strong>Rencana Kembali:</strong> <span id="completeTanggalKembaliRencana"></span></p>
                    <p style="margin: 5px 0;"><strong>Durasi Rencana:</strong> <span id="completeDurasiRencana"></span> hari</p>
                </div>

                <div class="form-group">
                    <label for="tanggal_kembali_aktual">
                        <i class="fas fa-calendar-check"></i> 
                        Tanggal Kembali Aktual <span style="color: #dc3545;">*</span>
                    </label>
                    <input type="date" 
                           name="tanggal_kembali_aktual" 
                           id="tanggal_kembali_aktual" 
                           class="form-control" 
                           required>
                    <small style="color: #7F8C8D; margin-top: 5px; display: block;">
                        Masukkan tanggal santri kembali ke pesantren. Jika pulang lebih cepat, kuota akan disesuaikan otomatis.
                    </small>
                </div>

                <div id="durasiAktualInfo" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #007bff; display: none;">
                    <p style="margin: 0;"><strong>Durasi Aktual:</strong> <span id="durasiAktual" style="font-weight: 600; color: #007bff;">-</span> hari</p>
                    <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: #7F8C8D;" id="selisihInfo"></p>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('completeModal')">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Selesaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.modal.fade { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
.modal-dialog { max-width: 500px; width: 90%; margin: auto; }
.modal-content { max-height: 90vh; overflow-y: auto; }

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>

<script>
let currentActionId = null;

// Auto submit search dengan debounce
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

// Complete (Selesaikan Kepulangan)
let currentCompleteData = {};

function completeKepulangan(id, namaSantri, tanggalPulang, tanggalKembaliRencana, durasiRencana) {
    currentCompleteData = {
        id: id,
        namaSantri: namaSantri,
        tanggalPulang: tanggalPulang,
        tanggalKembaliRencana: tanggalKembaliRencana,
        durasiRencana: durasiRencana
    };
    
    // Populate modal
    document.getElementById('completeIdKepulangan').textContent = id;
    document.getElementById('completeNamaSantri').textContent = namaSantri;
    document.getElementById('completeTanggalPulang').textContent = formatTanggal(tanggalPulang);
    document.getElementById('completeTanggalKembaliRencana').textContent = formatTanggal(tanggalKembaliRencana);
    document.getElementById('completeDurasiRencana').textContent = durasiRencana;
    
    // Set default tanggal kembali aktual = hari ini
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_kembali_aktual').value = today;
    document.getElementById('tanggal_kembali_aktual').min = tanggalPulang;
    
    // Hitung durasi aktual
    calculateDurasiAktual();
    
    // Show modal
    document.getElementById('completeModal').style.display = 'flex';
}

// Calculate durasi aktual
function calculateDurasiAktual() {
    const tanggalPulang = currentCompleteData.tanggalPulang;
    const tanggalKembaliAktual = document.getElementById('tanggal_kembali_aktual').value;
    
    if (!tanggalKembaliAktual) return;
    
    const startDate = new Date(tanggalPulang);
    const endDate = new Date(tanggalKembaliAktual);
    
    if (endDate < startDate) {
        document.getElementById('durasiAktualInfo').style.display = 'none';
        return;
    }
    
    const diffTime = Math.abs(endDate - startDate);
    const durasiAktual = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    const durasiRencana = currentCompleteData.durasiRencana;
    
    document.getElementById('durasiAktual').textContent = durasiAktual;
    document.getElementById('durasiAktualInfo').style.display = 'block';
    
    // Show selisih
    let selisihText = '';
    let selisihColor = '#007bff';
    
    if (durasiAktual < durasiRencana) {
        const selisih = durasiRencana - durasiAktual;
        selisihText = `✅ Santri pulang ${selisih} hari lebih cepat dari rencana. Kuota akan berkurang ${durasiAktual} hari.`;
        selisihColor = '#28a745';
    } else if (durasiAktual > durasiRencana) {
        const selisih = durasiAktual - durasiRencana;
        selisihText = `⚠️ Santri pulang ${selisih} hari lebih lambat dari rencana. Kuota akan bertambah ${selisih} hari.`;
        selisihColor = '#ffc107';
    } else {
        selisihText = `✓ Sesuai rencana (${durasiAktual} hari).`;
        selisihColor = '#007bff';
    }
    
    const selisihInfo = document.getElementById('selisihInfo');
    selisihInfo.textContent = selisihText;
    selisihInfo.style.color = selisihColor;
    document.getElementById('durasiAktual').style.color = selisihColor;
}

// Event listener untuk tanggal kembali aktual
document.getElementById('tanggal_kembali_aktual')?.addEventListener('change', calculateDurasiAktual);

// Submit form complete
document.getElementById('completeForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    
    fetch(`/admin/kepulangan/${currentCompleteData.id}/complete`, {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('completeModal');
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 1500);
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

// Helper: Format tanggal
function formatTanggal(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
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