<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientTask;
use App\Models\User;
use Illuminate\Http\Request;

class ClientTasksController extends Controller
{
    /**
     * Store a newly created task for a client.
     */
    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:open,in_progress,done',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $client->tasks()->create($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Задача успешно создана.');
    }

    /**
     * Show the form for creating a new task (общая задача).
     */
    public function create()
    {
        $clients = Client::where('status', 'active')->get();
        $users = User::where('is_active', true)->get();
        
        return view('tasks.create', compact('clients', 'users'));
    }

    /**
     * Store a newly created general task.
     */
    public function storeGeneral(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:open,in_progress,done',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        ClientTask::create($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Задача успешно создана.');
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(ClientTask $task)
    {
        $clients = Client::where('status', 'active')->get();
        $users = User::where('is_active', true)->get();
        
        return view('tasks.edit', compact('task', 'clients', 'users'));
    }

    /**
     * Update the specified task (общее обновление).
     */
    public function update(Request $request, ClientTask $task)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:open,in_progress,done',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Задача успешно обновлена.');
    }

    /**
     * Update task status for client.
     */
    public function updateStatus(Client $client, ClientTask $task, $status)
    {
        if (!in_array($status, ['open', 'in_progress', 'done'])) {
            return response()->json(['error' => 'Invalid status'], 400);
        }

        $task->update(['status' => $status]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('clients.show', $client)
            ->with('success', 'Статус задачи обновлен.');
    }

    /**
     * Remove the specified task (общее удаление).
     */
    public function destroy(ClientTask $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Задача успешно удалена.');
    }

    /**
     * Показать все задачи (общий список) с фильтрацией
     */
    public function allTasks(Request $request)
    {
        $query = ClientTask::with(['assignedUser', 'client']);

        // Фильтр по поиску
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Фильтр по статусу
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Фильтр по приоритету
        if ($request->has('priority') && $request->priority != '') {
            $query->where('priority', $request->priority);
        }

        // Фильтр по ответственному
        if ($request->has('assigned_to') && $request->assigned_to != '') {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tasks = $query->orderBy('created_at', 'desc')
                    ->paginate($request->per_page ?? 10);

        $users = User::where('is_active', true)->get();

        return view('tasks.index', compact('tasks', 'users'));
    }
}