<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table : notifications (table Laravel native — déjà générée par défaut)
 * Fichier : 2024_01_02_000011_create_notifications_table.php
 *
 * NOTE : Si vous utilisez php artisan notifications:table,
 *        cette migration est déjà générée automatiquement.
 *        Ne la dupliquez pas dans ce cas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');          // notifiable_type + notifiable_id
            $table->text('data');                  // JSON : contenu de la notif
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
