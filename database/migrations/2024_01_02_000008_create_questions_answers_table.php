<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tables : questions + answers
 * Fichier : 2024_01_02_000008_create_questions_answers_table.php
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Questions ──────────────────────────────────────────────────────
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->text('question_text');
            $table->string('type')->default('single'); // single | multiple | true_false
            $table->unsignedTinyInteger('points')->default(1);
            $table->text('explanation')->nullable();   // explication affichée après correction
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        // ── Réponses possibles ─────────────────────────────────────────────
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->text('answer_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
        Schema::dropIfExists('questions');
    }
};
