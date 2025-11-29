<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Добро пожаловать в CRM</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <a href="{{ route('clients.index') }}" class="p-6 bg-white shadow rounded-xl block">
                <h3 class="font-semibold text-lg">Клиенты</h3>
                <p class="text-gray-600">Управление базой клиентов</p>
            </a>

            <div class="p-6 bg-white shadow rounded-xl">
                <h3 class="font-semibold text-lg">Задачи</h3>
                <p class="text-gray-600">Управление задачами</p>
            </div>

            <div class="p-6 bg-white shadow rounded-xl">
                <h3 class="font-semibold text-lg">Отчёты</h3>
                <p class="text-gray-600">Аналитика CRM</p>
            </div>

            <div class="p-6 bg-white shadow rounded-xl">
                <h3 class="font-semibold text-lg">Настройки</h3>
                <p class="text-gray-600">Управление системой</p>
            </div>

        </div>
    </div>
</x-app-layout>
