<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
   public function index(Request $request)
{
    $tab = $request->get('tab', 'general');
    
    $users = User::with(['roles', 'department', 'position'])->get();
    $roles = Role::withCount(['users', 'permissions'])->get();
    
    // Загружаем departments с positions и users для positions
    $departments = Department::with([
        'manager', 
        'children',
        'positions.users' // ДОБАВЛЯЕМ ЗАГРУЗКУ ПОЛЬЗОВАТЕЛЕЙ ДЛЯ ДОЛЖНОСТЕЙ
    ])->whereNull('parent_id')->get();
    
    $positions = Position::with(['department', 'users'])->get(); // ДОБАВЛЯЕМ users
    
    // Вручную добавляем positions к departments (если нужно)
    foreach ($departments as $department) {
        if (!$department->relationLoaded('positions')) {
            $department->positions = $positions->where('department_id', $department->id);
        }
    }

    // Пользователи без должности
    $usersWithoutPosition = $users->where('position_id', null);

    // Модули
    $modules = collect([
        ['id' => 1, 'name' => 'clients', 'description' => 'Управление клиентами'],
        ['id' => 2, 'name' => 'deals', 'description' => 'Управление сделками'],
        ['id' => 3, 'name' => 'tasks', 'description' => 'Управление задачами'],
        ['id' => 4, 'name' => 'invoices', 'description' => 'Управление счетами'],
        ['id' => 5, 'name' => 'reports', 'description' => 'Отчеты и аналитика'],
        ['id' => 6, 'name' => 'settings', 'description' => 'Настройки системы'],
        ['id' => 7, 'name' => 'users', 'description' => 'Управление пользователями'],
        ['id' => 8, 'name' => 'catalog', 'description' => 'Каталог товаров/услуг'],
    ]);
    
    return view('settings.index', compact(
        'tab', 
        'users', 
        'roles', 
        'departments', 
        'positions', 
        'modules',
        'usersWithoutPosition'
    ));
}

    public function general()
    {
        $users = User::with(['roles', 'department', 'position'])->get();
        $roles = Role::withCount(['users', 'permissions'])->get();
        $departments = Department::with(['manager', 'children'])->whereNull('parent_id')->get();
        $positions = Position::with('department')->get();

        // Добавляем модули статически
        $modules = collect([
            ['id' => 1, 'name' => 'clients', 'description' => 'Управление клиентами'],
            ['id' => 2, 'name' => 'deals', 'description' => 'Управление сделками'],
            ['id' => 3, 'name' => 'tasks', 'description' => 'Управление задачами'],
            ['id' => 4, 'name' => 'invoices', 'description' => 'Управление счетами'],
            ['id' => 5, 'name' => 'reports', 'description' => 'Отчеты и аналитика'],
            ['id' => 6, 'name' => 'settings', 'description' => 'Настройки системы'],
            ['id' => 7, 'name' => 'users', 'description' => 'Управление пользователями'],
            ['id' => 8, 'name' => 'catalog', 'description' => 'Каталог товаров/услуг'],
        ]);
        
        return view('settings.index', [
            'tab' => 'general',
            'users' => $users,
            'roles' => $roles,
            'departments' => $departments,
            'positions' => $positions,
            'modules' => $modules
        ]);
    }

    public function backup()
    {
        $users = User::with(['roles', 'department', 'position'])->get();
        $roles = Role::withCount(['users', 'permissions'])->get();
        $departments = Department::with(['manager', 'children'])->whereNull('parent_id')->get();
        $positions = Position::with('department')->get();

        // Добавляем модули статически
        $modules = collect([
            ['id' => 1, 'name' => 'clients', 'description' => 'Управление клиентами'],
            ['id' => 2, 'name' => 'deals', 'description' => 'Управление сделками'],
            ['id' => 3, 'name' => 'tasks', 'description' => 'Управление задачами'],
            ['id' => 4, 'name' => 'invoices', 'description' => 'Управление счетами'],
            ['id' => 5, 'name' => 'reports', 'description' => 'Отчеты и аналитика'],
            ['id' => 6, 'name' => 'settings', 'description' => 'Настройки системы'],
            ['id' => 7, 'name' => 'users', 'description' => 'Управление пользователями'],
            ['id' => 8, 'name' => 'catalog', 'description' => 'Каталог товаров/услуг'],
        ]);
        
        return view('settings.index', [
            'tab' => 'backup',
            'users' => $users,
            'roles' => $roles,
            'departments' => $departments,
            'positions' => $positions,
            'modules' => $modules
        ]);
    }

    public function system()
    {
        $users = User::with(['roles', 'department', 'position'])->get();
        $roles = Role::withCount(['users', 'permissions'])->get();
        $departments = Department::with(['manager', 'children'])->whereNull('parent_id')->get();
        $positions = Position::with('department')->get();

        // Добавляем модули статически
        $modules = collect([
            ['id' => 1, 'name' => 'clients', 'description' => 'Управление клиентами'],
            ['id' => 2, 'name' => 'deals', 'description' => 'Управление сделками'],
            ['id' => 3, 'name' => 'tasks', 'description' => 'Управление задачами'],
            ['id' => 4, 'name' => 'invoices', 'description' => 'Управление счетами'],
            ['id' => 5, 'name' => 'reports', 'description' => 'Отчеты и аналитика'],
            ['id' => 6, 'name' => 'settings', 'description' => 'Настройки системы'],
            ['id' => 7, 'name' => 'users', 'description' => 'Управление пользователями'],
            ['id' => 8, 'name' => 'catalog', 'description' => 'Каталог товаров/услуг'],
        ]);
        
        return view('settings.index', [
            'tab' => 'system',
            'users' => $users,
            'roles' => $roles,
            'departments' => $departments,
            'positions' => $positions,
            'modules' => $modules
        ]);
    }
}