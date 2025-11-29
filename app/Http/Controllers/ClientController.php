<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientBankAccount;
use App\Models\User;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\NotificationHelper;
require_once app_path('Helpers/NotificationHelper.php');

class ClientController extends Controller
{
    protected $service;

    public function __construct(ClientService $service)
    {
        $this->service = $service;
    }

    /**
     * Показать список клиентов
     */
    public function index(Request $request)
    {
        $filters = [
            'q' => $request->input('search'),
            'status' => $request->input('status'),
            'responsible_id' => $request->input('responsible_id'),
        ];

        $perPage = (int)$request->input('per_page', 20);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'desc');

        $clients = $this->service->paginate($filters, $perPage, $sort, $order);

        // Используем полное имя вместо name
        $users = User::select('id', 'last_name', 'first_name', 'middle_name')
                    ->orderBy('last_name')
                    ->orderBy('first_name')
                    ->get();

        return view('clients.index', compact('clients','users'));
    }

    /**
     * Показать детальную информацию о клиенте
     */
    public function show(Client $client)
    {
        $perPage = request('per_page', 10);
        
        // Загружаем банковские счета с сортировкой
        $client->load(['bankAccounts' => function($query) {
            $query->orderBy('is_default', 'desc')->orderBy('created_at', 'desc');
        }]);

        $logs = $client->logs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $users = User::whereIn('id', $client->logs()->pluck('user_id')->unique()->filter())->get();

        $client->load(['tasks', 'deals', 'interactions', 'files', 'invoices']);

        return view('clients.show', compact('client', 'logs', 'users'));
    }

    /**
     * Показать все задачи (общий список)
     */
    public function allTasks(Request $request)
    {
        $tasks = ClientTask::with(['assignedUser', 'client'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Показать форму создания клиента
     */
    public function create()
    {
        // Используем полное имя вместо name
        $users = User::select('id', 'last_name', 'first_name', 'middle_name')
                    ->orderBy('last_name')
                    ->orderBy('first_name')
                    ->get();
        
        return view('clients.create', compact('users'));
    }

    /**
     * Показать форму редактирования клиента
     */
    public function edit(Client $client)
    {
        // Используем полное имя вместо name
        $users = User::select('id', 'last_name', 'first_name', 'middle_name')
                    ->orderBy('last_name')
                    ->orderBy('first_name')
                    ->get();
        
        return view('clients.edit', compact('client','users'));
    }

    /**
     * Удалить клиента
     */
    public function destroy(Client $client)
    {
        $this->service->delete($client);
        return redirect()->route('clients.index')->with('success','Клиент удалён');
    }

    /**
     * Отношение с логами
     */
    public function logs()
    {
        return $this->hasMany(ClientLog::class);
    }

    /**
     * Создать нового клиента
     */
    public function store(Request $request)
    {
        \Log::info('=== CLIENT STORE METHOD CALLED ===');
        \Log::info('Request data:', $request->all());

        try {
            // Базовые правила валидации
            $rules = [
                'type' => 'required|in:individual,entrepreneur,legal',
                'status' => 'required|in:lead,active,inactive',
            ];

            // Правила для ИНН в зависимости от типа
            if (in_array($request->type, ['entrepreneur', 'legal'])) {
                $rules['inn'] = 'required|string|min:10|max:12';
            } else {
                $rules['inn'] = 'nullable|string|min:10|max:12';
            }

            // Правила для имен в зависимости от типа
            if (in_array($request->type, ['individual', 'entrepreneur'])) {
                $rules['first_name'] = 'required|string|min:2';
                $rules['last_name'] = 'required|string|min:2';
            } else {
                $rules['company_name'] = 'required|string|min:2';
            }

            \Log::info('Validation rules:', $rules);

            $validated = $request->validate($rules);
            \Log::info('Validation passed', $validated);

            // Базовые данные
            $clientData = [
                'type' => $validated['type'],
                'status' => $validated['status'],
                'created_by' => auth()->id(),
            ];

            // Добавляем ИНН если есть
            if (!empty($validated['inn'])) {
                $clientData['inn'] = $validated['inn'];
            }

            // Добавляем имя в зависимости от типа
            if ($request->type === 'legal') {
                $clientData['company_name'] = $validated['company_name'];
                $clientData['first_name'] = null;
                $clientData['last_name'] = null;
            } else {
                $clientData['first_name'] = $validated['first_name'];
                $clientData['last_name'] = $validated['last_name'];
                $clientData['company_name'] = null;
            }

            // Остальные поля если есть
            $optionalFields = ['phone', 'email', 'address', 'responsible_id', 'source', 'notes', 'middle_name', 'ogrn', 'kpp', 'ogrnip', 'legal_form', 'legal_type'];
            foreach ($optionalFields as $field) {
                if ($request->has($field) && $request->$field) {
                    $clientData[$field] = $request->$field;
                }
            }

            // Теги
            if ($request->has('tags') && $request->tags) {
                $clientData['tags'] = array_map('trim', explode(',', $request->tags));
            }

            \Log::info('Final client data:', $clientData);

            // Создаем клиента
            $client = Client::create($clientData);

            \Log::info('Client created successfully ID: ' . $client->id);

            return redirect()->route('clients.show', $client)
                ->with('success', 'Клиент успешно создан');

        } catch (\Exception $e) {
            \Log::error('Error creating client: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->withInput()->with('error', 'Ошибка при создании клиента: ' . $e->getMessage());
        }
    }

    /**
     * Обновить данные клиента
     */
    public function update(Request $request, Client $client)
    {
        \Log::info('=== CLIENT UPDATE METHOD CALLED ===');
        \Log::info('Request data:', $request->all());
        \Log::info('Client ID:', ['id' => $client->id]);

        try {
            // Базовые правила валидации
            $rules = [
                'type' => 'required|in:individual,entrepreneur,legal',
                'status' => 'required|in:lead,active,inactive',
                'legal_form' => 'nullable|string',
                'inn' => 'required|string',
                'ogrn' => 'nullable|string',
                'kpp' => 'nullable|string',
                'ogrnip' => 'nullable|string',
                'phone' => 'nullable|string',
                'email' => 'nullable|email',
                'address' => 'nullable|string',
                'responsible_id' => 'nullable|exists:users,id',
                'source' => 'nullable|string',
                'total_revenue' => 'nullable|numeric|min:0',
                'tags' => 'nullable|string',
                'notes' => 'nullable|string',
            ];

            // Динамические правила в зависимости от типа
            if (in_array($request->type, ['individual', 'entrepreneur'])) {
                $rules['first_name'] = 'required|string|max:255';
                $rules['last_name'] = 'required|string|max:255';
            } else {
                $rules['company_name'] = 'required|string|max:255';
            }

            $validated = $request->validate($rules);

            \Log::info('Validation passed for update');

            // Преобразуем теги из строки в массив
            if ($request->has('tags') && $request->tags) {
                $validated['tags'] = array_map('trim', explode(',', $request->tags));
            }

            \Log::info('Updating client with data:', $validated);

            $client->update($validated);

            \Log::info('Client updated successfully');

            return redirect()->route('clients.show', $client)
                ->with('success', 'Данные клиента обновлены');

        } catch (\Exception $e) {
            \Log::error('Error updating client: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->withInput()->with('error', 'Ошибка при обновлении клиента: ' . $e->getMessage());
        }
    }

    /**
     * Добавить банковский счет для клиента
     */
    public function addBankAccount(Request $request, Client $client)
    {
        \Log::info('=== ADD BANK ACCOUNT METHOD CALLED ===');
        \Log::info('Client ID:', ['id' => $client->id]);
        \Log::info('Request data:', $request->all());

        try {
            $validated = $request->validate([
                'bank_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:50',
                'correspondent_account' => 'nullable|string|max:50',
                'bik' => 'nullable|string|max:20',
                'inn' => 'nullable|string|max:20',
                'kpp' => 'nullable|string|max:20',
                'currency' => 'required|in:RUB,USD,EUR,CNY',
                'is_default' => 'boolean',
                'notes' => 'nullable|string'
            ]);

            \Log::info('Bank account validation passed');

            DB::transaction(function () use ($client, $validated) {
                // Если это основной счет, снимаем флаг с других счетов
                if ($validated['is_default'] ?? false) {
                    $client->bankAccounts()->update(['is_default' => false]);
                    \Log::info('Removed default flag from other accounts');
                }

                // Создаем новый банковский счет
                $client->bankAccounts()->create($validated);
                \Log::info('Bank account created successfully');
            });

            return redirect()->back()->with('success', 'Банковский счет успешно добавлен');

        } catch (\Exception $e) {
            \Log::error('Error adding bank account: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->withInput()->with('error', 'Ошибка при добавлении банковского счета: ' . $e->getMessage());
        }
    }

    /**
     * Обновить банковский счет клиента
     */
    public function updateBankAccount(Request $request, Client $client, ClientBankAccount $bankAccount)
    {
        \Log::info('=== UPDATE BANK ACCOUNT METHOD CALLED ===');
        \Log::info('Client ID:', ['id' => $client->id]);
        \Log::info('Bank Account ID:', ['id' => $bankAccount->id]);
        \Log::info('Request data:', $request->all());

        // Проверяем, что счет принадлежит клиенту
        if ($bankAccount->client_id !== $client->id) {
            \Log::warning('Bank account does not belong to client', [
                'bank_account_client_id' => $bankAccount->client_id,
                'client_id' => $client->id
            ]);
            abort(404, 'Счет не найден');
        }

        try {
            $validated = $request->validate([
                'bank_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:50',
                'correspondent_account' => 'nullable|string|max:50',
                'bik' => 'nullable|string|max:20',
                'inn' => 'nullable|string|max:20',
                'kpp' => 'nullable|string|max:20',
                'currency' => 'required|in:RUB,USD,EUR,CNY',
                'is_default' => 'boolean',
                'notes' => 'nullable|string'
            ]);

            \Log::info('Bank account update validation passed');

            DB::transaction(function () use ($client, $bankAccount, $validated) {
                // Если это основной счет, снимаем флаг с других счетов
                if ($validated['is_default'] ?? false) {
                    $client->bankAccounts()->where('id', '!=', $bankAccount->id)->update(['is_default' => false]);
                    \Log::info('Removed default flag from other accounts');
                }

                // Обновляем банковский счет
                $bankAccount->update($validated);
                \Log::info('Bank account updated successfully');
            });

            return redirect()->back()->with('success', 'Банковский счет успешно обновлен');

        } catch (\Exception $e) {
            \Log::error('Error updating bank account: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->withInput()->with('error', 'Ошибка при обновлении банковского счета: ' . $e->getMessage());
        }
    }

    /**
     * Удалить банковский счет клиента
     */
    public function deleteBankAccount(Client $client, ClientBankAccount $bankAccount)
    {
        \Log::info('=== DELETE BANK ACCOUNT METHOD CALLED ===');
        \Log::info('Client ID:', ['id' => $client->id]);
        \Log::info('Bank Account ID:', ['id' => $bankAccount->id]);

        // Проверяем, что счет принадлежит клиенту
        if ($bankAccount->client_id !== $client->id) {
            \Log::warning('Bank account does not belong to client', [
                'bank_account_client_id' => $bankAccount->client_id,
                'client_id' => $client->id
            ]);
            abort(404, 'Счет не найден');
        }

        try {
            $bankAccount->delete();
            \Log::info('Bank account deleted successfully');

            return redirect()->back()->with('success', 'Банковский счет успешно удален');

        } catch (\Exception $e) {
            \Log::error('Error deleting bank account: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Ошибка при удалении банковского счета: ' . $e->getMessage());
        }
    }

    /**
     * Установить банковский счет как основной
     */
    public function setDefaultBankAccount(Client $client, ClientBankAccount $bankAccount)
    {
        \Log::info('=== SET DEFAULT BANK ACCOUNT METHOD CALLED ===');
        \Log::info('Client ID:', ['id' => $client->id]);
        \Log::info('Bank Account ID:', ['id' => $bankAccount->id]);

        // Проверяем, что счет принадлежит клиенту
        if ($bankAccount->client_id !== $client->id) {
            \Log::warning('Bank account does not belong to client', [
                'bank_account_client_id' => $bankAccount->client_id,
                'client_id' => $client->id
            ]);
            abort(404, 'Счет не найден');
        }

        try {
            DB::transaction(function () use ($client, $bankAccount) {
                // Снимаем флаг со всех счетов клиента
                $client->bankAccounts()->update(['is_default' => false]);
                \Log::info('Removed default flag from all accounts');
                
                // Устанавливаем флаг для выбранного счета
                $bankAccount->update(['is_default' => true]);
                \Log::info('Set default flag for account: ' . $bankAccount->id);
            });

            return redirect()->back()->with('success', 'Счет установлен как основной');

        } catch (\Exception $e) {
            \Log::error('Error setting default bank account: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Ошибка при установке основного счета: ' . $e->getMessage());
        }
    }
}