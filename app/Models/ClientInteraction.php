<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientInteraction extends Model
{
    protected $fillable = [
        'client_id',
        'user_id', 
        'type',
        'title',
        'description',
        'outcome',
        'interaction_date', // добавьте это поле
    ];

    protected $casts = [
        'interaction_date' => 'datetime',
    ];

    /**
     * Get the client that owns the interaction.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user that created the interaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}