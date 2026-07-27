<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Convert existing data
        // aktif -> tetap (convert active employees to permanent)
        // nonaktif -> tetap (convert inactive employees to permanent as well, can be changed manually later)
        DB::table('karyawans')
            ->whereIn('status_karyawan', ['aktif', 'nonaktif'])
            ->update(['status_karyawan' => 'tetap']);

        // Step 2: Modify the column to new enum values
        // Check if using SQLite (for testing) or MySQL (for production)
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite doesn't support ENUM or ALTER COLUMN directly
            // We'll use a string column with CHECK constraint
            // This is already a string column from the original migration, so just add validation at app level
            // The Model already validates this through fillable and validation rules
        } else {
            // MySQL/MariaDB supports ENUM
            DB::statement("ALTER TABLE karyawans MODIFY COLUMN status_karyawan ENUM('tetap', 'kontrak', 'magang') NOT NULL DEFAULT 'tetap'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Convert data back to old format
        // All employment types back to 'aktif'
        DB::table('karyawans')
            ->whereIn('status_karyawan', ['tetap', 'kontrak', 'magang'])
            ->update(['status_karyawan' => 'aktif']);

        // Step 2: Restore original enum
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite: No enum to restore, validation is at app level
        } else {
            // MySQL/MariaDB: Restore original enum
            DB::statement("ALTER TABLE karyawans MODIFY COLUMN status_karyawan ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif'");
        }
    }
};
