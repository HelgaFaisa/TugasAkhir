<?php $__env->startSection('title', 'Dashboard Santri'); ?>

<?php $__env->startSection('content'); ?>
<style>
*,*::before,*::after{box-sizing:border-box}
:root{
    --ink:#1A1F2E; --ink2:#4A5568; --ink3:#8A97AD;
    --surf:#F7F8FC; --white:#fff; --bdr:#E8ECF4;
    --green:#22C97E; --gbg:#EDFAF4; --gmid:#9CEACE;
    --blue:#3B82F6;  --bbg:#EFF6FF;
    --amber:#F59E0B; --abg:#FFFBEB;
    --red:#EF4444;   --rbg:#FEF2F2;
    --teal:#14B8A6;  --tbg:#F0FDFA;
    --purple:#8B5CF6; --pbg:#F5F3FF;
    --r:15px; --rlg:20px; --rsm:9px;
    --s0:0 1px 3px rgba(26,31,46,.07),0 1px 2px rgba(26,31,46,.04);
    --s1:0 4px 16px rgba(26,31,46,.09),0 1px 4px rgba(26,31,46,.04);
    --fn:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
}
.sd{font-family:var(--fn);color:var(--ink);padding-bottom:48px}
/* hero */
.sd-hero{background:var(--white);border:1px solid var(--bdr);border-radius:var(--rlg);padding:22px 26px;margin-bottom:16px;display:flex;align-items:center;gap:16px;position:relative;overflow:hidden;box-shadow:var(--s1)}
.sd-hero::before{content:"";position:absolute;inset:0;pointer-events:none;background:linear-gradient(135deg,rgba(34,201,126,.07) 0%,rgba(59,130,246,.04) 60%,transparent 100%)}
.sd-av{width:52px;height:52px;border-radius:50%;flex-shrink:0;background:linear-gradient(135deg,#22C97E,#14B8A6);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.3rem;color:#fff;position:relative;z-index:1;box-shadow:0 4px 12px rgba(34,201,126,.35)}
.sd-ht{flex:1;position:relative;z-index:1}
.sd-ht h1{font-size:1.18rem;font-weight:800;color:var(--ink);margin:0 0 4px}
.sd-ht p{font-size:.85rem;color:var(--ink2);margin:0;display:flex;align-items:center;gap:7px;flex-wrap:wrap}
.sd-sem{display:inline-flex;align-items:center;gap:4px;background:var(--gbg);color:#059862;border:1px solid var(--gmid);padding:2px 9px;border-radius:20px;font-size:.72rem;font-weight:700}
.sd-hd{text-align:right;font-size:.79rem;color:var(--ink3);line-height:1.7;position:relative;z-index:1}
.sd-hd strong{color:var(--ink2);display:block;font-size:.87rem;font-weight:700}
/* alert */
.sd-alert{display:flex;align-items:flex-start;gap:11px;padding:12px 16px;border-radius:var(--r);margin-bottom:9px;border:1px solid transparent;font-size:.86rem;line-height:1.5}
.sd-alert.red{background:var(--rbg);border-color:#FCA5A5;color:#991B1B}
.sd-alert.blue{background:var(--bbg);border-color:#93C5FD;color:#1D4ED8}
.sd-alert a{color:inherit;font-weight:700;text-decoration:underline}
/* section label */
.sd-sec{font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ink3);margin:22px 0 11px;display:flex;align-items:center;gap:8px}
.sd-sec::after{content:"";flex:1;height:1px;background:var(--bdr)}
/* kpi */
.sd-kpi-g{display:grid;grid-template-columns:repeat(auto-fit,minmax(195px,1fr));gap:11px}
.sd-kpi{background:var(--white);border:1px solid var(--bdr);border-radius:var(--r);padding:17px;position:relative;overflow:hidden;box-shadow:var(--s0);transition:box-shadow .2s,transform .2s}
.sd-kpi:hover{box-shadow:var(--s1);transform:translateY(-2px)}
.sd-kpi-top{position:absolute;top:0;left:0;right:0;height:3px;border-radius:var(--r) var(--r) 0 0}
.sd-kpi-ico{width:35px;height:35px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:.92rem;margin-bottom:11px}
.sd-kpi-val{font-size:1.55rem;font-weight:800;line-height:1;margin-bottom:3px}
.sd-kpi-lbl{font-size:.77rem;color:var(--ink2);font-weight:500}
.sd-kpi-ft{margin-top:7px}
.kv-g .sd-kpi-top{background:var(--green)}.kv-g .sd-kpi-ico{background:var(--gbg);color:#059862}.kv-g .sd-kpi-val{color:#059862}
.kv-b .sd-kpi-top{background:var(--blue)}.kv-b .sd-kpi-ico{background:var(--bbg);color:var(--blue)}.kv-b .sd-kpi-val{color:var(--blue)}
.kv-a .sd-kpi-top{background:var(--amber)}.kv-a .sd-kpi-ico{background:var(--abg);color:#B45309}.kv-a .sd-kpi-val{color:#B45309}
.kv-r .sd-kpi-top{background:var(--red)}.kv-r .sd-kpi-ico{background:var(--rbg);color:var(--red)}.kv-r .sd-kpi-val{color:var(--red)}
.kv-t .sd-kpi-top{background:var(--teal)}.kv-t .sd-kpi-ico{background:var(--tbg);color:#0F766E}.kv-t .sd-kpi-val{color:#0F766E;font-size:.9rem;padding-top:3px;line-height:1.3}
/* link btn */
.sd-a{display:inline-flex;align-items:center;gap:4px;font-size:.72rem;font-weight:700;color:var(--blue);text-decoration:none;padding:3px 9px;border-radius:7px;background:var(--bbg);transition:background .15s;white-space:nowrap}
.sd-a:hover{background:#DBEAFE;text-decoration:none}
.sd-a.g{color:#059862;background:var(--gbg)}.sd-a.g:hover{background:#D1FAE5}
.sd-a.t{color:#0F766E;background:var(--tbg)}.sd-a.t:hover{background:#CCFBF1}
.sd-a.p{color:var(--purple);background:var(--pbg)}.sd-a.p:hover{background:#EDE9FE}
/* card */
.sd-card{background:var(--white);border:1px solid var(--bdr);border-radius:var(--r);box-shadow:var(--s0);overflow:hidden}
.sd-ch{padding:13px 18px 11px;display:flex;align-items:center;justify-content:space-between;gap:8px;border-bottom:1px solid var(--bdr)}
.sd-ch h3{font-size:.89rem;font-weight:700;margin:0;color:var(--ink);display:flex;align-items:center;gap:6px}
/* table */
.sd-tbl{width:100%;border-collapse:collapse}
.sd-tbl th,.sd-tbl td{padding:9px 18px;font-size:.8rem;text-align:left;border-bottom:1px solid var(--bdr)}
.sd-tbl th{color:var(--ink3);font-weight:600;background:var(--surf)}
.sd-tbl tr:last-child td{border-bottom:none}
/* pill */
.sd-pill{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:20px;font-size:.7rem;font-weight:700;white-space:nowrap}
.p-g{background:var(--gbg);color:#059862}.p-b{background:var(--bbg);color:#1D4ED8}
.p-a{background:var(--abg);color:#B45309}.p-r{background:var(--rbg);color:#991B1B}
/* kepulangan */
.sd-kq-nums{display:flex;gap:22px;flex-wrap:wrap;margin-bottom:14px}
.sd-kq-num .v{font-size:1.45rem;font-weight:800;line-height:1}
.sd-kq-num .s{font-size:.74rem;color:var(--ink2);margin-top:3px}
.sd-qbar{height:10px;background:var(--bdr);border-radius:99px;overflow:hidden}
.sd-qfill{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--green),var(--teal));transition:width .7s ease}
.sd-qfill.w{background:linear-gradient(90deg,var(--amber),#F97316)}
.sd-qfill.o{background:linear-gradient(90deg,var(--red),#DC2626)}
.sd-qmeta{display:flex;justify-content:space-between;font-size:.73rem;color:var(--ink2);margin-top:4px}
/* 2-col */
.sd-2{display:grid;grid-template-columns:1fr 1fr;gap:13px}
/* berita */
.sd-bitem{display:flex;align-items:flex-start;gap:11px;padding:12px 18px;border-bottom:1px solid var(--bdr);text-decoration:none;transition:background .15s}
.sd-bitem:last-child{border-bottom:none}
.sd-bitem:hover{background:var(--surf);text-decoration:none}
.sd-bdot{width:7px;height:7px;border-radius:50%;background:var(--green);flex-shrink:0;margin-top:5px}
.sd-btit{font-size:.83rem;font-weight:600;color:var(--ink);margin-bottom:2px;line-height:1.4}
.sd-bmeta{font-size:.71rem;color:var(--ink3)}
/* STATUS INPUT CAPAIAN */
.sd-ci-top{display:flex;align-items:flex-start;gap:12px;padding:15px 18px;border-bottom:1px solid var(--bdr)}
.sd-ci-badge{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:99px;font-size:.77rem;font-weight:800;white-space:nowrap}
.ci-buka{background:var(--gbg);color:#059862;border:1px solid var(--gmid)}
.ci-segera{background:var(--abg);color:#B45309;border:1px solid #FDE68A}
.ci-tutup{background:var(--surf);color:#64748B;border:1px solid #CBD5E1}
.ci-pulse{width:8px;height:8px;border-radius:50%;background:#22C97E;flex-shrink:0;animation:cpulse 1.4s infinite}
@keyframes cpulse{0%,100%{box-shadow:0 0 0 0 rgba(34,201,126,.5)}50%{box-shadow:0 0 0 5px rgba(34,201,126,0)}}
.sd-ci-prog{padding:13px 18px 6px}
.sd-ci-bar{height:8px;background:var(--bdr);border-radius:99px;overflow:hidden;margin:7px 0 4px}
.sd-ci-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,#3B82F6,#6366F1);transition:width .9s ease}
.sd-ci-cats{display:flex;flex-direction:column;gap:8px;padding:6px 18px 16px}
.sd-ci-cat{display:flex;align-items:center;gap:8px}
.sd-ci-cat-name{font-size:.75rem;color:var(--ink2);font-weight:500;display:flex;align-items:center;gap:5px;min-width:80px}
.sd-ci-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.sd-ci-cat-bar{flex:1;height:5px;background:var(--bdr);border-radius:99px;overflow:hidden}
.sd-ci-cat-fill{height:100%;border-radius:99px;transition:width .8s ease}
.sd-ci-cat-pct{font-size:.75rem;font-weight:800;min-width:36px;text-align:right}
/* GROUPED BAR KEHADIRAN */
.sd-bar-body{padding:16px 20px 20px}
.sd-bar-legend{display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:12px}
.sd-bar-leg-item{display:flex;align-items:center;gap:5px;font-size:.73rem;font-weight:600;color:var(--ink2)}
.sd-bar-leg-dot{width:10px;height:10px;border-radius:3px;flex-shrink:0}
.sd-bar-empty{height:180px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;color:var(--ink3)}
/* toggle */
.sd-toggle{display:flex;background:var(--surf);border:1px solid var(--bdr);border-radius:8px;padding:2px;gap:2px}
.sd-toggle-btn{padding:4px 12px;border-radius:6px;font-size:.72rem;font-weight:700;color:var(--ink3);cursor:pointer;border:none;background:transparent;transition:all .15s;white-space:nowrap}
.sd-toggle-btn.active{background:var(--white);color:var(--ink);box-shadow:0 1px 4px rgba(26,31,46,.1)}


/* responsive */
@media(max-width:700px){
    .sd-2{grid-template-columns:1fr}
    .sd-kpi-g{grid-template-columns:1fr 1fr}
    .sd-hero{flex-wrap:wrap}.sd-hd{display:none}
}
@media(max-width:420px){.sd-kpi-g{grid-template-columns:1fr}}
</style>

<?php
    $initials    = strtoupper(substr($data['nama_santri'], 0, 1));
    $poin        = $data['poin_pelanggaran'];
    $avgCap      = round(($data['progres_quran'] + $data['progres_hadist'] + $data['progres_materi_tambahan']) / 3, 1);
    $kpTotalHari = $statistikKepulangan['total_hari']   ?? 0;
    $kpSisa      = $statistikKepulangan['sisa_kuota']   ?? 12;
    $kpPersen    = $statistikKepulangan['persen_kuota'] ?? 0;
    $kpDisetujui = $statistikKepulangan['disetujui']    ?? 0;
    $kpMenunggu  = $statistikKepulangan['menunggu']     ?? 0;
    $kpOver      = $statistikKepulangan['over_limit']   ?? false;

    // Grouped Bar Kehadiran per Kategori
    // Bar kehadiran — bulan ini
    $barKategori = $absensiPerKategori['labels']  ?? [];
    $barHadir    = $absensiPerKategori['hadir']   ?? [];
    $barAlpa     = $absensiPerKategori['alpa']    ?? [];
    $barIzin     = $absensiPerKategori['izin']    ?? [];
    $barSakit    = $absensiPerKategori['sakit']   ?? [];
    // Bar kehadiran — minggu ini
    $barKatMgg   = $absensiPerKategoriMinggu['labels'] ?? $barKategori;
    $barHadirMgg = $absensiPerKategoriMinggu['hadir']  ?? [];
    $barAlpaMgg  = $absensiPerKategoriMinggu['alpa']   ?? [];
    $barIzinMgg  = $absensiPerKategoriMinggu['izin']   ?? [];
    $barSakitMgg = $absensiPerKategoriMinggu['sakit']  ?? [];
    $bulanNama   = now()->locale('id')->isoFormat('MMMM YYYY');
    $mingguLabel = 'Minggu ini (' . now()->startOfWeek(\Carbon\Carbon::MONDAY)->locale('id')->isoFormat('D MMM') . ')';

    // Status Input Capaian
    $ciIsOpen   = $statusInputCapaian['is_open']      ?? false;
    $ciDeadline = $statusInputCapaian['deadline']     ?? null;
    $ciSudah    = $statusInputCapaian['sudah_input']  ?? 0;
    $ciTotal    = $statusInputCapaian['total_materi'] ?? 0;
    $ciPersen   = $ciTotal > 0 ? round($ciSudah / $ciTotal * 100) : 0;
    $ciSegera   = $ciIsOpen && $ciDeadline
        && \Carbon\Carbon::parse($ciDeadline)->isFuture()
        && \Carbon\Carbon::parse($ciDeadline)->diffInDays(now()) <= 7;
    $ciBadge    = $ciIsOpen ? ($ciSegera ? 'ci-segera' : 'ci-buka') : 'ci-tutup';
    $ciBLabel   = $ciIsOpen ? ($ciSegera ? '⚠ Segera Tutup' : '✓ Input Dibuka') : '✕ Input Ditutup';

    // Capaian per kategori (progress bar kecil di kartu input)
    $capKat = [
        ["Al-Qur'an", $data['progres_quran'],           '#22C97E'],
        ['Hadist',     $data['progres_hadist'],          '#3B82F6'],
        ['Tambahan',   $data['progres_materi_tambahan'], '#F59E0B'],
    ];


?>

<div class="sd">


<div class="sd-hero">
    <div class="sd-av"><?php echo e($initials); ?></div>
    <div class="sd-ht">
        <h1>Halo, <?php echo e(Str::words($data['nama_santri'], 2)); ?> &#128075;</h1>
        <p>
            Kelas <?php echo e($data['kelas']); ?>

            <?php if($semesterAktif): ?>
            <span class="sd-sem"><i class="fas fa-calendar-check"></i> <?php echo e($semesterAktif->nama_semester); ?></span>
            <?php endif; ?>
        </p>
    </div>
    <div class="sd-hd">
        <strong><?php echo e(now()->locale('id')->isoFormat('dddd')); ?></strong>
        <?php echo e(now()->locale('id')->isoFormat('D MMMM YYYY')); ?>

    </div>
</div>


<?php if(isset($statusKesehatan) && $statusKesehatan): ?>
<div class="sd-alert red">
    <span>&#127973;</span>
    <div>
        <strong>Sedang dalam perawatan UKP</strong> sejak <?php echo e($statusKesehatan->tanggal_masuk_formatted); ?>

        (<?php echo e($statusKesehatan->lama_dirawat); ?> hari). Keluhan: <em><?php echo e($statusKesehatan->keluhan); ?></em>.
        <a href="<?php echo e(route('santri.kesehatan.index')); ?>">Lihat detail &rarr;</a>
    </div>
</div>
<?php endif; ?>
<?php if(isset($kepulanganAktif) && $kepulanganAktif): ?>
<div class="sd-alert blue">
    <span>&#127968;</span>
    <div>
        <strong>Sedang dalam masa kepulangan</strong>
        (<?php echo e($kepulanganAktif->tanggal_pulang_formatted); ?> &ndash; <?php echo e($kepulanganAktif->tanggal_kembali_formatted); ?>).
        Pastikan kembali tepat waktu!
        <a href="<?php echo e(route('santri.kepulangan.show', $kepulanganAktif->id_kepulangan)); ?>">Lihat detail &rarr;</a>
    </div>
</div>
<?php endif; ?>


<div class="sd-sec"><i class="fas fa-layer-group"></i> Ringkasan</div>
<div class="sd-kpi-g">
    <div class="sd-kpi kv-g">
        <div class="sd-kpi-top"></div>
        <div class="sd-kpi-ico"><i class="fas fa-wallet"></i></div>
        <div class="sd-kpi-val" style="font-size:1.1rem">Rp <?php echo e(number_format($data['saldo_uang_saku'],0,',','.')); ?></div>
        <div class="sd-kpi-lbl">Saldo Uang Saku</div>
        <div class="sd-kpi-ft"><a href="<?php echo e(route('santri.uang-saku.index')); ?>" class="sd-a g">Riwayat <i class="fas fa-arrow-right" style="font-size:.58rem"></i></a></div>
    </div>
    <div class="sd-kpi <?php echo e($poin==0?'kv-g':($poin<50?'kv-a':'kv-r')); ?>">
        <div class="sd-kpi-top"></div>
        <div class="sd-kpi-ico"><i class="fas fa-shield-alt"></i></div>
        <div class="sd-kpi-val"><?php echo e($poin); ?></div>
        <div class="sd-kpi-lbl">Poin Pelanggaran</div>
        <div class="sd-kpi-ft">
            <?php if($poin==0): ?><span style="color:#059862;font-weight:700;font-size:.74rem">&#10003; Bersih</span>
            <?php else: ?><a href="<?php echo e(route('santri.pelanggaran.index')); ?>" class="sd-a">Lihat <i class="fas fa-arrow-right" style="font-size:.58rem"></i></a><?php endif; ?>
        </div>
    </div>
    <div class="sd-kpi kv-b">
        <div class="sd-kpi-top"></div>
        <div class="sd-kpi-ico"><i class="fas fa-graduation-cap"></i></div>
        <div class="sd-kpi-val"><?php echo e($avgCap); ?>%</div>
        <div class="sd-kpi-lbl">Rata-rata Capaian Belajar</div>
        <div class="sd-kpi-ft"><a href="<?php echo e(route('santri.capaian.index')); ?>" class="sd-a">Detail <i class="fas fa-arrow-right" style="font-size:.58rem"></i></a></div>
    </div>
    <div class="sd-kpi kv-t">
        <div class="sd-kpi-top"></div>
        <div class="sd-kpi-ico"><i class="fas fa-calendar-day"></i></div>
        <div class="sd-kpi-val"><?php echo e(now()->locale('id')->isoFormat('dddd')); ?></div>
        <div class="sd-kpi-lbl">Jadwal Kegiatan Aktif</div>
        <div class="sd-kpi-ft"><a href="<?php echo e(route('santri.kegiatan.index')); ?>" class="sd-a t">Cek Jadwal <i class="fas fa-arrow-right" style="font-size:.58rem"></i></a></div>
    </div>
</div>


<div class="sd-sec"><i class="fas fa-door-open"></i> Kepulangan Tahun <?php echo e(now()->year); ?></div>
<div class="sd-card">
    <div class="sd-ch">
        <h3><i class="fas fa-calendar-minus" style="color:var(--teal)"></i> Kuota Izin Kepulangan</h3>
        <a href="<?php echo e(route('santri.kepulangan.index')); ?>" class="sd-a"><i class="fas fa-list"></i> Lihat Semua</a>
    </div>
    <div style="padding:18px 20px">
        <div class="sd-kq-nums">
            <div class="sd-kq-num">
                <div class="v" style="color:var(--ink)"><?php echo e($kpTotalHari); ?> <span style="font-size:.74rem;font-weight:600;color:var(--ink3)">/ 12 hari</span></div>
                <div class="s">Total hari terpakai</div>
            </div>
            <div class="sd-kq-num">
                <div class="v" style="color:<?php echo e($kpOver?'var(--red)':'var(--green)'); ?>"><?php echo e($kpSisa); ?> hari</div>
                <div class="s">Sisa kuota</div>
            </div>
            <div class="sd-kq-num">
                <div class="v" style="color:var(--blue)"><?php echo e($kpDisetujui); ?></div>
                <div class="s">Kali disetujui</div>
            </div>
            <?php if($kpMenunggu > 0): ?>
            <div class="sd-kq-num" style="align-self:center">
                <span class="sd-pill p-a"><i class="fas fa-clock"></i> <?php echo e($kpMenunggu); ?> menunggu approval</span>
            </div>
            <?php endif; ?>
        </div>
        <div class="sd-qbar">
            <div class="sd-qfill <?php echo e($kpOver?'o':($kpPersen>=75?'w':'')); ?>" style="width:<?php echo e(min(100,$kpPersen)); ?>%"></div>
        </div>
        <div class="sd-qmeta">
            <span><?php echo e($kpTotalHari); ?> dari 12 hari kuota</span>
            <?php if($kpOver): ?><span style="color:var(--red);font-weight:700">&#9888; Melebihi kuota!</span>
            <?php else: ?><span>Sisa <?php echo e($kpSisa); ?> hari</span><?php endif; ?>
        </div>
    </div>
</div>


<div class="sd-sec"><i class="fas fa-pen-to-square"></i> Input Capaian &amp; Informasi</div>
<div class="sd-2">

    
    <div class="sd-card">
        <div class="sd-ch">
            <h3><i class="fas fa-clipboard-check" style="color:var(--blue)"></i> Status Input Capaian</h3>
            <a href="<?php echo e(route('santri.capaian.index')); ?>" class="sd-a p">Buka Halaman</a>
        </div>
        <div class="sd-ci-top">
            <div style="padding-top:1px">
                <div class="sd-ci-badge <?php echo e($ciBadge); ?>">
                    <?php if($ciIsOpen && !$ciSegera): ?><div class="ci-pulse"></div><?php endif; ?>
                    <?php echo e($ciBLabel); ?>

                </div>
            </div>
            <div style="flex:1">
                <?php if($semesterAktif): ?>
                <div style="font-size:.86rem;font-weight:700;color:var(--ink);margin-bottom:3px"><?php echo e($semesterAktif->nama_semester); ?></div>
                <?php endif; ?>
                <?php if($ciIsOpen && $ciDeadline): ?>
                <div style="font-size:.75rem;color:var(--ink3);line-height:1.5">
                    <i class="fas fa-clock" style="font-size:.63rem;margin-right:3px"></i>
                    Deadline: <strong style="color:<?php echo e($ciSegera?'var(--amber)':'var(--ink2)'); ?>">
                        <?php echo e(\Carbon\Carbon::parse($ciDeadline)->locale('id')->isoFormat('D MMMM YYYY')); ?>

                    </strong>
                    <?php if($ciSegera): ?>
                    &bull; <span style="color:var(--amber);font-weight:700"><?php echo e(\Carbon\Carbon::parse($ciDeadline)->diffForHumans()); ?></span>
                    <?php endif; ?>
                </div>
                <?php elseif(!$ciIsOpen): ?>
                <div style="font-size:.75rem;color:var(--ink3);margin-top:2px">Input capaian sedang tidak dibuka.</div>
                <?php endif; ?>
            </div>
        </div>
        <?php if($ciTotal > 0): ?>
        <div class="sd-ci-prog">
            <div style="display:flex;justify-content:space-between;font-size:.78rem">
                <span style="color:var(--ink2);font-weight:500">Progres pengisian materi</span>
                <span style="font-weight:800;color:var(--blue)"><?php echo e($ciSudah); ?> / <?php echo e($ciTotal); ?></span>
            </div>
            <div class="sd-ci-bar"><div class="sd-ci-fill" style="width:<?php echo e($ciPersen); ?>%"></div></div>
            <div style="font-size:.69rem;color:var(--ink3);text-align:right"><?php echo e($ciPersen); ?>% sudah diisi</div>
        </div>
        <?php else: ?>
        <div class="sd-ci-prog">
            <div style="font-size:.78rem;color:var(--ink2);font-weight:500">Progres capaian per kategori</div>
        </div>
        <?php endif; ?>
        <div class="sd-ci-cats">
            <?php $__currentLoopData = $capKat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$kNama, $kPct, $kColor]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="sd-ci-cat">
                <div class="sd-ci-cat-name">
                    <div class="sd-ci-dot" style="background:<?php echo e($kColor); ?>"></div>
                    <?php echo e($kNama); ?>

                </div>
                <div class="sd-ci-cat-bar">
                    <div class="sd-ci-cat-fill" style="width:<?php echo e($kPct); ?>%;background:<?php echo e($kColor); ?>"></div>
                </div>
                <div class="sd-ci-cat-pct" style="color:<?php echo e($kColor); ?>"><?php echo e($kPct); ?>%</div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="sd-card">
        <div class="sd-ch">
            <h3><i class="fas fa-newspaper" style="color:var(--blue)"></i> Berita Terbaru</h3>
            <a href="<?php echo e(route('santri.berita.index')); ?>" class="sd-a">Semua</a>
        </div>
        
        <?php $__empty_1 = true; $__currentLoopData = $beritaTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $berita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <a href="<?php echo e(route('santri.berita.show', $berita->id_berita)); ?>" class="sd-bitem">
            <div class="sd-bdot"></div>
            <div>
                <div class="sd-btit"><?php echo e($berita->judul); ?></div>
                <div class="sd-bmeta"><i class="fas fa-clock" style="font-size:.61rem"></i> <?php echo e($berita->created_at->diffForHumans()); ?></div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div style="padding:26px;text-align:center;color:var(--ink3);font-size:.82rem">
            <i class="fas fa-inbox" style="font-size:1.3rem;display:block;margin-bottom:6px;color:#D1D5DB"></i>
            Belum ada berita terbaru
        </div>
        <?php endif; ?>
    </div>

</div>


<div class="sd-sec"><i class="fas fa-chart-bar"></i> Kehadiran per Kategori Kegiatan</div>
<div class="sd-card" style="margin-bottom:13px">
    <div class="sd-ch">
        <h3><i class="fas fa-chart-bar" style="color:var(--teal)"></i> Kehadiran per Kategori</h3>
        <div style="display:flex;align-items:center;gap:8px">
            <div class="sd-toggle">
                <button class="sd-toggle-btn active" id="btnBulan" onclick="switchPeriode('bulan')"><?php echo e($bulanNama); ?></button>
                <button class="sd-toggle-btn" id="btnMinggu" onclick="switchPeriode('minggu')"><?php echo e($mingguLabel); ?></button>
            </div>
            <a href="<?php echo e(route('santri.kegiatan.index')); ?>" class="sd-a t">Detail</a>
        </div>
    </div>
    <div class="sd-bar-body">
        <div class="sd-bar-legend">
            <div class="sd-bar-leg-item"><div class="sd-bar-leg-dot" style="background:#22C97E"></div>Hadir</div>
            <div class="sd-bar-leg-item"><div class="sd-bar-leg-dot" style="background:#EF4444"></div>Alpa</div>
            <div class="sd-bar-leg-item"><div class="sd-bar-leg-dot" style="background:#3B82F6"></div>Izin</div>
            <div class="sd-bar-leg-item"><div class="sd-bar-leg-dot" style="background:#F59E0B"></div>Sakit</div>
        </div>
        <div style="position:relative;height:220px">
            <canvas id="chartBarKehadiran"></canvas>
        </div>
        <div id="barEmpty" style="display:none;height:180px;flex-direction:column;align-items:center;justify-content:center;gap:8px;color:var(--ink3)">
            <i class="fas fa-chart-bar" style="font-size:1.6rem;color:#D1D5DB"></i>
            <span style="font-size:.82rem">Belum ada data untuk periode ini</span>
        </div>
    </div>
</div>




<?php if($poin > 0): ?>
<div class="sd-sec"><i class="fas fa-exclamation-circle"></i> Catatan Pelanggaran Terkini</div>
<div class="sd-card">
    <div class="sd-ch">
        <h3><i class="fas fa-clipboard-list" style="color:var(--red)"></i> 5 Pelanggaran Terakhir</h3>
        <div style="display:flex;align-items:center;gap:8px">
            <span class="sd-pill p-r"><?php echo e($poin); ?> poin total</span>
            <a href="<?php echo e(route('santri.pelanggaran.index')); ?>" class="sd-a">Semua</a>
        </div>
    </div>
    <table class="sd-tbl">
        <thead><tr><th>Tanggal</th><th>Pelanggaran</th><th>Poin</th></tr></thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $pelanggaranTerbaru ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td style="color:var(--ink2);white-space:nowrap"><?php echo e(\Carbon\Carbon::parse($pel->tanggal)->locale('id')->isoFormat('D MMM YY')); ?></td>
                <td><?php echo e($pel->kategori->nama_pelanggaran ?? ($pel->keterangan ?? '-')); ?></td>
                <td><span class="sd-pill p-r"><?php echo e($pel->poin); ?></span></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="3" style="text-align:center;color:var(--ink3);font-style:italic;padding:22px">Tidak ada data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Data dari PHP ───────────────────────────────────────────
const DATA = {
    bulan: {
        labels: <?php echo json_encode($barKategori, 15, 512) ?>,
        hadir:  <?php echo json_encode($barHadir, 15, 512) ?>,
        alpa:   <?php echo json_encode($barAlpa, 15, 512) ?>,
        izin:   <?php echo json_encode($barIzin, 15, 512) ?>,
        sakit:  <?php echo json_encode($barSakit, 15, 512) ?>,
    },
    minggu: {
        labels: <?php echo json_encode($barKatMgg, 15, 512) ?>,
        hadir:  <?php echo json_encode($barHadirMgg, 15, 512) ?>,
        alpa:   <?php echo json_encode($barAlpaMgg, 15, 512) ?>,
        izin:   <?php echo json_encode($barIzinMgg, 15, 512) ?>,
        sakit:  <?php echo json_encode($barSakitMgg, 15, 512) ?>,
    }
};

const COLORS = {
    hadir:  'rgba(34,201,126,.85)',
    alpa:   'rgba(239,68,68,.8)',
    izin:   'rgba(59,130,246,.8)',
    sakit:  'rgba(245,158,11,.8)',
};

let barChart = null;
let currentPeriode = 'bulan';

function buildDatasets(d) {
    return [
        { label:'Hadir',  data:d.hadir,  backgroundColor:COLORS.hadir,  borderRadius:5, borderSkipped:false },
        { label:'Alpa',   data:d.alpa,   backgroundColor:COLORS.alpa,   borderRadius:5, borderSkipped:false },
        { label:'Izin',   data:d.izin,   backgroundColor:COLORS.izin,   borderRadius:5, borderSkipped:false },
        { label:'Sakit',  data:d.sakit,  backgroundColor:COLORS.sakit,  borderRadius:5, borderSkipped:false },
    ];
}

function initChart() {
    const canvas = document.getElementById('chartBarKehadiran');
    const emptyEl = document.getElementById('barEmpty');
    if (!canvas) return;

    const d = DATA[currentPeriode];
    const isEmpty = !d.labels || d.labels.length === 0;

    if (isEmpty) {
        canvas.parentElement.style.display = 'none';
        if (emptyEl) { emptyEl.style.display = 'flex'; }
        return;
    }

    canvas.parentElement.style.display = 'block';
    if (emptyEl) emptyEl.style.display = 'none';

    if (barChart) {
        barChart.data.labels = d.labels;
        barChart.data.datasets = buildDatasets(d);
        barChart.update('active');
        return;
    }

    barChart = new Chart(canvas, {
        type: 'bar',
        data: { labels: d.labels, datasets: buildDatasets(d) },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1A1F2E',
                    titleFont: { size: 12, weight: '700' },
                    bodyFont: { size: 12 },
                    padding: 10,
                    callbacks: {
                        title: items => items[0].label,
                        label: c => ` ${c.dataset.label}: ${c.parsed.y} sesi`,
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 11 },
                        callback: v => Number.isInteger(v) ? v : null
                    },
                    grid: { color: 'rgba(0,0,0,.04)' }
                }
            },
            barPercentage: 0.72,
            categoryPercentage: 0.78,
        }
    });
}

function switchPeriode(p) {
    currentPeriode = p;
    document.getElementById('btnBulan').classList.toggle('active', p === 'bulan');
    document.getElementById('btnMinggu').classList.toggle('active', p === 'minggu');
    initChart();
}

document.addEventListener('DOMContentLoaded', initChart);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\TugasAkhir\sim-pkpps\resources\views/santri/dashboardSantri.blade.php ENDPATH**/ ?>