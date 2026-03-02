<?php
// app/Console/Commands/CleanSantriAccounts.php

namespace App\Console\Commands;

use App\Models\SantriAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CleanSantriAccounts extends Command
{
    /**
     * Nama dan signature command.
     */
    protected $signature = 'santri:clean-accounts
        {--dry-run : Tampilkan perubahan tanpa menyimpan}';

    /**
     * Deskripsi command.
     */
    protected $description = 'Reset username & password semua akun santri/wali agar konsisten (santri=nama_lengkap, wali=nama_orang_tua, password=NIS)';

    /**
     * Jalankan command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('=== DRY RUN MODE (tidak ada perubahan yang disimpan) ===');
        }

        $accounts = SantriAccount::with('santri')->get();
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($accounts as $account) {
            $santri = $account->santri;

            // -- Skip jika santri tidak ditemukan --
            if (!$santri) {
                $errors[] = "Account ID {$account->id} (id_santri={$account->id_santri}): Data santri tidak ditemukan.";
                $skipped++;
                continue;
            }

            // -- Skip jika NIS kosong --
            if (empty($santri->nis)) {
                $errors[] = "Account ID {$account->id} ({$santri->nama_lengkap}): NIS kosong, dilewati.";
                $skipped++;
                continue;
            }

            // -- Tentukan username yang benar --
            if ($account->role === 'wali') {
                $usernameBenar = $santri->nama_orang_tua ?: $santri->nama_lengkap;
            } else {
                $usernameBenar = $santri->nama_lengkap;
            }

            // -- Cek apakah username sudah benar --
            $usernameChanged = ($account->username !== $usernameBenar);

            if ($usernameChanged) {
                // -- Pastikan username unik --
                $existing = SantriAccount::where('username', $usernameBenar)
                    ->where('id', '!=', $account->id)
                    ->exists();
                if ($existing) {
                    $usernameBenar = $usernameBenar . '_' . $santri->nis;
                }
            }

            if ($usernameChanged) {
                $this->line("  [{$account->role}] {$santri->nama_lengkap}: username '{$account->username}' -> '{$usernameBenar}'");
            }

            if (!$dryRun) {
                $account->username = $usernameBenar;
                $account->password = Hash::make($santri->nis);
                $account->save();
            }

            $updated++;
        }

        $this->newLine();
        $this->info("Selesai! Updated: {$updated}, Skipped: {$skipped}");

        if (count($errors) > 0) {
            $this->newLine();
            $this->warn('Masalah ditemukan:');
            foreach ($errors as $err) {
                $this->warn("  - {$err}");
            }
        }

        if ($dryRun) {
            $this->newLine();
            $this->comment('Jalankan tanpa --dry-run untuk menyimpan perubahan.');
        }

        return Command::SUCCESS;
    }
}
