<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table : courses
 * Fichier : 2024_01_02_000001_create_courses_table.php
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')            // instructeur propriétaire
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();  // chemin image miniature
            $table->string('level')                   // beginner | intermediate | advanced
                  ->default('beginner');
            $table->string('language')->default('fr');
            $table->decimal('price', 8, 2)->default(0); // 0 = gratuit
            $table->boolean('published')->default(false);
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
