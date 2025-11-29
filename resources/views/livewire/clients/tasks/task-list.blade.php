<div>

    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex gap-2">

            <select wire:model="status" class="form-select">
                <option value="">Все статусы</option>
                <option value="open">Открыта</option>
                <option value="in_progress">В работе</option>
                <option value="done">Готово</option>
            </select>

            <select wire:model="priority" class="form-select">
                <option value="">Все приоритеты</option>
                <option value="low">Низкий</option>
                <option value="medium">Средний</option>
                <option value="high">Высокий</option>
            </select>

        </div>

        <button class="btn btn-primary" wire:click="$emit('openTaskForm')">
            + Добавить задачу
        </button>
    </div>

    {{-- таблица --}}
    <div class="card">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Статус</th>
                    <th>Приоритет</th>
                    <th>Ответственный</th>
                    <th>Срок</th>
                    <th width="80"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    <tr wire:key="task-{{ $task->id }}">
                        <td>{{ $task->title }}</td>

                        <td>
                            <select class="form-select form-select-sm"
                                    wire:change="$emit('updateStatus', {{ $task->id }}, $event.target.value)">
                                <option value="open" @selected($task->status=='open')>Открыта</option>
                                <option value="in_progress" @selected($task->status=='in_progress')>В работе</option>
                                <option value="done" @selected($task->status=='done')>Готово</option>
                            </select>
                        </td>

                        <td>
                            <span class="badge 
                                @if($task->priority=='high') bg-danger
                                @elseif($task->priority=='medium') bg-warning text-dark
                                @else bg-success @endif">
                                {{ $task->priority }}
                            </span>
                        </td>

                        <td>{{ $task->assignedUser->name ?? '—' }}</td>

                        <td>{{ $task->due_date?->format('d.m.Y H:i') ?? '—' }}</td>

                        <td>
                            <button class="btn btn-sm btn-outline-primary"
                                    wire:click="$emit('openTaskShow', {{ $task->id }})">
                                →
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-2">
            {{ $tasks->links() }}
        </div>
    </div>

</div>
