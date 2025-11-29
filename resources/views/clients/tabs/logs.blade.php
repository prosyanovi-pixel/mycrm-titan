<div class="history-tab">
    <!-- Заголовок -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">📋 История изменений</h4>
    </div>

    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-md-2 col-6 mb-2">
            <div class="card bg-light stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $logs->total() }}</h5>
                    <small class="text-muted">Всего записей</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-2">
            <div class="card bg-info text-white stat-card">
                <div class="card-body text-center p-2">
                    @php
                        $uniqueUsers = $client->logs()->distinct('user_id')->count('user_id');
                    @endphp
                    <h5 class="mb-0">{{ $uniqueUsers }}</h5>
                    <small>Пользователей</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-2">
            <div class="card bg-success text-white stat-card">
                <div class="card-body text-center p-2">
                    @php
                        $todayLogs = $client->logs()->whereDate('created_at', today())->count();
                    @endphp
                    <h5 class="mb-0">{{ $todayLogs }}</h5>
                    <small>Сегодня</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-2">
            <div class="card bg-warning stat-card">
                <div class="card-body text-center p-2">
                    @php
                        $weekLogs = $client->logs()->where('created_at', '>=', now()->subWeek())->count();
                    @endphp
                    <h5 class="mb-0">{{ $weekLogs }}</h5>
                    <small>За неделю</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-2">
            <div class="card bg-primary text-white stat-card">
                <div class="card-body text-center p-2">
                    @php
                        $lastLog = $client->logs()->latest()->first();
                        $lastActivity = $lastLog ? $lastLog->created_at->diffForHumans() : '—';
                    @endphp
                    <h6 class="mb-0" style="font-size: 0.9rem;">{{ $lastActivity }}</h6>
                    <small>Последняя активность</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Фильтры и поиск -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm" id="searchLogs" 
                           placeholder="Поиск по действию...">
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" id="userFilter">
                        <option value="all">Все пользователи</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm" id="dateFilter">
                        <option value="all">За все время</option>
                        <option value="today">Сегодня</option>
                        <option value="week">За неделю</option>
                        <option value="month">За месяц</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary btn-sm w-100" onclick="resetLogFilters()">
                        <i class="bi bi-arrow-clockwise"></i> Сбросить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Информация о пагинации -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-muted small">
            Показано {{ $logs->firstItem() ?? 0 }}-{{ $logs->lastItem() ?? 0 }} из {{ $logs->total() }} записей
        </div>
        <div class="d-flex align-items-center gap-2">
            <label class="form-label small mb-0">На странице:</label>
            <select class="form-select form-select-sm" style="width: auto;" onchange="changePerPage(this.value)">
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
    </div>

    <!-- Лента истории -->
    @if($logs->count() > 0)
        <div class="timeline">
            @foreach($logs as $log)
            <div class="timeline-item log-row" 
                 data-user="{{ $log->user_id }}" 
                 data-action="{{ strtolower($log->action) }}"
                 data-date="{{ $log->created_at->format('Y-m-d') }}">
                <div class="timeline-marker bg-{{ $log->user ? 'primary' : 'secondary' }}"></div>
                
                <div class="timeline-content">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <span class="badge bg-{{ $log->user ? 'primary' : 'secondary' }} me-2">
                                            {{ $log->user ? substr($log->user->name, 0, 1) : 'S' }}
                                        </span>
                                        <strong>{{ $log->action }}</strong>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $log->created_at->format('d.m.Y H:i') }}
                                        @if($log->user)
                                            • <i class="bi bi-person"></i> {{ $log->user->name }}
                                        @else
                                            • <i class="bi bi-gear"></i> Система
                                        @endif
                                    </small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                            type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <button class="dropdown-item view-log-details" 
                                                    data-log-id="{{ $log->id }}"
                                                    data-log-action="{{ $log->action }}"
                                                    data-log-user="{{ $log->user?->name ?? 'Система' }}"
                                                    data-log-date="{{ $log->created_at->format('d.m.Y H:i') }}"
                                                    data-log-old="{{ $log->old_value }}"
                                                    data-log-new="{{ $log->new_value }}">
                                                <i class="bi bi-eye"></i> Подробности
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Краткая информация об изменениях -->
                            <div class="row g-2 small">
                                @if($log->old_value)
                                    <div class="col-md-6">
                                        <span class="text-danger">
                                            <i class="bi bi-arrow-left"></i> Было:
                                            @php
                                                $oldData = json_decode($log->old_value, true);
                                                $oldPreview = getChangePreview($oldData);
                                            @endphp
                                            {{ $oldPreview }}
                                        </span>
                                    </div>
                                @endif
                                @if($log->new_value)
                                    <div class="col-md-6">
                                        <span class="text-success">
                                            <i class="bi bi-arrow-right"></i> Стало:
                                            @php
                                                $newData = json_decode($log->new_value, true);
                                                $newPreview = getChangePreview($newData);
                                            @endphp
                                            {{ $newPreview }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Пагинация -->
        @if($logs->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted small">
                Страница {{ $logs->currentPage() }} из {{ $logs->lastPage() }}
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous Page Link --}}
                    @if($logs->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">‹</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $logs->previousPageUrl() }}#logs" rel="prev">‹</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                        @if($page == $logs->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}#logs">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if($logs->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $logs->nextPageUrl() }}#logs" rel="next">›</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">›</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
        @endif

    @else
        <div class="text-center py-5">
            <i class="bi bi-clock-history" style="font-size: 3rem; color: #6c757d;"></i>
            <p class="text-muted mt-3">История изменений пока пуста</p>
        </div>
    @endif
</div>

<!-- Модальное окно просмотра деталей -->
<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📋 Детали изменения</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Действие:</strong>
                        <div id="detailAction" class="fw-bold"></div>
                    </div>
                    <div class="col-md-6">
                        <strong>Пользователь:</strong>
                        <div id="detailUser"></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Дата и время:</strong>
                        <div id="detailDate"></div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-danger mb-2">
                            <i class="bi bi-arrow-left"></i> Было:
                        </h6>
                        <pre class="bg-light p-3 rounded small" id="detailOldValue" style="max-height: 200px; overflow-y: auto;"></pre>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success mb-2">
                            <i class="bi bi-arrow-right"></i> Стало:
                        </h6>
                        <pre class="bg-light p-3 rounded small" id="detailNewValue" style="max-height: 200px; overflow-y: auto;"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Фильтрация логов
    function applyLogFilters() {
        const searchTerm = document.getElementById('searchLogs').value.toLowerCase();
        const userFilter = document.getElementById('userFilter').value;
        const dateFilter = document.getElementById('dateFilter').value;
        const now = new Date();

        document.querySelectorAll('.log-row').forEach(row => {
            const action = row.getAttribute('data-action');
            const user = row.getAttribute('data-user');
            const date = new Date(row.getAttribute('data-date'));
            
            // Поиск по действию
            const showSearch = !searchTerm || action.includes(searchTerm);
            
            // Фильтр по пользователю
            const showUser = userFilter === 'all' || user === userFilter;
            
            // Фильтр по дате
            let showDate = true;
            if (dateFilter !== 'all') {
                const diffTime = now - date;
                const diffDays = diffTime / (1000 * 60 * 60 * 24);
                
                if (dateFilter === 'today' && !isToday(date)) showDate = false;
                else if (dateFilter === 'week' && diffDays > 7) showDate = false;
                else if (dateFilter === 'month' && diffDays > 30) showDate = false;
            }

            row.style.display = (showSearch && showUser && showDate) ? 'flex' : 'none';
        });
    }

    function isToday(date) {
        const today = new Date();
        return date.getDate() === today.getDate() &&
               date.getMonth() === today.getMonth() &&
               date.getFullYear() === today.getFullYear();
    }

    // События фильтрации
    document.getElementById('searchLogs').addEventListener('input', applyLogFilters);
    document.getElementById('userFilter').addEventListener('change', applyLogFilters);
    document.getElementById('dateFilter').addEventListener('change', applyLogFilters);

    window.resetLogFilters = function() {
        document.getElementById('searchLogs').value = '';
        document.getElementById('userFilter').value = 'all';
        document.getElementById('dateFilter').value = 'all';
        applyLogFilters();
    };

    // Изменение количества записей на странице
    window.changePerPage = function(perPage) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.set('page', '1'); // Сбрасываем на первую страницу
        window.location.href = url.toString();
    };

    // Просмотр деталей лога
    const logDetailsModal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
    document.querySelectorAll('.view-log-details').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('detailAction').textContent = this.getAttribute('data-log-action');
            document.getElementById('detailUser').textContent = this.getAttribute('data-log-user');
            document.getElementById('detailDate').textContent = this.getAttribute('data-log-date');
            
            const oldValue = this.getAttribute('data-log-old');
            const newValue = this.getAttribute('data-log-new');
            
            document.getElementById('detailOldValue').textContent = oldValue ? 
                JSON.stringify(JSON.parse(oldValue), null, 2) : '—';
            document.getElementById('detailNewValue').textContent = newValue ? 
                JSON.stringify(JSON.parse(newValue), null, 2) : '—';
            
            logDetailsModal.show();
        });
    });
});
</script>

<style>
.history-tab {
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

.stat-card h5, .stat-card h6 {
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

.log-row:hover .card {
    transform: translateX(5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

/* Адаптивность */
@media (max-width: 768px) {
    .history-tab .row {
        margin-left: -5px;
        margin-right: -5px;
    }
    
    .history-tab .col-6 {
        padding-left: 5px;
        padding-right: 5px;
    }
    
    .stat-card {
        min-width: 70px;
        height: 55px;
    }
    
    .stat-card h5 {
        font-size: 1rem;
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

/* Стили для JSON */
pre {
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
    margin: 0;
}
</style>

@php
function getChangePreview($data) {
    if (!is_array($data)) return '—';
    
    $preview = '';
    $count = 0;
    
    foreach ($data as $key => $value) {
        if ($count >= 2) break;
        
        if (is_array($value)) {
            $preview .= $key . ': { ... }, ';
        } else {
            $preview .= $key . ': ' . (is_string($value) ? substr($value, 0, 20) : $value) . ', ';
        }
        $count++;
    }
    
    return rtrim($preview, ', ') . (count($data) > 2 ? '...' : '');
}
@endphp