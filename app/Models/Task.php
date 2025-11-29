<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'client_tasks';

    protected $fillable = [
        'title',
        'description', 
        'status',
        'priority',
        'due_date',
        'user_id', // ← меняем здесь
        'client_id'
    ];

    // Обновляем название отношения
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'user_id'); // ← и здесь
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}