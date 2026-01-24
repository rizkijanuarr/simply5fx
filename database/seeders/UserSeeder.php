<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Rizki J.',
            'email'  => 'rizkij@s5fx.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }
}
