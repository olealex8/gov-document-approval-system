<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PengujiSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'penguji@test.com'],
            [
                'name' => 'Penguji Test',
                'password' => Hash::make('password'),
            ]
        );

        $user->assignRole('penguji');
    }
}
