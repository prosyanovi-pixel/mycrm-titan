<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientInteraction;
use App\Models\ClientLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientInteractionController extends Controller
{
    /**
     * Сохранение нового взаимодействия клиента
     */
    public function store(Request $request, Client $client)
    {
        $data = $request->validate([
            'type' => 'required|in:meeting,call,email,note',
            'title' => 'nullable|string|max:255',
            'description' => 'required|string',
            'outcome' => 'nullable|string',
        ]);

        $data['client_id'] = $client->id;
        $data['user_id'] = Auth::id();

        $interaction = ClientInteraction::create($data);

        // Обновляем активность клиента
        $client->update([
            'last_activity_at' => now(),
            'activity_score' => $client->activity_score + 1,
        ]);

        // Логирование
        ClientLog::create([
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'action' => 'Создано взаимодействие',
            'new_value' => json_encode($interaction),
        ]);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Взаимодействие добавлено.');
    }

    /**
     * Обновление взаимодействия
     */
    public function update(Request $request, Client $client, ClientInteraction $interaction)
    {
        $data = $request->validate([
            'type' => 'required|in:meeting,call,email,note',
            'title' => 'nullable|string|max:255',
            'description' => 'required|string',
            'outcome' => 'nullable|string',
        ]);

        $old = $interaction->toArray();

        $interaction->update($data);

        // Логирование
        ClientLog::create([
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'action' => 'Обновлено взаимодействие',
            'old_value' => json_encode($old),
            'new_value' => json_encode($interaction->fresh()),
        ]);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Взаимодействие обновлено.');
    }

    /**
     * Удаление взаимодействия
     */
    public function destroy(Client $client, ClientInteraction $interaction)
    {
        $old = $interaction->toArray();

        $interaction->delete();

        // Логирование
        ClientLog::create([
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'action' => 'Удалено взаимодействие',
            'old_value' => json_encode($old),
        ]);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Взаимодействие удалено.');
    }
}