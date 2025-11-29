<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientBankAccount extends Model
{
    protected $table = 'client_bank_accounts';

    protected $fillable = [
        'client_id',
        'bank_name',
        'account_number',
        'correspondent_account',
        'bik',
        'inn',
        'kpp',
        'currency',
        'is_default',
        'notes'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    /**
     * Клиент, к которому принадлежит банковский счет
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Получить список доступных валют
     */
    public static function getCurrencies()
    {
        return [
            'RUB' => 'Рубли (RUB)',
            'USD' => 'Доллары (USD)',
            'EUR' => 'Евро (EUR)',
            'CNY' => 'Юани (CNY)'
        ];
    }
}