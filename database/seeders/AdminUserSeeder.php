<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Проверяем, существует ли уже админ
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(), // если есть верификация email
            ]);

            echo "✅ Администратор создан:\n";
            echo "   👤 Имя: Admin\n";
            echo "   📧 Email: admin@example.com\n";
            echo "   🔑 Пароль: password\n";
        } else {
            echo "ℹ️ Администратор уже существует\n";
        }
    }
}