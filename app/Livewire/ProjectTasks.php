<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ClientTask;
use App\Models\User;

class ProjectTasks extends Component
{
    use WithPagination;

    public $status = '';
    public $priority = '';

    public function updating($field)
    {
        if (in_array($field, ['status', 'priority'])) {
            $this->resetPage();
        }
    }

    public function updateStatus($taskId, $newStatus)
    {
        $task = ClientTask::find($taskId);
        if ($task && in_array($newStatus, ['open', 'in_progress', 'done'])) {
            $task->update(['status' => $newStatus]);
            session()->flash('message', 'Статус обновлен!');
        }
    }

    public function createTask()
    {
        // Здесь будет логика создания задачи
        session()->flash('message', 'Создание новой задачи');
    }

    public function showTask($taskId)
    {
        // Здесь будет логика просмотра задачи
        session()->flash('message', "Просмотр задачи #{$taskId}");
    }

    public function render()
    {
        $query = ClientTask::with(['assignedUser']);

        // Фильтрация по статусу
        if ($this->status) {
            $query->where('status', $this->status);
        }

        // Фильтрация по приоритету
        if ($this->priority) {
            $query->where('priority', $this->priority);
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.project-tasks', [
            'tasks' => $tasks
        ]);
    }
}