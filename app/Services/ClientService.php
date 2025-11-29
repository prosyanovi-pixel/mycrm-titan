<?php
namespace App\Services;

use App\Models\Client;
use App\Models\ClientLog;
use App\Repositories\ClientRepository;
use Illuminate\Support\Facades\Auth;

class ClientService
{
    protected $repo;

    public function __construct(ClientRepository $repo)
    {
        $this->repo = $repo;
    }

    public function paginate(array $filters = [], int $perPage = 20, string $sort = 'id', string $order = 'desc')
    {
        return $this->repo->paginate($filters, $perPage, $sort, $order);
    }

    public function create(array $data): Client
    {
        // подготовка полей
        if (($data['type'] ?? null) === 'entrepreneur') {
            $data['legal_form'] = $data['legal_form'] ?? 'Индивидуальный предприниматель';
        }

        // ensure tags stored as JSON string or null
        if (!empty($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = json_encode($data['tags'], JSON_UNESCAPED_UNICODE);
        }

        $data['created_by'] = Auth::id();

        $client = $this->repo->create($data);

        ClientLog::create([
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'action' => 'create',
            'old_value' => null,
            'new_value' => json_encode($client->toArray(), JSON_UNESCAPED_UNICODE),
        ]);

        return $client;
    }

    public function update(Client $client, array $data): Client
    {
        $old = $client->toArray();

        if (($data['type'] ?? null) === 'entrepreneur') {
            $data['legal_form'] = $data['legal_form'] ?? 'Индивидуальный предприниматель';
        }

        if (!empty($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = json_encode($data['tags'], JSON_UNESCAPED_UNICODE);
        }

        $client = $this->repo->update($client, $data);

        // update activity
        $client->last_activity_at = now();
        $client->activity_score = ($client->activity_score ?: 0) + 1;
        $client->save();

        ClientLog::create([
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'action' => 'update',
            'old_value' => json_encode($old, JSON_UNESCAPED_UNICODE),
            'new_value' => json_encode($client->toArray(), JSON_UNESCAPED_UNICODE),
        ]);

        return $client;
    }

    public function delete(Client $client): void
    {
        ClientLog::create([
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'action' => 'delete',
            'old_value' => json_encode($client->toArray(), JSON_UNESCAPED_UNICODE),
            'new_value' => null,
        ]);

        $this->repo->delete($client);
    }
}
