<?php $__env->startSection('title', 'Profil Saya'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h2><i class="fas fa-user-circle"></i> Profil Saya</h2>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<div class="content-box">

    
    <div class="detail-header">
        <div style="display: flex; align-items: center; gap: 20px;">
            <?php if($santri->foto && file_exists(public_path('storage/' . $santri->foto))): ?>
                <img src="<?php echo e(asset('storage/' . $santri->foto)); ?>"
                     alt="Foto <?php echo e($santri->nama_lengkap); ?>"
                     style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-color); flex-shrink: 0;"
                     loading="lazy"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div style="width:80px;height:80px;border-radius:50%;background:var(--primary-color);display:none;align-items:center;justify-content:center;color:white;font-size:2rem;font-weight:bold;flex-shrink:0;">
                    <?php echo e(strtoupper(substr($santri->nama_lengkap, 0, 1))); ?>

                </div>
            <?php else: ?>
                <div style="width:80px;height:80px;border-radius:50%;background:var(--primary-color);display:flex;align-items:center;justify-content:center;color:white;font-size:2rem;font-weight:bold;flex-shrink:0;">
                    <?php echo e(strtoupper(substr($santri->nama_lengkap, 0, 1))); ?>

                </div>
            <?php endif; ?>

            <div>
                <h3 style="margin:0; font-size:1.8rem; color:var(--text-color);">
                    <?php echo e($santri->nama_lengkap); ?>

                </h3>
                <p style="margin:5px 0 0 0; color:var(--text-light);">
                    <i class="fas fa-id-badge"></i> <?php echo e($santri->id_santri); ?>

                    <?php if($santri->nis): ?>
                        | <i class="fas fa-barcode"></i> NIS: <?php echo e($santri->nis); ?>

                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <hr style="border:none; border-top:2px solid #E8F7F2; margin:25px 0;">

    
    <div class="detail-section">
        <h4><i class="fas fa-id-card"></i> Informasi Dasar</h4>
        <table class="detail-table">
            <tr>
                <th width="200"><i class="fas fa-id-badge"></i> ID Santri</th>
                <td><strong><?php echo e($santri->id_santri); ?></strong></td>
            </tr>
            <?php if($santri->nis): ?>
            <tr>
                <th><i class="fas fa-barcode"></i> NIS</th>
                <td><?php echo e($santri->nis); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <th><i class="fas fa-user"></i> Nama Lengkap</th>
                <td><strong><?php echo e($santri->nama_lengkap); ?></strong></td>
            </tr>
            <tr>
                <th><i class="fas fa-venus-mars"></i> Jenis Kelamin</th>
                <td>
                    <?php if($santri->jenis_kelamin == 'Laki-laki'): ?>
                        <i class="fas fa-mars" style="color:#81C6E8;"></i> <?php echo e($santri->jenis_kelamin); ?>

                    <?php else: ?>
                        <i class="fas fa-venus" style="color:#FF8B94;"></i> <?php echo e($santri->jenis_kelamin); ?>

                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-school"></i> Kelas</th>
                <td>
                    <?php if($santri->kelasSantri && $santri->kelasSantri->count() > 0): ?>
                        <?php
                            $grouped = $santri->kelasSantri
                                ->filter(fn($sk) => $sk->kelas && $sk->kelas->kelompok)
                                ->groupBy(fn($sk) => $sk->kelas->kelompok->nama_kelompok)
                                ->sortBy(fn($items) => $items->first()->kelas->kelompok->urutan ?? 99);
                        ?>
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompokName => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div style="padding:12px; background:linear-gradient(135deg,#F8FBF9,#E8F7F2); border-radius:8px; border-left:4px solid #6FBA9D;">
                                    <div style="font-weight:600; color:#2C5F4F; margin-bottom:8px;">
                                        <i class="fas fa-layer-group"></i> <?php echo e($kelompokName); ?>

                                    </div>
                                    <div style="display:flex; flex-direction:column; gap:6px;">
                                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <span style="width:8px;height:8px;background:#6FBA9D;border-radius:50%;flex-shrink:0;"></span>
                                                <strong style="color:#555; font-size:0.95rem;"><?php echo e($sk->kelas->nama_kelas); ?></strong>
                                                <?php if($sk->is_primary): ?>
                                                    <span style="padding:2px 8px; border-radius:4px; font-size:0.72rem; font-weight:600; background:#FFF3CD; color:#856404;">
                                                        <i class="fas fa-star"></i> Utama
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <span class="text-muted"><em>Belum Ada Kelas</em></span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-info-circle"></i> Status</th>
                <td>
                    <?php if($santri->status == 'Aktif'): ?>
                        <span style="padding:6px 12px; border-radius:6px; font-size:0.85rem; font-weight:600; background:linear-gradient(135deg,#E8F7F2,#D4F1E3); color:#2C5F4F; display:inline-block;">
                            <i class="fas fa-check-circle"></i> Aktif
                        </span>
                    <?php elseif($santri->status == 'Lulus'): ?>
                        <span style="padding:6px 12px; border-radius:6px; font-size:0.85rem; font-weight:600; background:linear-gradient(135deg,#E3F2FD,#D1E9F9); color:#2D4A7C; display:inline-block;">
                            <i class="fas fa-graduation-cap"></i> Lulus
                        </span>
                    <?php else: ?>
                        <span style="padding:6px 12px; border-radius:6px; font-size:0.85rem; font-weight:600; background:linear-gradient(135deg,#E8ECF0,#D1D8E0); color:#555; display:inline-block;">
                            <i class="fas fa-times-circle"></i> <?php echo e($santri->status); ?>

                        </span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><i class="fas fa-calendar-plus"></i> Terdaftar Sejak</th>
                <td>
                    <?php echo e($santri->created_at->format('d M Y')); ?>

                    <span style="color:#7F8C8D; font-size:0.85rem;">(<?php echo e($santri->created_at->diffForHumans()); ?>)</span>
                </td>
            </tr>
        </table>
    </div>

    
    <div class="detail-section">
        <h4><i class="fas fa-map-marker-alt"></i> Alamat & Asal</h4>
        <table class="detail-table">
            <tr>
                <th width="200"><i class="fas fa-map-marker-alt"></i> Alamat Lengkap</th>
                <td><?php echo e($santri->alamat_santri ?? '-'); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-map"></i> Daerah Asal</th>
                <td>
                    <?php if($santri->daerah_asal): ?>
                        <i class="fas fa-map-pin" style="color:#6FBA9D;"></i> <?php echo e($santri->daerah_asal); ?>

                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    
    <div class="detail-section">
        <h4><i class="fas fa-users"></i> Data Orang Tua / Wali</h4>
        <table class="detail-table">
            <tr>
                <th width="200"><i class="fas fa-user-tie"></i> Nama Orang Tua</th>
                <td><?php echo e($santri->nama_orang_tua ?? '-'); ?></td>
            </tr>
            <tr>
                <th><i class="fas fa-phone"></i> Nomor HP Orang Tua</th>
                <td>
                    <?php if($santri->nomor_hp_ortu): ?>
                        <i class="fas fa-phone-alt" style="color:#6FBA9D;"></i>
                        <a href="tel:<?php echo e($santri->nomor_hp_ortu); ?>" style="color:#6FBA9D; text-decoration:none;">
                            <?php echo e($santri->nomor_hp_ortu); ?>

                        </a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    
    <div class="detail-section">
        <h4><i class="fas fa-id-card"></i> Kartu RFID</h4>
        <?php if($santri->rfid_uid): ?>
            <div style="padding:14px; background:linear-gradient(135deg,#F8FBF9,#E8F7F2); border-radius:8px; border-left:4px solid #6FBA9D;">
                <p style="margin:0;">
                    <i class="fas fa-check-circle" style="color:#6FBA9D;"></i>
                    <strong>Kartu RFID sudah terdaftar</strong>
                </p>
                <p style="margin-top:8px; font-size:0.9rem;">
                    UID: <code style="background:white; padding:4px 8px; border-radius:4px; font-weight:600;"><?php echo e($santri->rfid_uid); ?></code>
                </p>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Kartu RFID belum terdaftar. Silakan hubungi admin untuk pendaftaran kartu.
            </div>
        <?php endif; ?>
    </div>

    
    <div style="margin-top:22px; padding:14px; background:linear-gradient(135deg,#F8FBF9,#E8F7F2); border-radius:8px; border-left:4px solid #6FBA9D;">
        <p style="margin:0; color:#2C5F4F; font-size:0.9rem;">
            <i class="fas fa-info-circle"></i>
            <strong>Catatan:</strong> Jika ada data yang perlu diperbarui atau diperbaiki, silakan hubungi admin atau pengurus pesantren.
        </p>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/profil/index.blade.php ENDPATH**/ ?>