<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievement_rule_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achievement_rule_id')->constrained('achievement_rules')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['achievement_rule_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievement_rule_courses');
    }
};

