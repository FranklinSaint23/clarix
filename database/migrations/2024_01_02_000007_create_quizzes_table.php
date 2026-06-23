<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table : quizzes
 * Fichier : 2024_01_02_000007_create_quizzes_table.php
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('lesson_id')            // quiz attaché à une leçon (optionnel)
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('pass_score')->default(70); // % minimum pour réussir
            $table->unsignedSmallInteger('time_limit')->nullable(); // en minutes (null = illimité)
            $table->unsignedTinyInteger('max_attempts')->default(3);
            $table->boolean('show_answers')->default(true); // afficher corrigé après soumission
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
