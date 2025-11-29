<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Создаем тестовых пользователей (если их нет)
        $users = [
            [
                'name' => 'Менеджер Иванов',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Менеджер Петрова',
                'email' => 'petrova@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($users as $userData) {
            DB::table('users')->insertOrIgnore($userData);
        }

        // Создаем тестовых клиентов
        $clients = [
            // Физические лица
            [
                'type' => 'individual',
                'last_name' => 'Сидоров',
                'first_name' => 'Алексей',
                'middle_name' => 'Петрович',
                'email' => 'sidorov@mail.ru',
                'phone' => '+7 (912) 345-67-89',
                'address' => 'г. Москва, ул. Ленина, д. 15, кв. 42',
                'responsible_id' => 2, // Менеджер Иванов
                'created_by' => 1, // Admin
                'status' => 'active',
                'source' => 'website',
                'total_revenue' => 150000.00,
                'last_activity_at' => now()->subDays(2),
                'activity_score' => 85,
                'notes' => 'Постоянный клиент, заинтересован в расширении сотрудничества',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'individual',
                'last_name' => 'Козлова',
                'first_name' => 'Мария',
                'middle_name' => 'Сергеевна',
                'email' => 'kozlova@gmail.com',
                'phone' => '+7 (923) 456-78-90',
                'address' => 'г. Санкт-Петербург, Невский пр., д. 100',
                'responsible_id' => 3, // Менеджер Петрова
                'created_by' => 1,
                'status' => 'lead',
                'source' => 'recommendation',
                'total_revenue' => 0.00,
                'last_activity_at' => now()->subDays(5),
                'activity_score' => 45,
                'notes' => 'Новый лид, требуется первичная консультация',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ИП
            [
                'type' => 'entrepreneur',
                'last_name' => 'Васильев',
                'first_name' => 'Дмитрий',
                'middle_name' => 'Игоревич',
                'ogrnip' => '321774600100123',
                'inn' => '771234567890',
                'email' => 'vasiliev.ip@business.ru',
                'phone' => '+7 (905) 123-45-67',
                'address' => 'г. Екатеринбург, ул. Мира, д. 25',
                'responsible_id' => 2,
                'created_by' => 1,
                'status' => 'active',
                'source' => 'cold_call',
                'total_revenue' => 450000.00,
                'last_activity_at' => now()->subDays(1),
                'activity_score' => 92,
                'notes' => 'ИП, работает в сфере IT услуг',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Юридические лица
            [
                'type' => 'legal',
                'company_name' => 'ООО "ТехноПром"',
                'legal_form' => 'ООО',
                'legal_type' => 'Производство',
                'ogrn' => '1187746001001',
                'kpp' => '771901001',
                'inn' => '7701123456',
                'email' => 'info@technoprom.ru',
                'phone' => '+7 (495) 123-45-67',
                'address' => 'г. Москва, ул. Промышленная, д. 15',
                'responsible_id' => 3,
                'created_by' => 1,
                'status' => 'negotiation',
                'source' => 'exhibition',
                'total_revenue' => 1200000.00,
                'last_activity_at' => now(),
                'activity_score' => 78,
                'notes' => 'Крупный производитель, ведем переговоры о долгосрочном сотрудничестве',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'legal',
                'company_name' => 'АО "СтройГарант"',
                'legal_form' => 'АО',
                'legal_type' => 'Строительство',
                'ogrn' => '1187746001002',
                'kpp' => '772501001',
                'inn' => '7702987654',
                'email' => 'contract@stroigarant.ru',
                'phone' => '+7 (495) 765-43-21',
                'address' => 'г. Москва, Ленинградский пр-т, д. 80',
                'responsible_id' => 2,
                'created_by' => 1,
                'status' => 'active',
                'source' => 'partner',
                'total_revenue' => 2800000.00,
                'last_activity_at' => now()->subDays(3),
                'activity_score' => 95,
                'notes' => 'Ключевой клиент, регулярные заказы',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($clients as $clientData) {
            DB::table('clients')->insert($clientData);
        }

        // Создаем тестовые сделки
        $deals = [
            [
                'client_id' => 1,
                'title' => 'Разработка корпоративного сайта',
                'amount' => 250000.00,
                'status' => 'won',
                'expected_close_at' => now()->subDays(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 3,
                'title' => 'Техническая поддержка 24/7',
                'amount' => 120000.00,
                'status' => 'in_progress',
                'expected_close_at' => now()->addDays(15),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 4,
                'title' => 'Внедрение CRM системы',
                'amount' => 850000.00,
                'status' => 'negotiation',
                'expected_close_at' => now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 5,
                'title' => 'Разработка мобильного приложения',
                'amount' => 1500000.00,
                'status' => 'proposal',
                'expected_close_at' => now()->addDays(45),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($deals as $dealData) {
            DB::table('deals')->insert($dealData);
        }

        // Создаем тестовые счета
        $invoices = [
            [
                'client_id' => 1,
                'amount' => 250000.00,
                'status' => 'paid',
                'issued_at' => now()->subDays(20),
                'paid_at' => now()->subDays(15),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 3,
                'amount' => 60000.00,
                'status' => 'sent',
                'issued_at' => now()->subDays(5),
                'paid_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 5,
                'amount' => 500000.00,
                'status' => 'paid',
                'issued_at' => now()->subDays(30),
                'paid_at' => now()->subDays(25),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($invoices as $invoiceData) {
            DB::table('invoices')->insert($invoiceData);
        }

        // Создаем тестовые взаимодействия
        $interactions = [
            [
                'client_id' => 1,
                'user_id' => 2,
                'type' => 'call',
                'content' => 'Обсудили детали нового проекта. Клиент доволен текущим сотрудничеством.',
                'interaction_at' => now()->subDays(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 2,
                'user_id' => 3,
                'type' => 'meeting',
                'content' => 'Первая встреча, презентовали наши услуги. Клиент проявил интерес.',
                'interaction_at' => now()->subDays(5),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 4,
                'user_id' => 2,
                'type' => 'email',
                'content' => 'Отправили коммерческое предложение по внедрению CRM.',
                'interaction_at' => now()->subDays(1),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($interactions as $interactionData) {
            DB::table('client_interactions')->insert($interactionData);
        }

        // Создаем тестовые задачи
        $tasks = [
            [
                'client_id' => 2,
                'user_id' => 3,
                'title' => 'Провести демонстрацию продукта',
                'description' => 'Организовать онлайн-демонстрацию основных возможностей системы для клиента',
                'status' => 'open',
                'due_date' => now()->addDays(3),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 4,
                'user_id' => 2,
                'title' => 'Подготовить договор',
                'description' => 'Подготовить проект договора по внедрению CRM системы',
                'status' => 'in_progress',
                'due_date' => now()->addDays(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 3,
                'user_id' => 2,
                'title' => 'Уточнить технические требования',
                'description' => 'Согласовать детали технического задания по проекту',
                'status' => 'done',
                'due_date' => now()->subDays(1),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($tasks as $taskData) {
            DB::table('client_tasks')->insert($taskData);
        }

        $this->command->info('✅ Тестовые данные успешно созданы!');
        $this->command->line('👥 Пользователи: 3 (admin@example.com / password)');
        $this->command->line('👤 Клиенты: 5 (физические лица, ИП, юридические лица)');
        $this->command->line('💰 Сделки: 4 с разными статусами');
        $this->command->line('🧾 Счета: 3 (оплаченные и ожидающие оплаты)');
        $this->command->line('📞 Взаимодействия: 3 (звонки, встречи, emails)');
        $this->command->line('✅ Задачи: 3 с разными статусами выполнения');
    }
}
