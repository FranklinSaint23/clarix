<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table : lessons
 * Fichier : 2024_01_02_000003_create_lessons_table.php
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('video'); // video | text | quiz
            $table->string('video_path')->nullable();  // chemin fichier vidéo local
            $table->string('video_url')->nullable();   // URL externe (YouTube, Vimeo)
            $table->longText('content')->nullable();   // contenu texte/html
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->boolean('is_free')->default(false); // leçon gratuite = preview
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
