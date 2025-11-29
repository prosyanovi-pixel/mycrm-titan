<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Kyslik\ColumnSortable\Sortable;
use Laravel\Scout\Searchable;

class Client extends Model
{
    use HasFactory, Sortable, Searchable;

    protected $fillable = [
        'type',

        // ФЛ
        'last_name', 'first_name', 'middle_name',

        // ИП
        'ogrnip', 'legal_form',

        // Юрлица
        'company_name', 'legal_type', 'ogrn', 'kpp',

        // Общие
        'inn', 'email', 'phone', 'address',
        'responsible_id', 'created_by',
        'status',
        'extra',

        // Дополнительные поля (добавьте эти)
        'source',
        'total_revenue',
        'tags',
        'notes',
        'activity_score',
        'last_activity_at'
    ];

    protected $casts = [
        'extra' => 'array',
        'tags' => 'array', // Добавьте это
        'total_revenue' => 'decimal:2',
        'last_activity_at' => 'datetime',
        'activity_score' => 'integer'
    ];

    public $sortable = [
        'id',
        'display_name',
        'type',
        'inn',
        'status',
        'responsible_id',
        'created_at',
        'total_revenue', // Добавьте это
        'last_activity_at', // Добавьте это
    ];

    public function displayNameSortable($query, $direction)
    {
        return $query->orderByRaw("
            CASE 
                WHEN type = 'legal' THEN company_name
                WHEN type = 'entrepreneur' THEN CONCAT('ИП ', last_name, ' ', first_name, ' ', middle_name)
                ELSE CONCAT(last_name, ' ', first_name, ' ', middle_name)
            END $direction
        ");
    }

    public function getDisplayNameAttribute()
    {
        if ($this->type === 'legal') {
            return $this->company_name ?: 'Без названия';
        }

        if ($this->type === 'entrepreneur') {
            $fio = trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name);
            return "ИП " . trim($fio);
        }

        return trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name);
    }

    // Добавьте этот метод для форматирования выручки
    public function getFormattedRevenueAttribute()
    {
        return number_format($this->total_revenue, 2, ',', ' ');
    }

    // Добавьте этот метод для форматирования тегов
    public function getTagsListAttribute()
    {
        return $this->tags ? implode(', ', $this->tags) : '';
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function interactions()
    {
        return $this->hasMany(ClientInteraction::class);
    }

    public function files()
    {
        return $this->hasMany(ClientFile::class);
    }

    public function tasks()
    {
        return $this->hasMany(ClientTask::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function logs()
    {
        return $this->hasMany(ClientLog::class);
    }

    public function getFullName()
    {
        if ($this->type === 'individual') {
            return trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name);
        } elseif ($this->type === 'legal') {
            return $this->company_name;
        } elseif ($this->type === 'entrepreneur') {
            return $this->last_name . ' ' . $this->first_name . ' (ИП)';
        }
        
        return 'Неизвестный тип';
    }
    /**
     * Scout → какие поля индексируем
     */
    public function toSearchableArray()
    {
        return [
            'id'           => $this->id,
            'type'         => $this->type,
            'display_name' => $this->display_name,
            'company_name' => $this->company_name,
            'full_name'    => trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name),
            'inn'          => $this->inn,
            'phone'        => $this->phone,
            'email'        => $this->email,
            'notes'        => $this->notes, // Добавьте это
            'tags'         => $this->tags ? implode(' ', $this->tags) : '', // Добавьте это
        ];
    }

    /**
     * Обновление активности клиента
     */
    public function updateActivity()
    {
        $this->update([
            'last_activity_at' => now(),
            'activity_score' => ($this->activity_score ?? 0) + 1
        ]);
    }

    /**
     * Банковские счета клиента
     */
    public function bankAccounts()
    {
        return $this->hasMany(ClientBankAccount::class);
    }

    /**
     * Основной банковский счет
     */
    public function defaultBankAccount()
    {
        return $this->hasOne(ClientBankAccount::class)->where('is_default', true);
    }




}