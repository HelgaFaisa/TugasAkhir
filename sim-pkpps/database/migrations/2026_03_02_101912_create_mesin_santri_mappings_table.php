<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesin_santri_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('id_mesin')->unique(); // EnNo dari GLog: '1','2','8'
            $table->string('id_santri')->nullable();
            $table->string('nama_mesin')->nullable();
            $table->string('dept_mesin')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->index('id_santri');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesin_santri_mappings');
    }
};