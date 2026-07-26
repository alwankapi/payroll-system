<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penggajians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('periode');
            $table->decimal('gaji_pokok', 15, 2)->unsigned()->default(0);
            $table->decimal('tunjangan', 15, 2)->unsigned()->default(0);
            $table->decimal('total_potongan', 15, 2)->unsigned()->default(0);
            $table->decimal('gaji_bersih', 15, 2)->unsigned()->default(0);
            $table->string('status', 20)->default('draft');
            $table->date('tanggal_bayar')->nullable();
            $table->timestamps();

            $table->unique(['karyawan_id', 'periode']);
            $table->index('periode');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penggajians');
    }
};
