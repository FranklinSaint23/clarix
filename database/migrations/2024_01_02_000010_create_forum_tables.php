<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tables : forum_threads + forum_posts
 * Fichier : 2024_01_02_000010_create_forum_tables.php
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Sujets de discussion ───────────────────────────────────────────
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('title');
            $table->boolean('pinned')->default(false);
            $table->boolean('locked')->default(false);  // empêche nouvelles réponses
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Messages / réponses ────────────────────────────────────────────
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_thread_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->longText('body');
            $table->boolean('is_solution')->default(false); // réponse marquée comme solution
            $table->unsignedInteger('likes')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_threads');
    }
};
