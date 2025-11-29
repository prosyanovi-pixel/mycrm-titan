<?php

require __DIR__.'/auth.php';

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientInteractionController;
use App\Http\Controllers\ClientFilesController;
use App\Http\Controllers\ClientTasksController;
use App\Http\Controllers\DealsController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\DepartmentController;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Перенаправление с корневой страницы на страницу входа
Route::redirect('/', '/login');

// ========= ОБЩИЕ МАРШРУТЫ ДЛЯ СДЕЛОК, ЗАДАЧ И ОТЧЕТОВ =========
Route::middleware(['auth'])->group(function () {
    // Общий список всех сделок
    Route::get('/deals', [DealsController::class, 'allDeals'])->name('deals.index');
    // Создание сделки
    Route::get('/deals/create', [DealsController::class, 'create'])->name('deals.create');
    Route::post('/deals', [DealsController::class, 'store'])->name('deals.store');
    // Редактирование сделки
    Route::get('/deals/{deal}/edit', [DealsController::class, 'edit'])->name('deals.edit');
    Route::put('/deals/{deal}', [DealsController::class, 'update'])->name('deals.update');
    // Удаление сделки
    Route::delete('/deals/{deal}', [DealsController::class, 'destroy'])->name('deals.destroy');
    // Общий список всех задач
    Route::get('/tasks', [ClientTasksController::class, 'allTasks'])->name('tasks.index');
    // Общий список всех счетов
    Route::get('/invoices', [InvoicesController::class, 'allInvoices'])->name('invoices.index');
    // Общий список отчетов
    Route::get('/reports', function () {
        return view('reports.index', ['title' => 'Отчеты']);
    })->name('reports.index');
});

// ========= ОБЩИЕ МАРШРУТЫ ДЛЯ УПРАВЛЕНИЯ ЗАДАЧАМИ =========
Route::middleware(['auth'])->group(function () {
    // Создание новой задачи
    Route::get('/tasks/create', [ClientTasksController::class, 'create'])->name('tasks.create');
    // Сохранение новой задачи
    Route::post('/tasks', [ClientTasksController::class, 'storeGeneral'])->name('tasks.store');
    // Редактирование задачи
    Route::get('/tasks/{task}/edit', [ClientTasksController::class, 'edit'])->name('tasks.edit');
    // Обновление задачи
    Route::put('/tasks/{task}', [ClientTasksController::class, 'update'])->name('tasks.update');
    // Удаление задачи
    Route::delete('/tasks/{task}', [ClientTasksController::class, 'destroy'])->name('tasks.destroy');
});

// Панель управления (дашборд)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Основные CRUD маршруты для клиентов
Route::resource('clients', ClientController::class)->middleware('auth');

// Превью карточки клиента
Route::get('/clients/{client}/preview', [ClientController::class, 'preview'])
    ->middleware('auth');

// ========= ДОПОЛНИТЕЛЬНЫЕ МОДУЛИ КЛИЕНТА =========
Route::middleware(['auth'])->group(function () {

    // ========= ВЗАИМОДЕЙСТВИЯ С КЛИЕНТАМИ =========
    Route::prefix('clients/{client}')->group(function () {
        // Создание взаимодействия
        Route::post('interactions', [ClientInteractionController::class, 'store'])
            ->name('clients.interactions.store');
        // Обновление взаимодействия
        Route::put('interactions/{interaction}', [ClientInteractionController::class, 'update'])
            ->name('clients.interactions.update');
        // Удаление взаимодействия
        Route::delete('interactions/{interaction}', [ClientInteractionController::class, 'destroy'])
            ->name('clients.interactions.destroy');
    });

    // ========= ФАЙЛЫ КЛИЕНТА =========
    Route::prefix('clients/{client}')->group(function () {
        // Список файлов
        Route::get('files', [ClientFilesController::class, 'index'])->name('clients.files.index');
        // Загрузка файла
        Route::post('files', [ClientFilesController::class, 'store'])->name('clients.files.store');
        // Обновление файла
        Route::put('files/{file}', [ClientFilesController::class, 'update'])->name('clients.files.update');
        // Скачивание файла
        Route::get('files/{file}/download', [ClientFilesController::class, 'download'])->name('clients.files.download');
        // Удаление файла
        Route::delete('files/{file}', [ClientFilesController::class, 'destroy'])->name('clients.files.destroy');
    });

    // ========= ЗАДАЧИ КЛИЕНТА =========
    Route::prefix('clients/{client}')->group(function () {
        // Создание задачи
        Route::post('tasks', [ClientTasksController::class, 'store'])->name('clients.tasks.store');
        // Обновление задачи
        Route::put('tasks/{task}', [ClientTasksController::class, 'update'])->name('clients.tasks.update');
        // Удаление задачи
        Route::delete('tasks/{task}', [ClientTasksController::class, 'destroy'])->name('clients.tasks.destroy');
        // Изменение статуса задачи
        Route::patch('tasks/{task}/status/{status}', [ClientTasksController::class, 'changeStatus'])
            ->name('clients.tasks.status');
    });

   // ========= СДЕЛКИ КЛИЕНТА =========
    Route::prefix('clients/{client}')->group(function () {
        // Список сделок
        Route::get('deals', [DealsController::class, 'index'])->name('clients.deals.index');
        // Создание сделки
        Route::get('deals/create', [DealsController::class, 'createForClient'])->name('clients.deals.create');
        // Сохранение сделки
        Route::post('deals', [DealsController::class, 'storeForClient'])->name('clients.deals.store');
        // Редактирование сделки
        Route::get('deals/{deal}/edit', [DealsController::class, 'editForClient'])->name('clients.deals.edit');
        // Обновление сделки
        Route::put('deals/{deal}', [DealsController::class, 'updateForClient'])->name('clients.deals.update');
        // Удаление сделки
        Route::delete('deals/{deal}', [DealsController::class, 'destroyForClient'])->name('clients.deals.destroy');
        // Изменение статуса сделки
        Route::get('deals/{deal}/status/{status}', [DealsController::class, 'changeStatus'])
            ->name('clients.deals.status');
    });

    // ========= СЧЕТА КЛИЕНТА =========
    Route::prefix('clients/{client}')->group(function () {
        // Список счетов
        Route::get('invoices', [InvoicesController::class, 'index'])->name('clients.invoices.index');
        // Создание счета
        Route::post('invoices', [InvoicesController::class, 'store'])->name('clients.invoices.store');
        // Обновление счета
        Route::put('invoices/{invoice}', [InvoicesController::class, 'update'])->name('clients.invoices.update');
        // Удаление счета
        Route::delete('invoices/{invoice}', [InvoicesController::class, 'destroy'])->name('clients.invoices.destroy');
        // Изменение статуса счета
        Route::get('invoices/{invoice}/status/{status}', [InvoicesController::class, 'changeStatus'])
            ->name('clients.invoices.status');
    });

    // Банковские реквизиты клиента
    Route::prefix('clients/{client}/bank-accounts')->name('clients.bank-accounts.')->group(function () {
        Route::post('/', [ClientController::class, 'addBankAccount'])->name('store');
        Route::put('/{bankAccount}', [ClientController::class, 'updateBankAccount'])->name('update');
        Route::delete('/{bankAccount}', [ClientController::class, 'deleteBankAccount'])->name('destroy');
        Route::post('/{bankAccount}/set-default', [ClientController::class, 'setDefaultBankAccount'])->name('set-default');
    });

    // ========= ПЛАТЕЖИ КЛИЕНТА =========
    Route::prefix('clients/{client}/invoices/{invoice}')->group(function () {
        // Список платежей
        Route::get('payments', [PaymentsController::class, 'index'])->name('clients.payments.index');
        // Создание платежа
        Route::post('payments', [PaymentsController::class, 'store'])->name('clients.payments.store');
        // Удаление платежа
        Route::delete('payments/{payment}', [PaymentsController::class, 'destroy'])->name('clients.payments.destroy');
    });

}); // Конец группы middleware auth для модулей клиента

// ========= НАСТРОЙКИ СИСТЕМЫ =========
Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index')->middleware('auth');
Route::get('/settings/general', [SettingsController::class, 'general'])->name('settings.general')->middleware('auth');
Route::get('/settings/backup', [SettingsController::class, 'backup'])->name('settings.backup')->middleware('auth');
Route::get('/settings/system', [SettingsController::class, 'system'])->name('settings.system')->middleware('auth');

// ========= УПРАВЛЕНИЕ ПОЛЬЗОВАТЕЛЯМИ =========
Route::middleware(['auth'])->group(function () {
    // Список пользователей
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    // Создание пользователя
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    // Обновление пользователя
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    // Удаление пользователя
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    // Переключение статуса пользователя
    Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    // Форма создания сотрудника
    Route::get('/users/create', [UserManagementController::class, 'createEmployee'])->name('users.create');
    // API: Получение должностей отдела
    Route::get('/api/departments/{department}/positions', [UserManagementController::class, 'getDepartmentPositions'])->name('api.departments.positions');
    // API: Получение данных пользователя
    Route::get('/api/users/{user}', [UserManagementController::class, 'show'])->name('api.users.show');
});

// ========= УПРАВЛЕНИЕ РОЛЯМИ =========
Route::middleware(['auth'])->group(function () {
    // Список ролей
    Route::get('/roles', [RolePermissionController::class, 'rolesIndex'])->name('roles.index');
    // Создание роли
    Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
    // Обновление роли
    Route::put('/roles/{role}', [RolePermissionController::class, 'updateRole'])->name('roles.update');
    // Удаление роли
    Route::delete('/roles/{role}', [RolePermissionController::class, 'destroyRole'])->name('roles.destroy');
    // API: Получение данных роли
    Route::get('/api/roles/{role}', [RolePermissionController::class, 'show'])->name('api.roles.show');
});

// ========= УПРАВЛЕНИЕ ПРАВАМИ ДОСТУПА =========
Route::middleware(['auth'])->group(function () {
    // Список прав доступа
    Route::get('/permissions', [RolePermissionController::class, 'permissionsIndex'])->name('permissions.index');
    // Синхронизация прав доступа для роли
    Route::post('/roles/{role}/permissions', [RolePermissionController::class, 'syncRolePermissions'])->name('roles.permissions.sync');
});

// ========= УПРАВЛЕНИЕ ОТДЕЛАМИ И ДОЛЖНОСТЯМИ =========
Route::middleware(['auth'])->group(function () {
    // Список отделов
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    // Создание отдела
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    // Обновление отдела
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    // Удаление отдела
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    
    // ========= ДОЛЖНОСТИ =========
    // Создание должности
    Route::post('/positions', [DepartmentController::class, 'storePosition'])->name('positions.store');
    // Обновление должности
    Route::put('/positions/{position}', [DepartmentController::class, 'updatePosition'])->name('positions.update');
    // Удаление должности
    Route::delete('/positions/{position}', [DepartmentController::class, 'destroyPosition'])->name('positions.destroy');
    // Назначение пользователя на должность
    Route::post('/users/assign-to-position', [UserManagementController::class, 'assignToPosition'])->name('users.assign-to-position');

    // ========= API МАРШРУТЫ =========
    // API: Получение данных отдела
    Route::get('/api/departments/{department}', [DepartmentController::class, 'show'])->name('api.departments.show');
    // API: Получение данных должности
    Route::get('/api/positions/{position}', [DepartmentController::class, 'showPosition'])->name('api.positions.show');
});

// ========= УПРАВЛЕНИЕ РЕЗЕРВНЫМИ КОПИЯМИ =========
Route::middleware(['auth'])->prefix('backup')->group(function () {
    // Главная страница резервных копий
    Route::get('/', [BackupController::class, 'index'])->name('backup.index');
    // Создание резервной копии
    Route::post('/create', [BackupController::class, 'createBackup'])->name('backup.create');
    // Скачивание резервной копии
    Route::get('/download/{filename}', [BackupController::class, 'downloadBackup'])->name('backup.download');
    // Удаление резервной копии
    Route::delete('/delete/{filename}', [BackupController::class, 'deleteBackup'])->name('backup.delete');
    // Форма восстановления из резервной копии
    Route::get('/restore', [BackupController::class, 'showRestoreForm'])->name('backup.restore');
    // Восстановление из резервной копии
    Route::post('/restore', [BackupController::class, 'restoreBackup'])->name('backup.restore.submit');
    // Проверка существования базы данных
    Route::get('/check-database', [BackupController::class, 'checkDatabaseExists'])->name('backup.check-database');
});