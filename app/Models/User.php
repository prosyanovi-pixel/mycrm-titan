<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'last_name',
        'first_name', 
        'middle_name',
        'email',
        'nickname',
        'position',
        'role_id',
        'phone',
        'password',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ==================== НОВЫЕ МЕТОДЫ ДЛЯ СИСТЕМЫ ПРАВ ====================

    /**
     * Полное ФИО
     */
    public function getFullNameAttribute()
    {
        return trim($this->last_name . ' ' . $this->first_name . ' ' . ($this->middle_name ?? ''));
    }

    /**
     * Короткое ФИО (Иванов П.С.)
     */
    public function getShortNameAttribute()
    {
        $firstName = mb_substr($this->first_name, 0, 1) . '.';
        $middleName = $this->middle_name ? mb_substr($this->middle_name, 0, 1) . '.' : '';
        
        return $this->last_name . ' ' . $firstName . $middleName;
    }

    /**
     * Отношение к роли
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Отношение к разделам меню через права доступа
     */
    public function menuPermissions()
    {
        return $this->belongsToMany(MenuSection::class, 'user_section_permissions')
                    ->withPivot('access_level')
                    ->withTimestamps();
    }

    /**
     * Получить доступные разделы меню
     */
    public function getAccessibleMenu()
    {
        return $this->menuPermissions()
                    ->where('access_level', '!=', 'none')
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->get();
    }

    /**
     * Проверка доступа к разделу
     */
    public function canAccessSection($sectionId, $minLevel = 'view')
    {
        $levels = ['none' => 0, 'view' => 1, 'edit' => 2];
        
        $permission = $this->menuPermissions()
                          ->where('menu_section_id', $sectionId)
                          ->first();
        
        if (!$permission) return false;
        
        return $levels[$permission->pivot->access_level] >= $levels[$minLevel];
    }

    /**
     * Проверка ролей
     */
    public function isAdmin()
    {
        return $this->role_id === 1;
    }

    public function isManager()
    {
        return $this->role_id === 2;
    }

    public function isRegularUser()
    {
        return $this->role_id === 3;
    }

    /**
     * Активация/деактивация пользователя
     */
    public function toggleStatus()
    {
        $this->is_active = !$this->is_active;
        return $this->save();
    }

    /**
     * Scope для активных пользователей
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для пользователей с определенной ролью
     */
    public function scopeWithRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }
    /**
     * Для обратной совместимости со старым полем name
     */
    public function getNameAttribute()
    {
        return $this->last_name . ' ' . $this->first_name;
    }
    /**
     * Scope для поиска по ФИО или email
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('last_name', 'like', "%{$search}%")
              ->orWhere('first_name', 'like', "%{$search}%")
              ->orWhere('middle_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('nickname', 'like', "%{$search}%");
        });
    }
}