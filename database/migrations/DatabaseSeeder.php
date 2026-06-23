<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * Seeder de base — crée 3 comptes de test (admin, instructeur, apprenant)
 *
 * Lancer avec : php artisan db:seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────────────────────
        User::create([
            'name'              => 'Administrateur',
            'email'             => 'admin@elearning.com',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        // ── Instructeur ────────────────────────────────────────────────────
        User::create([
            'name'              => 'Jean Instructeur',
            'email'             => 'instructor@elearning.com',
            'password'          => Hash::make('password'),
            'role'              => 'instructor',
            'email_verified_at' => now(),
        ]);

        // ── Apprenant ──────────────────────────────────────────────────────
        User::create([
            'name'              => 'Marie Apprenante',
            'email'             => 'student@elearning.com',
            'password'          => Hash::make('password'),
            'role'              => 'student',
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Comptes de test créés :');
        $this->command->table(
            ['Email', 'Mot de passe', 'Rôle'],
            [
                ['admin@elearning.com',      'password', 'admin'],
                ['instructor@elearning.com', 'password', 'instructor'],
                ['student@elearning.com',    'password', 'student'],
            ]
        );
    }
}
