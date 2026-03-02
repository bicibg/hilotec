<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->warn('Skipping AdminUserSeeder in production environment.');
            return;
        }

        User::updateOrCreate(
            ['email' => 'admin@hilotec.com'],
            [
                'name' => 'HILOTEC Admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
