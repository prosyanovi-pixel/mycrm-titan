<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Deal;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\NotificationHelper;

require_once app_path('Helpers/NotificationHelper.php');

class DealsController extends Controller
{
    /**
     * Список сделок клиента
     */
    public function index(Client $client)
    {
        $deals = $client->deals()->latest()->get();

        return view('clients.deals.index', compact('client', 'deals'));
    }

    /**
     * Общий список всех сделок
     */
    public function allDeals(Request $request)
    {
        $query = Deal::with(['client', 'createdByUser']);
        
        // Фильтр по поиску
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        // Фильтр по статусу
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Фильтр по клиенту
        if ($request->has('client_id') && $request->client_id != '') {
            $query->where('client_id', $request->client_id);
        }
        
        // Фильтр по сумме
        if ($request->has('amount_filter') && $request->amount_filter != '') {
            switch ($request->amount_filter) {
                case '0-50000':
                    $query->where('amount', '<=', 50000);
                    break;
                case '50000-200000':
                    $query->whereBetween('amount', [50000, 200000]);
                    break;
                case '200000-500000':
                    $query->whereBetween('amount', [200000, 500000]);
                    break;
                case '500000+':
                    $query->where('amount', '>=', 500000);
                    break;
            }
        }
        
        $deals = $query->orderBy('created_at', 'desc')
                    ->paginate($request->per_page ?? 10);
        
        $clients = Client::where('status', 'active')->get();
        $users = User::where('is_active', true)->get();
        
        return view('deals.index', compact('deals', 'clients', 'users'));
    }

    // ========= ОБЩИЕ МЕТОДЫ ДЛЯ СДЕЛОК =========

    /**
     * Форма создания общей сделки
     */
    public function create()
    {
        $clients = Client::where('status', 'active')->get();
        return view('deals.create', compact('clients'));
    }

    /**
     * Сохранение общей сделки
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title'   => 'required|string|max:255',
            'amount'  => 'required|numeric|min:0',
            'status'  => 'required|in:lead,proposal,negotiation,win,lost',
            'expected_close_at' => 'nullable|date',
        ]);

        try {
            $deal = Deal::create([
                'client_id'         => $request->client_id,
                'title'             => $request->title,
                'amount'            => $request->amount,
                'status'            => $request->status,
                'expected_close_at' => $request->expected_close_at,
                'created_by'        => Auth::id(),
            ]);

            // Обновление активности клиента
            $client = Client::find($request->client_id);
            $client->update([
                'last_activity_at' => now(),
            ]);

            NotificationHelper::success('✅ Сделка создана');
            return redirect()->route('deals.index');

        } catch (\Exception $e) {
            NotificationHelper::error('❌ Ошибка создания');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Форма редактирования общей сделки
     */
    public function edit(Deal $deal)
    {
        $clients = Client::where('status', 'active')->get();
        return view('deals.edit', compact('deal', 'clients'));
    }

    /**
     * Обновление общей сделки
     */
    public function update(Request $request, Deal $deal)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title'   => 'required|string|max:255',
            'amount'  => 'required|numeric|min:0',
            'status'  => 'required|in:lead,proposal,negotiation,win,lost',
            'expected_close_at' => 'nullable|date',
        ]);

        try {
            $oldStatus = $deal->status;
            $oldClientId = $deal->client_id;
            
            $deal->update($request->only([
                'client_id',
                'title',
                'amount',
                'status',
                'expected_close_at',
            ]));

            // Обновление активности клиента
            $client = Client::find($request->client_id);
            $client->update([
                'last_activity_at' => now(),
            ]);

            // Если выиграли сделку — добавляем к доходу клиента
            if ($deal->status === 'win' && $oldStatus !== 'win') {
                $client->update([
                    'total_revenue' => $client->total_revenue + $deal->amount,
                ]);
            }

            // Если сменили клиента и сделка была выиграна
            if ($oldClientId != $request->client_id && $oldStatus === 'win') {
                $oldClient = Client::find($oldClientId);
                $oldClient->update([
                    'total_revenue' => max(0, $oldClient->total_revenue - $deal->amount),
                ]);
            }

            NotificationHelper::success('✅ Сделка обновлена');
            return redirect()->route('deals.index');

        } catch (\Exception $e) {
            NotificationHelper::error('❌ Ошибка обновления');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Удаление общей сделки
     */
    public function destroy(Deal $deal)
    {
        try {
            // если была выигранная сделка — нужно вычесть сумму из revenue
            if ($deal->status === 'win') {
                $client = Client::find($deal->client_id);
                $client->update([
                    'total_revenue' => max(0, $client->total_revenue - $deal->amount),
                ]);
            }

            $deal->delete();

            NotificationHelper::success('✅ Сделка удалена');
            return redirect()->route('deals.index');

        } catch (\Exception $e) {
            NotificationHelper::error('❌ Ошибка удаления');
            return redirect()->route('deals.index');
        }
    }

    // ========= МЕТОДЫ ДЛЯ СДЕЛОК КОНКРЕТНОГО КЛИЕНТА =========

    /**
     * Создание сделки для конкретного клиента
     */
    public function storeForClient(Request $request, Client $client)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'amount'  => 'required|numeric|min:0',
            'status'  => 'required|in:lead,proposal,negotiation,win,lost',
            'expected_close_at' => 'nullable|date',
        ]);

        try {
            $deal = Deal::create([
                'client_id'         => $client->id,
                'title'             => $request->title,
                'amount'            => $request->amount,
                'status'            => $request->status,
                'expected_close_at' => $request->expected_close_at,
                'created_by'        => Auth::id(),
            ]);

            // Обновление активности клиента
            $client->update([
                'last_activity_at' => now(),
            ]);

            NotificationHelper::success('✅ Сделка создана');
            return redirect()->back();

        } catch (\Exception $e) {
            NotificationHelper::error('❌ Ошибка создания');
            return redirect()->back();
        }
    }

    /**
     * Форма создания сделки для клиента
     */
    public function createForClient(Client $client)
    {
        return view('clients.deals.create', compact('client'));
    }

    /**
     * Форма редактирования сделки клиента
     */
    public function editForClient(Client $client, Deal $deal)
    {
        if ($deal->client_id !== $client->id) abort(403);
        return view('clients.deals.edit', compact('client', 'deal'));
    }

    /**
     * Обновление сделки клиента
     */
    public function updateForClient(Request $request, Client $client, Deal $deal)
    {
        if ($deal->client_id !== $client->id) abort(403);

        $request->validate([
            'title'   => 'required|string|max:255',
            'amount'  => 'required|numeric|min:0',
            'status'  => 'required|in:lead,proposal,negotiation,win,lost',
            'expected_close_at' => 'nullable|date',
        ]);

        try {
            $oldStatus = $deal->status;
            $deal->update($request->only([
                'title',
                'amount',
                'status',
                'expected_close_at',
            ]));

            // Если выиграли сделку — добавляем к доходу клиента
            if ($deal->status === 'win' && $oldStatus !== 'win') {
                $client->update([
                    'total_revenue'   => $client->total_revenue + $deal->amount,
                    'last_activity_at' => now(),
                ]);
            }

            NotificationHelper::success('✅ Сделка обновлена');
            return redirect()->back();

        } catch (\Exception $e) {
            NotificationHelper::error('❌ Ошибка обновления');
            return redirect()->back();
        }
    }

    /**
     * Быстрая смена статуса
     */
    public function changeStatus(Client $client, Deal $deal, $status)
    {
        if ($deal->client_id !== $client->id) abort(403);

        if (!in_array($status, ['lead','proposal','negotiation','win','lost'])) {
            NotificationHelper::error('❌ Неверный статус');
            return redirect()->back();
        }

        try {
            $oldStatus = $deal->status;
            $deal->update(['status' => $status]);

            // Обновление метрик клиента
            if ($status === 'win' && $oldStatus !== 'win') {
                $client->update([
                    'total_revenue'   => $client->total_revenue + $deal->amount,
                    'last_activity_at' => now(),
                ]);
            }

            NotificationHelper::success('✅ Статус обновлён');
            return redirect()->back();

        } catch (\Exception $e) {
            NotificationHelper::error('❌ Ошибка смены статуса');
            return redirect()->back();
        }
    }

    /**
     * Удаление сделки клиента
     */
    public function destroyForClient(Client $client, Deal $deal)
    {
        if ($deal->client_id !== $client->id) abort(403);

        try {
            // если была выигранная сделка — нужно вычесть сумму из revenue
            if ($deal->status === 'win') {
                $client->update([
                    'total_revenue' => max(0, $client->total_revenue - $deal->amount),
                ]);
            }

            $deal->delete();

            NotificationHelper::success('✅ Сделка удалена');
            return redirect()->back();

        } catch (\Exception $e) {
            NotificationHelper::error('❌ Ошибка удаления');
            return redirect()->back();
        }
    }
}