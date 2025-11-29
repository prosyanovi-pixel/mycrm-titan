<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Position extends Model
{
    protected $fillable = [
        'name',
        'department_id', 
        'parent_position_id',
        'level',
        'permissions',
        'is_manager'
    ];

    // Отношение к отделу
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // Отношение к родительской должности
    public function parent()
    {
        return $this->belongsTo(Position::class, 'parent_position_id');
    }

    // Отношение к пользователям на этой должности
    public function users()
    {
        return $this->hasMany(User::class);
    }
}