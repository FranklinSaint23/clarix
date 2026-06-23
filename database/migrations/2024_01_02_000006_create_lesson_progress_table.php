<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table : lesson_progress  (suivi de complétion par leçon)
 * Fichier : 2024_01_02_000006_create_lesson_progress_table.php
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('lesson_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->boolean('completed')->default(false);
            $table->unsignedInteger('watch_seconds')->default(0); // temps de visionnage
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
    }
};
