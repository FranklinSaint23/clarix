<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tables : quiz_submissions + submission_answers
 * Fichier : 2024_01_02_000009_create_quiz_submissions_table.php
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Soumission de quiz ─────────────────────────────────────────────
        Schema::create('quiz_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('quiz_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->decimal('score', 5, 2)->default(0);   // score obtenu en %
            $table->boolean('passed')->default(false);
            $table->unsignedSmallInteger('attempt')->default(1);
            $table->unsignedSmallInteger('time_spent')->nullable(); // secondes
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        // ── Réponses données par l'apprenant ──────────────────────────────
        Schema::create('submission_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_submission_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('question_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('answer_id')              // réponse choisie
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_answers');
        Schema::dropIfExists('quiz_submissions');
    }
};
