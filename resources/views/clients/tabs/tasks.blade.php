<div class="tasks-tab">
    <!-- Заголовок -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">✅ Задачи клиента</h4>
    </div>

    <!-- Статистика и кнопка в одной линии -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <!-- Статистика -->
        <div class="d-flex flex-wrap gap-2">
            <div class="card bg-light stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->tasks->count() }}</h5>
                    <small class="text-muted">Всего</small>
                </div>
            </div>
            <div class="card bg-warning stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->tasks->where('status', 'open')->count() }}</h5>
                    <small>Открытые</small>
                </div>
            </div>
            <div class="card bg-info text-white stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->tasks->where('status', 'in_progress')->count() }}</h5>
                    <small>В работе</small>
                </div>
            </div>
            <div class="card bg-success text-white stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->tasks->where('status', 'done')->count() }}</h5>
                    <small>Выполнено</small>
                </div>
            </div>
            <div class="card bg-danger text-white stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->tasks->where('priority', 'high')->count() }}</h5>
                    <small>Высокий приоритет</small>
                </div>
            </div>
        </div>
        
        <!-- Кнопка создания задачи -->
        <button class="btn btn-primary btn-lg" 
                data-bs-toggle="modal" 
                data-bs-target="#taskModal">
            <i class="bi bi-plus-circle"></i> Новая задача
        </button>
    </div>

    <!-- Фильтры -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row g-2">
                <div class="col-md-3">
                    <select class="form-select form-select-sm" id="statusFilter">
                        <option value="all">Все статусы</option>
                        <option value="open">Открытые</option>
                        <option value="in_progress">В работе</option>
                        <option value="done">Выполненные</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" id="priorityFilter">
                        <option value="all">Все приоритеты</option>
                        <option value="high">Высокий</option>
                        <option value="medium">Средний</option>
                        <option value="low">Низкий</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" id="assignedFilter">
                        <option value="all">Все ответственные</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-secondary btn-sm w-100" onclick="resetFilters()">Сбросить</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Таблица задач -->
    @if($client->tasks->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Задача</th>
                        <th>Статус</th>
                        <th>Приоритет</th>
                        <th>Ответственный</th>
                        <th>Срок</th>
                        <th>Создана</th>
                        <th width="140" class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($client->tasks as $task)
                    <tr class="task-row" 
                        data-status="{{ $task->status }}" 
                        data-priority="{{ $task->priority }}"
                        data-assigned="{{ $task->user_id }}">
                        <td>
                            <div class="fw-semibold">{{ $task->title }}</div>
                            @if($task->description)
                                <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                            @endif
                            @if($task->due_date && $task->due_date->isPast() && $task->status !== 'done')
                                <br><small class="text-danger">⚠️ Просрочена</small>
                            @endif
                        </td>

                        <td>
                            <div class="dropdown">
                                <span class="badge bg-{{ [
                                    'open' => 'warning',
                                    'in_progress' => 'info', 
                                    'done' => 'success'
                                ][$task->status] }} dropdown-toggle" data-bs-toggle="dropdown" style="cursor: pointer;">
                                    @if($task->status === 'open') ⏳ Открыта
                                    @elseif($task->status === 'in_progress') 🔄 В работе
                                    @elseif($task->status === 'done') ✅ Выполнена
                                    @endif
                                </span>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item change-task-status" href="#" data-status="open">⏳ Открыта</a></li>
                                    <li><a class="dropdown-item change-task-status" href="#" data-status="in_progress">🔄 В работе</a></li>
                                    <li><a class="dropdown-item change-task-status" href="#" data-status="done">✅ Выполнена</a></li>
                                </ul>
                            </div>
                        </td>

                        <td>
                            <span class="badge bg-{{ [
                                'low' => 'success',
                                'medium' => 'warning', 
                                'high' => 'danger'
                            ][$task->priority] }}">
                                @if($task->priority === 'low') 📉 Низкий
                                @elseif($task->priority === 'medium') 📊 Средний
                                @elseif($task->priority === 'high') 📈 Высокий
                                @endif
                            </span>
                        </td>

                        <td>
                            @if($task->assignedUser)
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                         style="width: 24px; height: 24px; font-size: 12px;">
                                        {{ substr($task->assignedUser->name, 0, 1) }}
                                    </div>
                                    {{ $task->assignedUser->name }}
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>
                            @if($task->due_date)
                                <small>{{ $task->due_date->format('d.m.Y') }}</small>
                                @if($task->due_date->isPast() && $task->status !== 'done')
                                    <br><small class="text-danger">🔻 Просрочено</small>
                                @elseif($task->due_date->diffInDays(now()) <= 3)
                                    <br><small class="text-warning">⏰ Скоро</small>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>
                            <small>{{ $task->created_at->format('d.m.Y') }}</small>
                            <br>
                            <small class="text-muted">{{ $task->created_at->format('H:i') }}</small>
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                <button class="btn btn-outline-primary btn-sm edit-task" 
                                        data-task-id="{{ $task->id }}"
                                        data-task-title="{{ $task->title }}"
                                        data-task-description="{{ $task->description }}"
                                        data-task-status="{{ $task->status }}"
                                        data-task-priority="{{ $task->priority }}"
                                        data-task-due_date="{{ $task->due_date?->format('Y-m-d\TH:i') }}"
                                        data-task-assigned_to="{{ $task->user_id }}"
                                        title="Редактировать"
                                        style="min-width: 36px;">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- Исправленная форма удаления -->
                                <button class="btn btn-outline-danger btn-sm delete-task" 
                                        data-task-id="{{ $task->id }}"
                                        data-task-title="{{ $task->title }}"
                                        title="Удалить"
                                        style="min-width: 36px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-check-circle" style="font-size: 3rem; color: #6c757d;"></i>
            <p class="text-muted mt-3">Задач пока нет. Создайте первую задачу!</p>
        </div>
    @endif
</div>

<!-- Модальное окно создания задачи -->
<div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('clients.tasks.store', $client) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">✅ Новая задача</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Название задачи *</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Приоритет *</label>
                                <select name="priority" class="form-select" required>
                                    <option value="low">📉 Низкий</option>
                                    <option value="medium" selected>📊 Средний</option>
                                    <option value="high">📈 Высокий</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Описание задачи...">{{ old('description') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Статус *</label>
                                <select name="status" class="form-select" required>
                                    <option value="open" selected>⏳ Открыта</option>
                                    <option value="in_progress">🔄 В работе</option>
                                    <option value="done">✅ Выполнена</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ответственный</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">Не назначен</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Крайний срок</label>
                        <input type="datetime-local" name="due_date" class="form-control" value="{{ old('due_date') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать задачу</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования задачи -->
<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editTaskForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Редактирование задачи</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Название задачи *</label>
                                <input type="text" name="title" class="form-control" required id="editTaskTitle">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Приоритет *</label>
                                <select name="priority" class="form-select" required id="editTaskPriority">
                                    <option value="low">📉 Низкий</option>
                                    <option value="medium">📊 Средний</option>
                                    <option value="high">📈 Высокий</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control" rows="3" id="editTaskDescription"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Статус *</label>
                                <select name="status" class="form-select" required id="editTaskStatus">
                                    <option value="open">⏳ Открыта</option>
                                    <option value="in_progress">🔄 В работе</option>
                                    <option value="done">✅ Выполнена</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ответственный</label>
                                <select name="assigned_to" class="form-select" id="editTaskAssignedTo">
                                    <option value="">Не назначен</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Крайний срок</label>
                        <input type="datetime-local" name="due_date" class="form-control" id="editTaskDueDate">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div class="modal fade" id="deleteTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">❌ Удаление задачи</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить задачу "<strong id="deleteTaskTitle"></strong>"?</p>
                <p class="text-muted small">Это действие нельзя отменить.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form id="deleteTaskForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить задачу</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Фильтрация задач
    function applyFilters() {
        const statusFilter = document.getElementById('statusFilter').value;
        const priorityFilter = document.getElementById('priorityFilter').value;
        const assignedFilter = document.getElementById('assignedFilter').value;

        document.querySelectorAll('.task-row').forEach(row => {
            const showStatus = statusFilter === 'all' || row.getAttribute('data-status') === statusFilter;
            const showPriority = priorityFilter === 'all' || row.getAttribute('data-priority') === priorityFilter;
            const showAssigned = assignedFilter === 'all' || row.getAttribute('data-assigned') === assignedFilter;

            row.style.display = (showStatus && showPriority && showAssigned) ? '' : 'none';
        });
    }

    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    document.getElementById('priorityFilter').addEventListener('change', applyFilters);
    document.getElementById('assignedFilter').addEventListener('change', applyFilters);

    window.resetFilters = function() {
        document.getElementById('statusFilter').value = 'all';
        document.getElementById('priorityFilter').value = 'all';
        document.getElementById('assignedFilter').value = 'all';
        applyFilters();
    };

    // Быстрая смена статуса задачи
    document.querySelectorAll('.change-task-status').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const newStatus = this.getAttribute('data-status');
            const taskId = this.closest('tr').querySelector('.edit-task').getAttribute('data-task-id');
            
            if (confirm('Изменить статус задачи?')) {
                fetch(`/clients/{{ $client->id }}/tasks/${taskId}/status/${newStatus}`, {
                    method: 'PATCH',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Ошибка при изменении статуса');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при изменении статуса');
                });
            }
        });
    });

    // Редактирование задачи
    const editTaskModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
    document.querySelectorAll('.edit-task').forEach(btn => {
        btn.addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            const form = document.getElementById('editTaskForm');
            
            // Исправленный маршрут для редактирования
            form.action = `/clients/{{ $client->id }}/tasks/${taskId}`;
            document.getElementById('editTaskTitle').value = this.getAttribute('data-task-title');
            document.getElementById('editTaskDescription').value = this.getAttribute('data-task-description') || '';
            document.getElementById('editTaskStatus').value = this.getAttribute('data-task-status');
            document.getElementById('editTaskPriority').value = this.getAttribute('data-task-priority');
            document.getElementById('editTaskDueDate').value = this.getAttribute('data-task-due_date') || '';
            document.getElementById('editTaskAssignedTo').value = this.getAttribute('data-task-assigned_to') || '';
            
            editTaskModal.show();
        });
    });

    // Удаление задачи
    const deleteTaskModal = new bootstrap.Modal(document.getElementById('deleteTaskModal'));
    document.querySelectorAll('.delete-task').forEach(btn => {
        btn.addEventListener('click', function() {
            const taskId = this.getAttribute('data-task-id');
            const taskTitle = this.getAttribute('data-task-title');
            const form = document.getElementById('deleteTaskForm');
            
            // Устанавливаем правильный маршрут для удаления
            form.action = `/clients/{{ $client->id }}/tasks/${taskId}`;
            document.getElementById('deleteTaskTitle').textContent = taskTitle;
            
            deleteTaskModal.show();
        });
    });
});
</script>

<style>
.tasks-tab {
    padding: 20px 0;
}

/* Статистические карточки */
.stat-card {
    min-width: 80px;
    height: 60px;
    transition: all 0.3s ease;
}

.stat-card .card-body {
    padding: 0.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.stat-card h5 {
    font-size: 1.1rem;
    margin-bottom: 0.2rem;
}

.stat-card small {
    font-size: 0.7rem;
    line-height: 1;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Кнопка создания */
.btn.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    white-space: nowrap;
}

/* Кнопки действий в таблице */
.d-flex.gap-1 {
    gap: 0.25rem !important;
}

.btn-sm {
    min-width: 36px;
    padding: 0.25rem 0.5rem;
}

/* Адаптивность */
@media (max-width: 768px) {
    .tasks-tab .d-flex.flex-wrap {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem !important;
    }
    
    .stat-card {
        min-width: 70px;
        height: 55px;
    }
    
    .stat-card h5 {
        font-size: 1rem;
    }
    
    .btn.btn-lg {
        width: 100%;
        justify-content: center;
    }
    
    .tasks-tab .row.g-2 .col-md-3 {
        margin-bottom: 0.5rem;
    }
}

.task-row:hover {
    background-color: rgba(0,0,0,.025);
}
.avatar-sm {
    font-weight: 600;
}
</style>