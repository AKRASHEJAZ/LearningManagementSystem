<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'approval_status')) {
            return;
        }

        DB::table('users')
            ->whereNull('approval_status')
            ->update(['approval_status' => 'pending']);

        DB::table('users')
            ->where('is_admin', 1)
            ->update([
                'approval_status' => 'approved',
                'approved_at' => DB::raw('CURRENT_TIMESTAMP'),
                'approved_by' => null,
                'rejected_at' => null,
                'rejected_by' => null,
                'rejection_reason' => null,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: data backfill not reversed.
    }
};
