<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rekap_absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->unsignedTinyInteger('bulan'); // 1-12
            $table->year('tahun');
            $table->unsignedTinyInteger('total_hari_kerja')->default(22);
            $table->unsignedTinyInteger('hadir')->default(0);
            $table->unsignedTinyInteger('izin')->default(0);
            $table->unsignedTinyInteger('sakit')->default(0);
            $table->unsignedTinyInteger('alpha')->default(0);
            $table->unsignedTinyInteger('terlambat')->default(0);
            $table->unsignedTinyInteger('lembur')->default(0)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            // Unique constraint: satu karyawan hanya punya satu rekap per bulan/tahun
            $table->unique(['karyawan_id', 'bulan', 'tahun']);
            
            // Index untuk query performa
            $table->index(['bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_absensis');
    }
};
