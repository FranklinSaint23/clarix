<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table : resources  (fichiers téléchargeables attachés à une leçon)
 * Fichier : 2024_01_02_000004_create_resources_table.php
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');                   // stocké dans storage/app/public
            $table->string('file_type')->nullable();       // pdf, zip, docx, mp4…
            $table->unsignedBigInteger('file_size')->default(0); // en octets
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
