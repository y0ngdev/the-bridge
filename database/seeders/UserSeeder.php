<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            //            'first_name' => 'Demo',
            //            'last_name' => 'User',
            'name' => 'Unit Coordinator',
            'email' => 'aru@rcffuta.com',
            'password' => Hash::make('alumnirelations'),
        ]);
    }
}
