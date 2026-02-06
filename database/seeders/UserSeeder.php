<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::factory()->admin()->create([
            'name' => 'Unit Coordinator',
            'email' => 'aru@rcffuta.com',
            'password' => Hash::make('alumnirelations'),
        ]);

        // Normal member user (for logging calls)
        User::factory()->create([
            'name' => 'Call Logger',
            'email' => 'member@rcffuta.com',
            'password' => Hash::make('member123'),
        ]);
    }
}
