<div>
    {{-- Сообщения --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex gap-2">
            {{-- Фильтр по статусу --}}
            <select wire:model="status" class="form-select">
                <option value="">Все статусы</option>
                <option value="open">Открыта</option>
                <option value="in_progress">В работе</option>
                <option value="done">Готово</option>
            </select>

            {{-- Фильтр по приоритету --}}
            <select wire:model="priority" class="form-select">
                <option value="">Все приоритеты</option>
                <option value="low">Низкий</option>
                <option value="medium">Средний</option>
                <option value="high">Высокий</option>
            </select>
        </div>

        {{-- Кнопка добавления --}}
        <button class="btn btn-primary" wire:click="createTask">
            + Добавить задачу
        </button>
    </div>

    {{-- Таблица задач --}}
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
                @forelse($tasks as $task)
                    <tr wire:key="task-{{ $task->id }}">
                        <td>{{ $task->title }}</td>

                        {{-- Статус с возможностью изменения --}}
                        <td>
                            <select class="form-select form-select-sm"
                                    wire:change="updateStatus({{ $task->id }}, $event.target.value)">
                                <option value="open" @selected($task->status == 'open')>Открыта</option>
                                <option value="in_progress" @selected($task->status == 'in_progress')>В работе</option>
                                <option value="done" @selected($task->status == 'done')>Готово</option>
                            </select>
                        </td>

                        {{-- Приоритет --}}
                        <td>
                            @php
                                $priorityClasses = [
                                    'high' => 'bg-danger',
                                    'medium' => 'bg-warning text-dark', 
                                    'low' => 'bg-success'
                                ];
                                $priorityLabels = [
                                    'high' => 'Высокий',
                                    'medium' => 'Средний',
                                    'low' => 'Низкий'
                                ];
                            @endphp
                            <span class="badge {{ $priorityClasses[$task->priority] ?? 'bg-secondary' }}">
                                {{ $priorityLabels[$task->priority] ?? $task->priority }}
                            </span>
                        </td>

                        {{-- Ответственный --}}
                        <td>
                            @if($task->assignedUser)
                                {{ $task->assignedUser->first_name }} {{ $task->assignedUser->last_name }}
                            @else
                                —
                            @endif
                        </td>

                        {{-- Срок --}}
                        <td>
                            @if($task->due_date)
                                {{ \Carbon\Carbon::parse($task->due_date)->format('d.m.Y') }}
                            @else
                                —
                            @endif
                        </td>

                        {{-- Кнопка просмотра --}}
                        <td>
                            <button class="btn btn-sm btn-outline-primary"
                                    wire:click="showTask({{ $task->id }})">
                                →
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            Задачи не найдены
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Пагинация --}}
        <div class="p-2">
            {{ $tasks->links() }}
        </div>
    </div>
</div>