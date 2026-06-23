<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table : enrollments  (inscriptions des apprenants aux cours)
 * Fichier : 2024_01_02_000005_create_enrollments_table.php
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('course_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('status')->default('active'); // active | completed | cancelled
            $table->timestamp('completed_at')->nullable();
            $table->decimal('paid_amount', 8, 2)->default(0);
            $table->timestamps();

            // Un apprenant ne peut s'inscrire qu'une seule fois par cours
            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
