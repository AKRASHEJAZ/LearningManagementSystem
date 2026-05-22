<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievement_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achievement_id')->constrained('achievements')->cascadeOnDelete();
            $table->string('type', 30)->index(); // course_count | specific_courses
            $table->unsignedInteger('min_course_completions')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['achievement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievement_rules');
    }
};

