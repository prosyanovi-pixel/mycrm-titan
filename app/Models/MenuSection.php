<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuSection extends Model
{
    protected $fillable = ['name', 'page_title', 'route_name', 'icon', 'order', 'is_active'];
    
    public function userPermissions()
    {
        return $this->belongsToMany(User::class, 'user_section_permissions')
                    ->withPivot('access_level', 'created_at')
                    ->withTimestamps(false);
    }
    
    public function getUrlAttribute()
    {
        // Проверяем существование маршрута перед его использованием
        if ($this->route_name && \Illuminate\Support\Facades\Route::has($this->route_name)) {
            return route($this->route_name);
        }
        
        // Если маршрут не существует, возвращаем заглушку
        return $this->attributes['url'] ?? '#';
    }
    
    public function isActive()
    {
        if (!$this->route_name) {
            return false;
        }
        
        // Проверяем существование маршрута перед проверкой активности
        if (!\Illuminate\Support\Facades\Route::has($this->route_name)) {
            return false;
        }
        
        return request()->routeIs($this->route_name . '*');
    }
    
    /**
     * Получить заголовок страницы для текущего маршрута
     */
    public static function getCurrentPageTitle()
    {
        $currentRoute = request()->route()->getName();
        
        // Ищем раздел по route_name
        $section = static::where('route_name', $currentRoute)
                        ->orWhere('route_name', 'like', $currentRoute . '.%')
                        ->first();
        
        if ($section && $section->page_title) {
            return $section->page_title;
        }
        
        // Если не нашли в базе, используем fallback
        return static::getFallbackTitle($currentRoute);
    }
    
    /**
     * Резервные заголовки для страниц без записи в базе
     */
    protected static function getFallbackTitle($route)
    {
        $titles = [
            'dashboard' => 'Рабочий стол',
            'clients.index' => 'Клиенты',
            'clients.create' => 'Добавить клиента',
            'clients.edit' => 'Редактировать клиента',
            'clients.show' => 'Карточка клиента',
            'clients.deals.index' => 'Сделки',
            'clients.tasks.index' => 'Задачи',
            'clients.invoices.index' => 'Счета',
            'clients.files.index' => 'Файлы',
            'users.index' => 'Пользователи',
            'users.create' => 'Добавить пользователя',
            'roles.index' => 'Роли',
            'permissions.index' => 'Права доступа',
            'departments.index' => 'Отделы',
            'settings.index' => 'Настройки',
            'settings.general' => 'Общие настройки',
            'settings.backup' => 'Резервные копии',
            'settings.system' => 'Системные настройки',
            'backup.index' => 'Резервные копии',
        ];
        
        return $titles[$route] ?? 'CRM Система';
    }
}