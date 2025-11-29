@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            {{-- Фильтры --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3">
                    <form method="GET" class="row g-2 align-items-center">
                        
                        {{-- Поиск --}}
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-0 bg-light"
                                       placeholder="Поиск задач..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        {{-- Статус --}}
                        <div class="col-md-2">
                            <select name="status" class="form-select form-select-sm border-0 bg-light">
                                <option value="">Все статусы</option>
                                <option value="open" @selected(request('status')=='open')>Открыта</option>
                                <option value="in_progress" @selected(request('status')=='in_progress')>В работе</option>
                                <option value="done" @selected(request('status')=='done')>Выполнена</option>
                            </select>
                        </div>

                        {{-- Приоритет --}}
                        <div class="col-md-2">
                            <select name="priority" class="form-select form-select-sm border-0 bg-light">
                                <option value="">Все приоритеты</option>
                                <option value="low" @selected(request('priority')=='low')>Низкий</option>
                                <option value="medium" @selected(request('priority')=='medium')>Средний</option>
                                <option value="high" @selected(request('priority')=='high')>Высокий</option>
                            </select>
                        </div>

                        {{-- Ответственный --}}
                        <div class="col-md-2">
                            <select name="assigned_to" class="form-select form-select-sm border-0 bg-light">
                                <option value="">Все ответственные</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(request('assigned_to')==$user->id)>
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Кол-во строк --}}
                        <div class="col-md-1">
                            <select name="per_page" class="form-select form-select-sm border-0 bg-light" 
                                    onchange="this.form.submit()">
                                <option value="10" @selected(request('per_page', 10)==10)>10</option>
                                <option value="25" @selected(request('per_page', 10)==25)>25</option>
                                <option value="50" @selected(request('per_page', 10)==50)>50</option>
                            </select>
                        </div>

                        {{-- Кнопки действий --}}
                        <div class="col-md-2">
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                        onclick="this.form.reset(); this.form.submit()">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                                
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-funnel"></i> Применить
                                </button>
                                
                                <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-circle"></i> Добавить
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Основной контент с таблицей и статистикой --}}
            <div class="row">
                {{-- Таблица задач (занимает 9 колонок) --}}
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-dark">Задача</th>
                                            <th class="text-dark">Клиент</th>
                                            <th class="text-dark">Статус</th>
                                            <th class="text-dark">Приоритет</th>
                                            <th class="text-dark">Ответственный</th>
                                            <th class="text-dark">Срок</th>
                                            <th width="120" class="text-dark">Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tasks as $task)
                                            <tr>
                                                <td>
                                                    <strong>{{ $task->title }}</strong>
                                                    @if($task->description)
                                                        <br><small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($task->client)
                                                        <a href="{{ route('clients.show', $task->client_id) }}" class="text-decoration-none">
                                                            {{ $task->client->company_name ?? $task->client->getFullName() }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'open' => 'secondary',
                                                            'in_progress' => 'primary', 
                                                            'done' => 'success'
                                                        ];
                                                        $statusLabels = [
                                                            'open' => 'Открыта',
                                                            'in_progress' => 'В работе',
                                                            'done' => 'Выполнена'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}">
                                                        {{ $statusLabels[$task->status] ?? $task->status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $priorityColors = [
                                                            'low' => 'success',
                                                            'medium' => 'warning',
                                                            'high' => 'danger'
                                                        ];
                                                        $priorityLabels = [
                                                            'low' => 'Низкий',
                                                            'medium' => 'Средний',
                                                            'high' => 'Высокий'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $priorityColors[$task->priority] ?? 'secondary' }}">
                                                        {{ $priorityLabels[$task->priority] ?? $task->priority }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($task->assignedUser)
                                                        {{ $task->assignedUser->first_name }} {{ $task->assignedUser->last_name }}
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($task->due_date)
                                                        @php
                                                            $dueDate = \Carbon\Carbon::parse($task->due_date);
                                                        @endphp
                                                        {{ $dueDate->format('d.m.Y') }}
                                                        @if($dueDate->isPast() && $task->status != 'done')
                                                            <span class="badge bg-danger">Просрочена</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <a href="{{ route('tasks.edit', $task->id) }}" 
                                                        class="btn btn-sm btn-outline-primary" 
                                                        title="Редактировать"
                                                        data-bs-toggle="tooltip">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        
                                                        <a href="{{ route('clients.show', $task->client_id) }}" 
                                                        class="btn btn-sm btn-outline-info" 
                                                        title="Клиент"
                                                        data-bs-toggle="tooltip">
                                                            <i class="bi bi-person"></i>
                                                        </a>
                                                        
                                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" 
                                                            class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-outline-danger" 
                                                                    title="Удалить"
                                                                    data-bs-toggle="tooltip"
                                                                    onclick="return confirm('Удалить задачу?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                                    Задачи не найдены
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Пагинация --}}
                            @if($tasks->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <small class="text-muted">
                                        Показано с {{ $tasks->firstItem() }} по {{ $tasks->lastItem() }} из {{ $tasks->total() }} задач
                                    </small>
                                </div>
                                <div>
                                    {{ $tasks->links() }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Статистика справа (занимает 3 колонки) --}}
                <div class="col-md-3">
                    <div class="sticky-top" style="top: 20px;">
                        {{-- Общая статистика --}}
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="mb-0"><i class="bi bi-check-circle me-2"></i>Статистика задач</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small">Всего задач</span>
                                        <span class="badge bg-primary rounded-pill">{{ $tasks->total() }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small text-success">Выполнено</span>
                                        <span class="badge bg-success rounded-pill">{{ $tasks->where('status', 'done')->count() }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small text-primary">В работе</span>
                                        <span class="badge bg-primary rounded-pill">{{ $tasks->where('status', 'in_progress')->count() }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small text-secondary">Открыто</span>
                                        <span class="badge bg-secondary rounded-pill">{{ $tasks->where('status', 'open')->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Статистика по приоритетам --}}
                        <div class="card mb-3">
                            <div class="card-header bg-warning text-dark py-2">
                                <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>По приоритетам</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small text-danger">Высокий</span>
                                        <span class="badge bg-danger rounded-pill">{{ $tasks->where('priority', 'high')->count() }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small text-warning">Средний</span>
                                        <span class="badge bg-warning rounded-pill">{{ $tasks->where('priority', 'medium')->count() }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small text-success">Низкий</span>
                                        <span class="badge bg-success rounded-pill">{{ $tasks->where('priority', 'low')->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Просроченные задачи --}}
                        <div class="card">
                            <div class="card-header bg-danger text-white py-2">
                                <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Срочные задачи</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small">Просрочено</span>
                                        <span class="badge bg-danger rounded-pill">
                                            @php
                                                $overdueCount = 0;
                                                foreach($tasks as $task) {
                                                    if ($task->due_date && $task->due_date->isPast() && $task->status != 'done') {
                                                        $overdueCount++;
                                                    }
                                                }
                                            @endphp
                                            {{ $overdueCount }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small text-warning">На сегодня</span>
                                        <span class="badge bg-warning rounded-pill">
                                            @php
                                                $todayCount = 0;
                                                foreach($tasks as $task) {
                                                    if ($task->due_date && $task->due_date->isToday() && $task->status != 'done') {
                                                        $todayCount++;
                                                    }
                                                }
                                            @endphp
                                            {{ $todayCount }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small text-info">На неделю</span>
                                        <span class="badge bg-info rounded-pill">
                                            @php
                                                $weekCount = 0;
                                                $nextWeek = \Carbon\Carbon::now()->addWeek();
                                                foreach($tasks as $task) {
                                                    if ($task->due_date && $task->due_date->isBetween(\Carbon\Carbon::now(), $nextWeek) && $task->status != 'done') {
                                                        $weekCount++;
                                                    }
                                                }
                                            @endphp
                                            {{ $weekCount }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Скрипт для инициализации тултипов --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<style>
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
}

.d-flex.gap-1 > * {
    margin: 0 1px;
}

.btn-outline-primary:hover,
.btn-outline-info:hover,
.btn-outline-danger:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.sticky-top {
    position: sticky;
    z-index: 10;
}

.list-group-item {
    border: none;
    padding: 0.5rem 1rem;
}

.badge.rounded-pill {
    font-size: 0.7em;
}
</style>
@endsection