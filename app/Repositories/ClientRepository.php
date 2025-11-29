<?php
namespace App\Repositories;

use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientRepository
{
    protected $model;

    public function __construct(Client $client)
    {
        $this->model = $client;
    }

    /**
     * Базовый поиск: если Scout включён — ищем через Scout, иначе через SQL.
     *
     * @param array $filters
     * @param int $perPage
     * @param string $sort
     * @param string $order
     * @return LengthAwarePaginator
     */
    public function paginate(array $filters = [], int $perPage = 20, string $sort = 'id', string $order = 'desc')
    {
        $query = $this->model->newQuery();

        // Поиск: если передан 'q'
        if (!empty($filters['q'])) {
            // Если модель Searchable (Scout), используем scout
            if (method_exists($this->model, 'search')) {
                $scoutResult = $this->model::search($filters['q']);

                // если есть фильтры по status/responsible_id — применяем через whereRaw join
                // упрощённый вариант: получаем ids и query->whereIn
                $ids = $scoutResult->keys();
                if (!empty($ids)) {
                    $query->whereIn('id', $ids);
                } else {
                    // ничего не найдено
                    return new LengthAwarePaginator([], 0, $perPage);
                }
            } else {
                $query->where(function($q) use ($filters) {
                    $q->where('company_name', 'like', '%'.$filters['q'].'%')
                      ->orWhere('last_name', 'like', '%'.$filters['q'].'%')
                      ->orWhere('first_name', 'like', '%'.$filters['q'].'%')
                      ->orWhere('inn', 'like', '%'.$filters['q'].'%')
                      ->orWhere('phone', 'like', '%'.$filters['q'].'%');
                });
            }
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['responsible_id'])) {
            $query->where('responsible_id', $filters['responsible_id']);
        }

        // сортировка защита
        $allowedSorts = ['id','company_name','last_name','inn','status','created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }

        $query->orderBy($sort, $order);

        return $query->paginate($perPage)->appends(request()->query());
    }

    public function find(int $id): ?Client
    {
        return $this->model->find($id);
    }

    public function create(array $data): Client
    {
        return $this->model->create($data);
    }

    public function update(Client $client, array $data): Client
    {
        $client->update($data);
        return $client->fresh();
    }

    public function delete(Client $client): void
    {
        $client->delete();
    }
}
