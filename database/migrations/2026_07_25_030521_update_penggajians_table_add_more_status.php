<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan kolom catatan
        Schema::table('penggajians', function (Blueprint $table) {
            if (!Schema::hasColumn('penggajians', 'catatan')) {
                $table->text('catatan')->nullable()->after('tanggal_bayar');
            }
        });
        
        // Note: Status enum update tidak diperlukan karena Laravel model akan handle validation
        // Enum MySQL tidak compatible dengan SQLite untuk testing
    }

    public function down(): void
    {
        Schema::table('penggajians', function (Blueprint $table) {
            if (Schema::hasColumn('penggajians', 'catatan')) {
                $table->dropColumn('catatan');
            }
        });
    }
};
