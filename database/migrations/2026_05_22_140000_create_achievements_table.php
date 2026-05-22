<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->string('name', 120);
            $table->string('description', 255)->nullable();
            $table->string('icon', 40)->default('badge');
            $table->unsignedInteger('points')->default(0);
            $table->string('tier', 20)->default('bronze')->index(); // bronze|silver|gold|platinum
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};

