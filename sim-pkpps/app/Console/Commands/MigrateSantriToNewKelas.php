<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\KelompokKelas;
use App\Models\SantriKelas;

class MigrateSantriToNewKelas extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'migrate:santri-kelas-full
                            {--dry-run : Preview tanpa menyimpan ke database}';

    /**
     * The console command description.
     */
    protected $description = 'Full migration: Pindahkan data kolom kelas santri ke tabel santri_kelas (sistem baru)';

    /**
     * Counters
     */
    protected int $totalSantri = 0;
    protected int $successCount = 0;
    protected int $skipCount = 0;
    protected int $errorCount = 0;

    /**
     * Collected errors & skipped
     */
    protected array $errors = [];
    protected array $skipped = [];

    /**
     * Resolved kelas mapping cache: ['PB' => Kelas model, ...]
     */
    protected array $kelasMapping = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════╗');
        $this->info('║   MIGRASI SANTRI KE SISTEM KELAS BARU              ║');
        $this->info('║   ' . ($isDryRun ? '🔍 MODE: DRY-RUN (Preview Only)' : '🚀 MODE: EXECUTE (Real Migration)') . '          ║');
        $this->info('╚══════════════════════════════════════════════════════╝');
        $this->newLine();

        // ────────────────────
        // STEP 1: Validasi kelompok kelas
        // ────────────────────
        $this->info('📋 Step 1: Validasi kelompok kelas...');

        if (!$this->validateAndBuildMapping()) {
            $this->error('❌ Validasi gagal! Pastikan data kelompok_kelas dan kelas sudah tersedia.');
            return Command::FAILURE;
        }

        $this->info('  ✅ Mapping kelas berhasil di-resolve:');
        foreach ($this->kelasMapping as $oldKelas => $kelasModel) {
            $this->line("     <fg=cyan>{$oldKelas}</> → <fg=green>{$kelasModel->kode_kelas} ({$kelasModel->nama_kelas})</>");
        }
        $this->newLine();

        // ────────────────────
        // STEP 2: Ambil semua santri
        // ────────────────────
        $this->info('📋 Step 2: Mengambil data santri...');

        $santris = Santri::select('id', 'id_santri', 'nama_lengkap', 'kelas')->get();
        $this->totalSantri = $santris->count();

        if ($this->totalSantri === 0) {
            $this->warn('⚠️  Tidak ada data santri ditemukan.');
            return Command::SUCCESS;
        }

        $this->info("  📊 Total santri ditemukan: <fg=yellow>{$this->totalSantri}</>");
        $this->newLine();

        // ────────────────────
        // STEP 3: Migrate
        // ────────────────────
        $tahunAjaran = SantriKelas::getCurrentAcademicYear();
        $this->info("📋 Step 3: Memulai migrasi (Tahun Ajaran: <fg=yellow>{$tahunAjaran}</>)...");
        $this->newLine();

        if (!$isDryRun) {
            // Wrap dalam transaction untuk safety
            DB::beginTransaction();
        }

        try {
            $this->output->progressStart($this->totalSantri);

            foreach ($santris as $santri) {
                $this->processSantri($santri, $tahunAjaran, $isDryRun);
                $this->output->progressAdvance();
            }

            $this->output->progressFinish();
            $this->newLine();

            if (!$isDryRun) {
                DB::commit();
                $this->info('✅ Transaction committed.');
            }
        } catch (\Exception $e) {
            if (!$isDryRun) {
                DB::rollBack();
                $this->error('❌ Transaction rolled back!');
            }
            $this->error("Fatal error: {$e->getMessage()}");
            Log::error('MigrateSantriToNewKelas fatal error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }

        // ────────────────────
        // STEP 4: Summary Report
        // ────────────────────
        $this->printSummary($isDryRun, $tahunAjaran);

        // ────────────────────
        // STEP 5: Post-migration validation
        // ────────────────────
        if (!$isDryRun) {
            $this->validatePostMigration();
        }

        return Command::SUCCESS;
    }

    /**
     * Validasi kelompok kelas dan build mapping dinamis.
     */
    protected function validateAndBuildMapping(): bool
    {
        $mappings = [
            'PB'       => '%PB%',
            'Lambatan' => '%Lambatan%',
            'Cepatan'  => '%Cepatan%',
        ];

        foreach ($mappings as $oldKelas => $likePattern) {
            $kelas = Kelas::whereHas('kelompok', function ($q) use ($likePattern) {
                $q->where('nama_kelompok', 'like', $likePattern);
            })
            ->where('is_active', true)
            ->orderBy('urutan')
            ->first();

            if (!$kelas) {
                $this->error("  ❌ Tidak ditemukan kelas aktif untuk kelompok '{$oldKelas}' (pattern: {$likePattern})");
                return false;
            }

            $this->kelasMapping[$oldKelas] = $kelas;
        }

        return true;
    }

    /**
     * Process satu santri.
     */
    protected function processSantri(Santri $santri, string $tahunAjaran, bool $isDryRun): void
    {
        try {
            $kelasLama = $santri->kelas;

            // Skip jika kelas NULL atau tidak dikenali
            if (empty($kelasLama) || !isset($this->kelasMapping[$kelasLama])) {
                $reason = empty($kelasLama) ? 'Kelas NULL' : "Kelas '{$kelasLama}' tidak dikenali";
                $this->skipped[] = [
                    'id_santri'    => $santri->id_santri,
                    'nama'         => $santri->nama_lengkap,
                    'reason'       => $reason,
                ];
                $this->skipCount++;
                return;
            }

            $kelasBaru = $this->kelasMapping[$kelasLama];

            if ($isDryRun) {
                // Dry-run: hanya tampilkan
                $this->line("  <fg=green>✓</> {$santri->id_santri} ({$santri->nama_lengkap}): <fg=yellow>{$kelasLama}</> → <fg=cyan>{$kelasBaru->kode_kelas} ({$kelasBaru->nama_kelas})</>");
                $this->successCount++;
                return;
            }

            // Real execute: Insert/update ke santri_kelas
            SantriKelas::updateOrCreate(
                [
                    'id_santri'    => $santri->id_santri,
                    'tahun_ajaran' => $tahunAjaran,
                    'is_primary'   => true,
                ],
                [
                    'id_kelas' => $kelasBaru->id,
                ]
            );

            $this->successCount++;

        } catch (\Exception $e) {
            $this->errors[] = [
                'id_santri' => $santri->id_santri,
                'nama'      => $santri->nama_lengkap,
                'error'     => $e->getMessage(),
            ];
            $this->errorCount++;

            Log::warning('MigrateSantriToNewKelas: Error processing santri', [
                'id_santri' => $santri->id_santri,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * Print summary report.
     */
    protected function printSummary(bool $isDryRun, string $tahunAjaran): void
    {
        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════╗');
        $this->info('║   📊 SUMMARY REPORT                                ║');
        $this->info('╚══════════════════════════════════════════════════════╝');

        $this->newLine();
        $this->line("  Mode          : <fg=" . ($isDryRun ? 'yellow>DRY-RUN (Preview)' : 'green>EXECUTED (Real)') . "</>");
        $this->line("  Tahun Ajaran  : <fg=cyan>{$tahunAjaran}</>");
        $this->newLine();

        $this->line("  Total santri  : <fg=white>{$this->totalSantri}</>");
        $this->line("  ✅ Berhasil    : <fg=green>{$this->successCount}</>");
        $this->line("  ⚠️  Skipped    : <fg=yellow>{$this->skipCount}</>");
        $this->line("  ❌ Error       : <fg=red>{$this->errorCount}</>");

        // List skipped
        if (count($this->skipped) > 0) {
            $this->newLine();
            $this->warn('  ⚠️  Santri yang di-skip:');
            foreach ($this->skipped as $item) {
                $this->line("     - <fg=yellow>{$item['id_santri']}</> ({$item['nama']}): {$item['reason']}");
            }
        }

        // List errors
        if (count($this->errors) > 0) {
            $this->newLine();
            $this->error('  ❌ Santri yang error:');
            foreach ($this->errors as $item) {
                $this->line("     - <fg=red>{$item['id_santri']}</> ({$item['nama']}): {$item['error']}");
            }
        }

        $this->newLine();

        if ($isDryRun) {
            $this->info('💡 Ini hanya preview. Jalankan tanpa --dry-run untuk eksekusi migrasi.');
        } else {
            $this->info('✅ Migrasi selesai! Data santri_kelas telah diperbarui.');
        }

        $this->newLine();
    }

    /**
     * Validasi setelah migrasi.
     */
    protected function validatePostMigration(): void
    {
        $this->info('📋 Post-migration validation...');

        // Count santri yang punya kelas (kolom lama) tapi belum ada di santri_kelas
        $santriDenganKelas = Santri::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->count();

        $santriDiSantriKelas = SantriKelas::where('is_primary', true)->count();

        $this->line("  Santri dengan kelas (kolom lama) : <fg=yellow>{$santriDenganKelas}</>");
        $this->line("  Santri di santri_kelas (primary) : <fg=cyan>{$santriDiSantriKelas}</>");

        if ($santriDiSantriKelas >= $santriDenganKelas) {
            $this->info('  ✅ Validasi OK! Semua santri sudah ter-migrate.');
        } else {
            $diff = $santriDenganKelas - $santriDiSantriKelas;
            $this->warn("  ⚠️  Ada {$diff} santri yang belum ter-migrate. Periksa log di atas.");
        }

        $this->newLine();
    }
}
