<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = [
        'client_id',
        'created_by',
        'title',
        'amount',
        'status',
        'expected_close_at',
        'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expected_close_at' => 'datetime', // Добавить эту строку
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}