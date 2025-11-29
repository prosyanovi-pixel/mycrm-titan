<div class="interactions-tab">
    <!-- Заголовок -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">💬 Взаимодействия с клиентом</h4>
    </div>

    <!-- Статистика и кнопка в одной линии -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <!-- Статистика -->
        <div class="d-flex flex-wrap gap-2">
            <div class="card bg-light stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->interactions->count() }}</h5>
                    <small class="text-muted">Всего</small>
                </div>
            </div>
            <div class="card bg-info text-white stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->interactions->where('type', 'meeting')->count() }}</h5>
                    <small>Встречи</small>
                </div>
            </div>
            <div class="card bg-success text-white stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->interactions->where('type', 'call')->count() }}</h5>
                    <small>Звонки</small>
                </div>
            </div>
            <div class="card bg-warning stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->interactions->where('type', 'email')->count() }}</h5>
                    <small>Письма</small>
                </div>
            </div>
            <div class="card bg-secondary text-white stat-card">
                <div class="card-body text-center p-2">
                    @php
                        $lastInteraction = $client->interactions->sortByDesc('created_at')->first();
                        $daysAgo = $lastInteraction ? $lastInteraction->created_at->diffInDays(now()) : '—';
                    @endphp
                    <h5 class="mb-0">{{ $daysAgo }}</h5>
                    <small>Дней назад</small>
                </div>
            </div>
        </div>
        
        <!-- Кнопка создания взаимодействия -->
        <button class="btn btn-primary btn-lg" 
                data-bs-toggle="modal" 
                data-bs-target="#interactionModal">
            <i class="bi bi-plus-circle"></i> Новое взаимодействие
        </button>
    </div>

    <!-- Фильтры -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="filter-buttons d-flex flex-wrap gap-1">
                <button class="btn btn-outline-secondary btn-sm active" data-filter="all">
                    Все ({{ $client->interactions->count() }})
                </button>
                <button class="btn btn-outline-info btn-sm" data-filter="meeting">
                    📅 Встречи ({{ $client->interactions->where('type', 'meeting')->count() }})
                </button>
                <button class="btn btn-outline-success btn-sm" data-filter="call">
                    📞 Звонки ({{ $client->interactions->where('type', 'call')->count() }})
                </button>
                <button class="btn btn-outline-warning btn-sm" data-filter="email">
                    ✉️ Письма ({{ $client->interactions->where('type', 'email')->count() }})
                </button>
                <button class="btn btn-outline-primary btn-sm" data-filter="note">
                    📝 Заметки ({{ $client->interactions->where('type', 'note')->count() }})
                </button>
            </div>
        </div>
    </div>

    <!-- Лента взаимодействий -->
    @if($client->interactions->count() > 0)
        <div class="timeline">
            @foreach($client->interactions->sortByDesc('created_at') as $interaction)
            <div class="timeline-item interaction-row" data-type="{{ $interaction->type }}">
                <div class="timeline-marker bg-{{ [
                    'meeting' => 'info',
                    'call' => 'success', 
                    'email' => 'warning',
                    'note' => 'primary'
                ][$interaction->type] }}"></div>
                
                <div class="timeline-content">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">
                                        @if($interaction->type === 'meeting') 📅 Встреча
                                        @elseif($interaction->type === 'call') 📞 Звонок
                                        @elseif($interaction->type === 'email') ✉️ Письмо
                                        @elseif($interaction->type === 'note') 📝 Заметка
                                        @endif
                                        
                                        @if($interaction->title)
                                            : {{ $interaction->title }}
                                        @endif
                                    </h6>
                                    <small class="text-muted">
                                        {{ $interaction->created_at->format('d.m.Y H:i') }}
                                        @if($interaction->user)
                                            • {{ $interaction->user->name }}
                                        @endif
                                    </small>
                                </div>
                                <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                    <button class="btn btn-outline-primary btn-sm edit-interaction"
                                            data-interaction-id="{{ $interaction->id }}"
                                            data-interaction-type="{{ $interaction->type }}"
                                            data-interaction-title="{{ $interaction->title }}"
                                            data-interaction-description="{{ $interaction->description }}"
                                            data-interaction-outcome="{{ $interaction->outcome }}"
                                            title="Редактировать"
                                            style="min-width: 36px;">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button class="btn btn-outline-danger btn-sm delete-interaction" 
                                            data-interaction-id="{{ $interaction->id }}"
                                            data-interaction-title="{{ $interaction->title }}"
                                            title="Удалить"
                                            style="min-width: 36px;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>

                            @if($interaction->description)
                                <p class="mb-0">{{ $interaction->description }}</p>
                            @endif

                            @if($interaction->outcome)
                                <div class="mt-2 p-2 bg-light rounded">
                                    <strong>Результат:</strong> {{ $interaction->outcome }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-chat-dots" style="font-size: 3rem; color: #6c757d;"></i>
            <p class="text-muted mt-3">Взаимодействий пока нет. Добавьте первое взаимодействие!</p>
        </div>
    @endif
</div>

<!-- Модальное окно создания взаимодействия -->
<div class="modal fade" id="interactionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('clients.interactions.store', $client) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">💬 Новое взаимодействие</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Тип взаимодействия *</label>
                                <select name="type" class="form-select" required>
                                    <option value="meeting">📅 Встреча</option>
                                    <option value="call">📞 Звонок</option>
                                    <option value="email">✉️ Письмо</option>
                                    <option value="note">📝 Заметка</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Заголовок</label>
                                <input type="text" name="title" class="form-control" placeholder="Краткое описание">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Описание *</label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Подробное описание взаимодействия..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Результат</label>
                        <textarea name="outcome" class="form-control" rows="2" placeholder="Каков был результат?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Добавить взаимодействие</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования взаимодействия -->
<div class="modal fade" id="editInteractionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editInteractionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Редактирование взаимодействия</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Тип взаимодействия *</label>
                                <select name="type" class="form-select" required id="editInteractionType">
                                    <option value="meeting">📅 Встреча</option>
                                    <option value="call">📞 Звонок</option>
                                    <option value="email">✉️ Письмо</option>
                                    <option value="note">📝 Заметка</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Заголовок</label>
                                <input type="text" name="title" class="form-control" id="editInteractionTitle">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Описание *</label>
                        <textarea name="description" class="form-control" rows="4" required id="editInteractionDescription"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Результат</label>
                        <textarea name="outcome" class="form-control" rows="2" id="editInteractionOutcome"></textarea>
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
<div class="modal fade" id="deleteInteractionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">❌ Удаление взаимодействия</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить взаимодействие "<strong id="deleteInteractionTitle"></strong>"?</p>
                <p class="text-muted small">Это действие нельзя отменить.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form id="deleteInteractionForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Фильтрация взаимодействий
    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Активная кнопка
            document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Показываем/скрываем элементы
            document.querySelectorAll('.interaction-row').forEach(row => {
                if (filter === 'all' || row.getAttribute('data-type') === filter) {
                    row.style.display = 'flex';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // Редактирование взаимодействия
    const editInteractionModal = new bootstrap.Modal(document.getElementById('editInteractionModal'));
    document.querySelectorAll('.edit-interaction').forEach(btn => {
        btn.addEventListener('click', function() {
            const interactionId = this.getAttribute('data-interaction-id');
            const form = document.getElementById('editInteractionForm');
            
            form.action = `/clients/{{ $client->id }}/interactions/${interactionId}`;
            document.getElementById('editInteractionType').value = this.getAttribute('data-interaction-type');
            document.getElementById('editInteractionTitle').value = this.getAttribute('data-interaction-title') || '';
            document.getElementById('editInteractionDescription').value = this.getAttribute('data-interaction-description') || '';
            document.getElementById('editInteractionOutcome').value = this.getAttribute('data-interaction-outcome') || '';
            
            editInteractionModal.show();
        });
    });

    // Удаление взаимодействия
    const deleteInteractionModal = new bootstrap.Modal(document.getElementById('deleteInteractionModal'));
    document.querySelectorAll('.delete-interaction').forEach(btn => {
        btn.addEventListener('click', function() {
            const interactionId = this.getAttribute('data-interaction-id');
            const interactionTitle = this.getAttribute('data-interaction-title') || 'взаимодействие';
            const form = document.getElementById('deleteInteractionForm');
            
            form.action = `/clients/{{ $client->id }}/interactions/${interactionId}`;
            document.getElementById('deleteInteractionTitle').textContent = interactionTitle;
            
            deleteInteractionModal.show();
        });
    });
});
</script>

<style>
.interactions-tab {
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

/* Фильтры */
.filter-buttons .btn {
    margin-bottom: 0.25rem;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.filter-buttons .btn.active {
    font-weight: 600;
    background-color: #6c757d;
    color: white;
}

/* Кнопки действий */
.d-flex.gap-1 {
    gap: 0.25rem !important;
}

.btn-sm {
    min-width: 36px;
    padding: 0.25rem 0.5rem;
}

/* Timeline стили */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    display: flex;
    margin-bottom: 20px;
    position: relative;
}

.timeline-marker {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    position: absolute;
    left: -30px;
    top: 15px;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #e9ecef;
    z-index: 2;
}

.timeline-content {
    flex: 1;
}

.interaction-row:hover .card {
    transform: translateX(5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

/* Адаптивность */
@media (max-width: 768px) {
    .interactions-tab .d-flex.flex-wrap {
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
    
    .timeline {
        padding-left: 20px;
    }
    
    .timeline-marker {
        left: -20px;
        width: 10px;
        height: 10px;
    }
}
</style>