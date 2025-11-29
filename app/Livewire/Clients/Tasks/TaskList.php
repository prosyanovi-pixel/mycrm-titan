<?php
namespace App\Http\Livewire\Clients\Tasks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;
use App\Models\ClientTask;

class TaskList extends Component
{
    use WithPagination;

    public Client $client;
    public $status = '';
    public $priority = '';

    protected $listeners = [
        'taskSaved' => '$refresh',
        'taskUpdated' => '$refresh',
    ];

    public function mount(Client $client)
    {
        $this->client = $client;
    }

    public function updating($field)
    {
        if (in_array($field, ['status', 'priority'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = ClientTask::where('client_id', $this->client->id)
            ->with('assignedUser');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->priority) {
            $query->where('priority', $this->priority);
        }

        return view('livewire.clients.tasks.list', [
            'tasks' => $query->paginate(10),
        ]);
    }
}
