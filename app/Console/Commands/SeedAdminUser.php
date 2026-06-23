<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SeedAdminUser extends Command
{
    protected $signature = 'admin:seed';
    protected $description = 'Crée le compte admin par défaut s\'il n\'existe pas';

    public function handle(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@clarix.com'],
            [
                'name'              => 'Administrateur',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->info('✅ Admin prêt : admin@clarix.com');
    }
}
