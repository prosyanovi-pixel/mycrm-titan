<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * Доступные статусы задачи
     */
    public const STATUS_OPEN        = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE        = 'done';

    /**
     * Приоритеты
     */
    public const PRIORITY_LOW    = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH   = 'high';

    /**
     * Отношения
     */

    // Клиент
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Исполнитель
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope’ы для фильтрации
     */
    public function scopeStatus($query, $value)
    {
        if ($value) {
            return $query->where('status', $value);
        }
        return $query;
    }

    public function scopePriority($query, $value)
    {
        if ($value) {
            return $query->where('priority', $value);
        }
        return $query;
    }

    public function scopeDueDateBetween($query, $from, $to)
    {
        if ($from && $to) {
            return $query->whereBetween('due_date', [$from, $to]);
        }
        return $query;
    }

    /**
     * Готовый accessor → красиво выводим статус
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_OPEN        => 'Открыта',
            self::STATUS_IN_PROGRESS => 'В работе',
            self::STATUS_DONE        => 'Завершена',
            default => 'Неизвестно',
        };
    }

    /**
     * Готовый accessor → цвет для бейджа
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_OPEN        => 'secondary',
            self::STATUS_IN_PROGRESS => 'warning',
            self::STATUS_DONE        => 'success',
            default => 'dark',
        };
    }

    /**
     * Просрочена ли задача
     */
    public function getIsOverdueAttribute()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== self::STATUS_DONE;
    }
}
