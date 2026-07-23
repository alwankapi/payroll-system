<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penggajian_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penggajian_id')->constrained('penggajians')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('potongan_id')->constrained('potongans')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nama_potongan', 100);
            $table->decimal('nilai_potongan', 15, 2)->unsigned()->default(0);
            $table->timestamps();

            $table->index('penggajian_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penggajian_detail');
    }
};
