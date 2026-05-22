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
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->string('result_status', 20)->nullable()->after('status')->index(); // learning|passed|failed
            $table->timestamp('result_updated_at')->nullable()->after('result_status');
            $table->foreignId('result_updated_by')->nullable()->after('result_updated_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropForeign(['result_updated_by']);
            $table->dropColumn(['result_status', 'result_updated_at', 'result_updated_by']);
        });
    }
};
