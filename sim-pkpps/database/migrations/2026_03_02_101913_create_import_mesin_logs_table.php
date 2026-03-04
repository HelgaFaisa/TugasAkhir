<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_mesin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('jumlah_scan')->default(0);
            $table->integer('berhasil')->default(0);
            $table->integer('konflik_selesai')->default(0);
            $table->integer('dilewati')->default(0);
            $table->integer('no_santri')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_mesin_logs');
    }
};