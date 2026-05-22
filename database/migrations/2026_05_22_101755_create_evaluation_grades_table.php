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
        Schema::create('evaluation_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('student_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('graded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('score')->nullable();
            $table->string('status', 20)->default('learning')->index(); // learning|passed|failed
            $table->text('feedback')->nullable();
            $table->timestamp('graded_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['evaluation_id', 'student_user_id']);
            $table->index(['course_id', 'student_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_grades');
    }
};
