<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('potongans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_potongan', 100);
            $table->enum('jenis_potongan', ['persentase', 'nominal']);
            $table->decimal('nilai', 15, 2)->unsigned()->default(0);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->index('status_aktif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('potongans');
    }
};
