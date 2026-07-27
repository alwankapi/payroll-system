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
        // Standardize existing penggajian status to only 3 valid statuses
        // Old status mapping:
        // - diproses, disetujui -> final
        // - ditolak, dibatalkan -> draft
        
        DB::table('penggajians')
            ->whereIn('status', ['diproses', 'disetujui'])
            ->update(['status' => 'final']);
            
        DB::table('penggajians')
            ->whereIn('status', ['ditolak', 'dibatalkan'])
            ->update(['status' => 'draft']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reliably reverse this migration as we lost information
        // about which 'final' records were 'diproses' vs 'disetujui'
        // and which 'draft' records were 'ditolak' vs 'dibatalkan'
    }
};
