<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Проверяем, создан ли уже админ
        if (!User::where('email', 'admin@example.com')->exists()) {

            User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
            ]);

            echo "Администратор создан: admin@example.com / password\n";
        }
    }
}

