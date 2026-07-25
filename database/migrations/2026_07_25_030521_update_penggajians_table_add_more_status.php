<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum status untuk menambahkan opsi baru
        DB::statement("ALTER TABLE penggajians MODIFY COLUMN status ENUM('draft', 'diproses', 'disetujui', 'dibayar', 'ditolak', 'dibatalkan') NOT NULL DEFAULT 'draft'");
        
        // Tambahkan kolom catatan
        Schema::table('penggajians', function (Blueprint $table) {
            $table->text('catatan')->nullable()->after('tanggal_bayar');
        });
    }

    public function down(): void
    {
        // Kembalikan ke enum status lama
        DB::statement("ALTER TABLE penggajians MODIFY COLUMN status ENUM('draft', 'final', 'dibayar') NOT NULL DEFAULT 'draft'");
    }
};
