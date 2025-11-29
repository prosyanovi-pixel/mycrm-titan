<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'parent_id', 
        'manager_id', 
        'level', 
        'path', 
        'is_active'
    ];

    // Отношение к менеджеру
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Отношение к подотделам
    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    // Отношение к родительскому отделу
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    // Отношение к пользователям в отделе
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Добавьте это отношение - должности в отделе
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }
    public function getEmployeesCount()
    {
        return User::where('department_id', $this->id)->count();
    }
}