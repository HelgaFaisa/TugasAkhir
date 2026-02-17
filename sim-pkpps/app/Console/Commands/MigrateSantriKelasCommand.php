<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\SantriKelas;

class MigrateSantriKelasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:santri-kelas 
                            {--dry-run : Run without inserting data}
                            {--force : Overwrite existing data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data kelas santri dari kolom \'kelas\' ke tabel \'santri_kelas\'';

    /**
     * Mapping kelas lama ke ID kelas baru
     *
     * @var array
     */
    protected $kelasMapping = [
        'PB' => 1,
        'Lambatan' => 2,
        'Cepatan' => 3,
    ];

    /**
     * Counters
     */
    protected $totalSantri = 0;
    protected $successCount = 0;
    protected $skipCount = 0;
    protected $errorCount = 0;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        // Header
        $this->info('╔══════════════════════════════════════════════════════╗');
        $this->info('║     Migrating Santri Kelas Data to New System       ║');
        $this->info('╚══════════════════════════════════════════════════════╝');
        $this->newLine();

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No data will be inserted');
            $this->newLine();
        }

        // Get tahun ajaran aktif
        $tahunAjaran = SantriKelas::getCurrentAcademicYear();
        $this->info("📅 Tahun Ajaran: {$tahunAjaran}");
        $this->newLine();

        // Verify kelas mapping exists
        if (!$this->verifyKelasMapping()) {
            return 1;
        }

        // Get all santri dengan kelas
        $santris = Santri::whereNotNull('kelas')
                        ->whereIn('kelas', array_keys($this->kelasMapping))
                        ->get();

        $this->totalSantri = $santris->count();

        if ($this->totalSantri === 0) {
            $this->warn('⚠️  No santri found with kelas data');
            return 0;
        }

        $this->info("Found {$this->totalSantri} santri to migrate");
        $this->newLine();

        // Confirmation
        if (!$dryRun && !$force) {
            if (!$this->confirm('Do you want to proceed with migration?')) {
                $this->warn('Migration cancelled');
                return 0;
            }
            $this->newLine();
        }

        // Progress bar
        $progressBar = $this->output->createProgressBar($this->totalSantri);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $progressBar->setMessage('Starting migration...');
        $progressBar->start();

        // Begin transaction
        DB::beginTransaction();

        try {
            foreach ($santris as $santri) {
                $progressBar->setMessage("Processing: {$santri->nama_lengkap}");
                
                $result = $this->migrateSantri($santri, $tahunAjaran, $dryRun, $force);
                
                if ($result === 'success') {
                    $this->successCount++;
                } elseif ($result === 'skip') {
                    $this->skipCount++;
                } else {
                    $this->errorCount++;
                }
                
                $progressBar->advance();
            }

            $progressBar->setMessage('Migration completed!');
            $progressBar->finish();
            $this->newLine(2);

            if (!$dryRun) {
                DB::commit();
                $this->info('✓ Transaction committed');
            } else {
                DB::rollBack();
                $this->info('✓ Transaction rolled back (dry-run)');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->newLine(2);
            $this->error('✗ Migration failed: ' . $e->getMessage());
            Log::error('Santri Kelas Migration Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        // Summary
        $this->newLine();
        $this->displaySummary($dryRun);

        return 0;
    }

    /**
     * Verify kelas mapping exists in database
     */
    protected function verifyKelasMapping()
    {
        $this->info('🔍 Verifying kelas mapping...');

        $missing = [];
        foreach ($this->kelasMapping as $kelasName => $kelasId) {
            $kelas = Kelas::find($kelasId);
            if (!$kelas) {
                $missing[] = "{$kelasName} (ID: {$kelasId})";
            } else {
                $this->line("  ✓ {$kelasName} -> {$kelas->nama_kelas} (ID: {$kelasId})");
            }
        }

        if (!empty($missing)) {
            $this->error('✗ Missing kelas in database:');
            foreach ($missing as $item) {
                $this->error("  - {$item}");
            }
            $this->error('Please run: php artisan db:seed --class=KelasSeeder');
            return false;
        }

        $this->newLine();
        return true;
    }

    /**
     * Migrate single santri
     */
    protected function migrateSantri($santri, $tahunAjaran, $dryRun, $force)
    {
        try {
            // Get ID kelas baru
            $idKelas = $this->kelasMapping[$santri->kelas] ?? null;

            if (!$idKelas) {
                Log::warning('Santri kelas mapping not found', [
                    'id_santri' => $santri->id_santri,
                    'kelas' => $santri->kelas
                ]);
                return 'error';
            }

            // Check if already exists
            $existing = SantriKelas::where('id_santri', $santri->id_santri)
                                  ->where('id_kelas', $idKelas)
                                  ->where('tahun_ajaran', $tahunAjaran)
                                  ->first();

            if ($existing && !$force) {
                return 'skip';
            }

            if ($dryRun) {
                return 'success';
            }

            // Delete existing if force
            if ($existing && $force) {
                $existing->delete();
            }

            // Create new record
            SantriKelas::create([
                'id_santri' => $santri->id_santri,
                'id_kelas' => $idKelas,
                'tahun_ajaran' => $tahunAjaran,
                'is_primary' => true,
            ]);

            return 'success';

        } catch (\Exception $e) {
            Log::error('Error migrating santri', [
                'id_santri' => $santri->id_santri,
                'error' => $e->getMessage()
            ]);
            return 'error';
        }
    }

    /**
     * Display summary
     */
    protected function displaySummary($dryRun)
    {
        $this->info('╔══════════════════════════════════════════════════════╗');
        $this->info('║                   MIGRATION SUMMARY                  ║');
        $this->info('╚══════════════════════════════════════════════════════╝');
        $this->newLine();

        $this->line("  📊 Total santri:           {$this->totalSantri}");
        $this->line("  ✓ Migrated:                <fg=green>{$this->successCount}</>");
        $this->line("  ⊘ Skipped (already exists): <fg=yellow>{$this->skipCount}</>");
        $this->line("  ✗ Errors:                  <fg=red>{$this->errorCount}</>");
        
        $this->newLine();

        if ($dryRun) {
            $this->warn('🔍 DRY RUN - No data was actually inserted');
        } else {
            if ($this->errorCount === 0) {
                $this->info('✓ Migration completed successfully!');
            } else {
                $this->warn('⚠️  Migration completed with errors. Check laravel.log for details.');
            }
        }

        $this->newLine();

        // Next steps
        if (!$dryRun && $this->errorCount === 0) {
            $this->info('📝 Next steps:');
            $this->line('  1. Verify data: SELECT * FROM santri_kelas');
            $this->line('  2. Test backward compatibility: $santri->kelas_name');
            $this->line('  3. Scan codebase for kelas usage: php scan_kelas_usage.php');
            $this->line('  4. Consider dropping santris.kelas column after full migration');
        }
    }
}
