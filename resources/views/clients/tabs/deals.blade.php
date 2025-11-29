<div class="deals-tab">
    <!-- Заголовок -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">💰 Сделки клиента</h4>
    </div>

    <!-- Статистика и кнопка в одной линии -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <!-- Статистика -->
        <div class="d-flex flex-wrap gap-2">
            <div class="card bg-light stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->deals->count() }}</h5>
                    <small class="text-muted">Всего</small>
                </div>
            </div>
            <div class="card bg-success text-white stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->deals->where('status', 'win')->count() }}</h5>
                    <small>Выиграно</small>
                </div>
            </div>
            <div class="card bg-warning stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->deals->where('status', 'negotiation')->count() }}</h5>
                    <small>Переговоры</small>
                </div>
            </div>
            <div class="card bg-info text-white stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->deals->where('status', 'proposal')->count() }}</h5>
                    <small>Коммерческие</small>
                </div>
            </div>
            <div class="card bg-danger text-white stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->deals->where('status', 'lost')->count() }}</h5>
                    <small>Проиграно</small>
                </div>
            </div>
        </div>
        
        <!-- Кнопка создания сделки -->
        <button class="btn btn-primary btn-lg" 
                data-bs-toggle="modal" 
                data-bs-target="#dealModal">
            <i class="bi bi-plus-circle"></i> Новая сделка
        </button>
    </div>

    <!-- Фильтры -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="filter-buttons d-flex flex-wrap gap-1">
                <button class="btn btn-outline-secondary btn-sm active" data-filter="all">
                    Все ({{ $client->deals->count() }})
                </button>
                <button class="btn btn-outline-secondary btn-sm" data-filter="lead">
                    Лиды ({{ $client->deals->where('status', 'lead')->count() }})
                </button>
                <button class="btn btn-outline-info btn-sm" data-filter="proposal">
                    Коммерческие ({{ $client->deals->where('status', 'proposal')->count() }})
                </button>
                <button class="btn btn-outline-warning btn-sm" data-filter="negotiation">
                    Переговоры ({{ $client->deals->where('status', 'negotiation')->count() }})
                </button>
                <button class="btn btn-outline-success btn-sm" data-filter="win">
                    Выиграно ({{ $client->deals->where('status', 'win')->count() }})
                </button>
                <button class="btn btn-outline-danger btn-sm" data-filter="lost">
                    Проиграно ({{ $client->deals->where('status', 'lost')->count() }})
                </button>
            </div>
        </div>
    </div>

    <!-- Таблица сделок -->
    @if($client->deals->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Название</th>
                        <th>Статус</th>
                        <th>Сумма</th>
                        <th>Ожидаемое закрытие</th>
                        <th>Создано</th>
                        <th width="140" class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($client->deals as $deal)
                    @php
                        // Преобразуем expected_close_at в Carbon объект для безопасной проверки
                        $expectedCloseAt = $deal->expected_close_at ? \Carbon\Carbon::parse($deal->expected_close_at) : null;
                        $isOverdue = $expectedCloseAt && $expectedCloseAt->isPast() && !in_array($deal->status, ['win', 'lost']);
                        $isSoon = $expectedCloseAt && $expectedCloseAt->diffInDays(now()) <= 7 && !$isOverdue;
                    @endphp
                    <tr class="deal-row" data-status="{{ $deal->status }}">
                        <td>
                            <strong>{{ $deal->title }}</strong>
                            @if($isOverdue)
                                <br><small class="text-danger">⚠️ Просрочена</small>
                            @endif
                        </td>

                        <td>
                            <div class="dropdown">
                                <span class="badge bg-{{ [
                                    'lead' => 'secondary',
                                    'proposal' => 'info', 
                                    'negotiation' => 'warning',
                                    'win' => 'success',
                                    'lost' => 'danger'
                                ][$deal->status] }} dropdown-toggle" data-bs-toggle="dropdown" style="cursor: pointer;">
                                    @if($deal->status === 'lead') 🟢 Лид
                                    @elseif($deal->status === 'proposal') 🔵 Коммерческое
                                    @elseif($deal->status === 'negotiation') 🟡 Переговоры
                                    @elseif($deal->status === 'win') ✅ Выиграно
                                    @elseif($deal->status === 'lost') ❌ Проиграно
                                    @endif
                                </span>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item change-status" href="#" data-status="lead">🟢 Лид</a></li>
                                    <li><a class="dropdown-item change-status" href="#" data-status="proposal">🔵 Коммерческое</a></li>
                                    <li><a class="dropdown-item change-status" href="#" data-status="negotiation">🟡 Переговоры</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-success change-status" href="#" data-status="win">✅ Выиграно</a></li>
                                    <li><a class="dropdown-item text-danger change-status" href="#" data-status="lost">❌ Проиграно</a></li>
                                </ul>
                            </div>
                        </td>

                        <td>
                            <strong>{{ number_format($deal->amount, 0, ',', ' ') }} ₽</strong>
                            @if($deal->status === 'win')
                                <br><small class="text-success">💰 В доходе</small>
                            @endif
                        </td>

                        <td>
                            @if($expectedCloseAt)
                                {{ $expectedCloseAt->format('d.m.Y') }}
                                @if($isOverdue)
                                    <br><small class="text-danger">🔻 Просрочено</small>
                                @elseif($isSoon)
                                    <br><small class="text-warning">⏰ Скоро</small>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>
                            <small>{{ $deal->created_at->format('d.m.Y') }}</small>
                            <br>
                            <small class="text-muted">{{ $deal->created_at->format('H:i') }}</small>
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                <button class="btn btn-outline-primary btn-sm edit-deal" 
                                        data-deal-id="{{ $deal->id }}"
                                        data-deal-title="{{ $deal->title }}"
                                        data-deal-amount="{{ $deal->amount }}"
                                        data-deal-status="{{ $deal->status }}"
                                        data-deal-expected_close_at="{{ $expectedCloseAt?->format('Y-m-d') }}"
                                        title="Редактировать"
                                        style="min-width: 36px;">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <button class="btn btn-outline-danger btn-sm delete-deal" 
                                        data-deal-id="{{ $deal->id }}"
                                        data-deal-title="{{ $deal->title }}"
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
            <i class="bi bi-graph-up" style="font-size: 3rem; color: #6c757d;"></i>
            <p class="text-muted mt-3">Сделок пока нет. Создайте первую сделку!</p>
        </div>
    @endif
</div>

<!-- Модальное окно создания сделки -->
<div class="modal fade" id="dealModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
           <form action="{{ route('clients.deals.store', $client) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">📈 Новая сделка</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Название сделки *</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required 
                               placeholder="Например: Поставка оборудования">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Сумма *</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="amount" class="form-control" 
                                   value="{{ old('amount') }}" required placeholder="0.00">
                            <span class="input-group-text">₽</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Статус *</label>
                        <select name="status" class="form-select" required>
                            <option value="lead" {{ old('status') == 'lead' ? 'selected' : '' }}>🟢 Лид</option>
                            <option value="proposal" {{ old('status') == 'proposal' ? 'selected' : '' }}>🔵 Коммерческое</option>
                            <option value="negotiation" {{ old('status') == 'negotiation' ? 'selected' : '' }}>🟡 Переговоры</option>
                            <option value="win" {{ old('status') == 'win' ? 'selected' : '' }}>✅ Выиграно</option>
                            <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>❌ Проиграно</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ожидаемая дата закрытия</label>
                        <input type="date" name="expected_close_at" class="form-control" 
                               value="{{ old('expected_close_at') }}"
                               min="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать сделку</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования сделки -->
<div class="modal fade" id="editDealModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editDealForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Редактирование сделки</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Название сделки *</label>
                        <input type="text" name="title" class="form-control" required id="editDealTitle">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Сумма *</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="amount" class="form-control" required id="editDealAmount">
                            <span class="input-group-text">₽</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Статус *</label>
                        <select name="status" class="form-select" required id="editDealStatus">
                            <option value="lead">🟢 Лид</option>
                            <option value="proposal">🔵 Коммерческое</option>
                            <option value="negotiation">🟡 Переговоры</option>
                            <option value="win">✅ Выиграно</option>
                            <option value="lost">❌ Проиграно</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ожидаемая дата закрытия</label>
                        <input type="date" name="expected_close_at" class="form-control" id="editDealExpectedCloseAt">
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
<div class="modal fade" id="deleteDealModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">❌ Удаление сделки</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить сделку "<strong id="deleteDealTitle"></strong>"?</p>
                <p class="text-muted small">Это действие нельзя отменить.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form id="deleteDealForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить сделку</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Фильтрация сделок по статусу
    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Активная кнопка
            document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Показываем/скрываем строки
            document.querySelectorAll('.deal-row').forEach(row => {
                if (filter === 'all' || row.getAttribute('data-status') === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // Быстрая смена статуса
    document.querySelectorAll('.change-status').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const newStatus = this.getAttribute('data-status');
            const dealId = this.closest('tr').querySelector('.edit-deal').getAttribute('data-deal-id');
            
            if (confirm('Изменить статус сделки?')) {
                fetch(`/clients/{{ $client->id }}/deals/${dealId}/status/${newStatus}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    }
                });
            }
        });
    });

    // Редактирование сделки
    const editModal = new bootstrap.Modal(document.getElementById('editDealModal'));
    document.querySelectorAll('.edit-deal').forEach(btn => {
        btn.addEventListener('click', function() {
            const dealId = this.getAttribute('data-deal-id');
            const form = document.getElementById('editDealForm');
            
            form.action = `/clients/{{ $client->id }}/deals/${dealId}`;
            document.getElementById('editDealTitle').value = this.getAttribute('data-deal-title');
            document.getElementById('editDealAmount').value = this.getAttribute('data-deal-amount');
            document.getElementById('editDealStatus').value = this.getAttribute('data-deal-status');
            document.getElementById('editDealExpectedCloseAt').value = this.getAttribute('data-deal-expected_close_at');
            
            editModal.show();
        });
    });

    // Удаление сделки
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteDealModal'));
    document.querySelectorAll('.delete-deal').forEach(btn => {
        btn.addEventListener('click', function() {
            const dealId = this.getAttribute('data-deal-id');
            const dealTitle = this.getAttribute('data-deal-title');
            const form = document.getElementById('deleteDealForm');
            
            form.action = `/clients/{{ $client->id }}/deals/${dealId}`;
            document.getElementById('deleteDealTitle').textContent = dealTitle;
            
            deleteModal.show();
        });
    });
});
</script>

<style>
.deals-tab {
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
    .deals-tab .d-flex.flex-wrap {
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
}
</style>