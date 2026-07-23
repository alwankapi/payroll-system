<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatans')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nik', 20)->unique();
            $table->string('nama_lengkap', 100);
            $table->text('alamat')->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->date('tanggal_masuk');
            $table->string('no_rekening', 30)->nullable();
            $table->enum('status_karyawan', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();

            $table->index('jabatan_id');
            $table->index('status_karyawan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
