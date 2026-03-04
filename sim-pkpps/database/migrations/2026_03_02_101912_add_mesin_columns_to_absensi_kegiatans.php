<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_kegiatans', function (Blueprint $table) {
            if (!Schema::hasColumn('absensi_kegiatans', 'metode_absen')) {
                $table->string('metode_absen')->default('Manual')->after('status');
                // Nilai: 'Manual' | 'RFID' | 'Import_Mesin'
            }
            if (!Schema::hasColumn('absensi_kegiatans', 'konflik_catatan')) {
                $table->string('konflik_catatan')->nullable()->after('metode_absen');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absensi_kegiatans', function (Blueprint $table) {
            $table->dropColumnIfExists('metode_absen');
            $table->dropColumnIfExists('konflik_catatan');
        });
    }
};