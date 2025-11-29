<?php

namespace App\Http\Livewire\Clients\Tasks;

use Livewire\Component;
use App\Models\ClientTask;
use App\Models\Client;

class TaskForm extends Component
{
    public Client $client;
    public $taskId = null;

    public $title;
    public $description;
    public $status = 'open';
    public $priority = 'medium';
    public $assigned_to = null;
    public $due_date;

    protected $listeners = ['openTaskForm' => 'open'];

    public function mount(Client $client)
    {
        $this->client = $client;
    }

    public function open($id = null)
    {
        $this->resetValidation();
        $this->reset(['title','description','status','priority','assigned_to','due_date','taskId']);

        if ($id) {
            $task = ClientTask::findOrFail($id);
            $this->taskId = $task->id;

            $this->title        = $task->title;
            $this->description  = $task->description;
            $this->status       = $task->status;
            $this->priority     = $task->priority;
            $this->assigned_to  = $task->assigned_to;
            $this->due_date     = $task->due_date?->format('Y-m-d\TH:i');
        }

        $this->dispatch('showTaskForm');
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|min:3',
        ]);

        ClientTask::updateOrCreate(
            ['id' => $this->taskId],
            [
                'client_id' => $this->client->id,
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
                'priority' => $this->priority,
                'assigned_to' => $this->assigned_to,
                'due_date' => $this->due_date,
            ]
        );

        $this->dispatch('taskSaved');
        $this->dispatch('hideTaskForm');
    }

    public function render()
    {
        return view('livewire.clients.tasks.form');
    }
}
