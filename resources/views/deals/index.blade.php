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
                                       placeholder="Поиск сделок..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        {{-- Статус --}}
                        <div class="col-md-2">
                            <select name="status" class="form-select form-select-sm border-0 bg-light">
                                <option value="">Все статусы</option>
                                <option value="new" @selected(request('status')=='new')>🆕 Новая</option>
                                <option value="lead" @selected(request('status')=='lead')>🎯 Лид</option>
                                <option value="proposal" @selected(request('status')=='proposal')>📄 Предложение</option>
                                <option value="negotiation" @selected(request('status')=='negotiation')>🤝 Переговоры</option>
                                <option value="won" @selected(request('status')=='won')>✅ Выиграна</option>
                                <option value="lost" @selected(request('status')=='lost')>❌ Проиграна</option>
                            </select>
                        </div>

                        {{-- Клиент --}}
                        <div class="col-md-2">
                            <select name="client_id" class="form-select form-select-sm border-0 bg-light">
                                <option value="">Все клиенты</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" @selected(request('client_id')==$client->id)>
                                        {{ $client->company_name ?? $client->getFullName() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Сумма --}}
                        <div class="col-md-2">
                            <select name="amount_filter" class="form-select form-select-sm border-0 bg-light">
                                <option value="">Любая сумма</option>
                                <option value="0-50000" @selected(request('amount_filter')=='0-50000')>До 50 тыс.</option>
                                <option value="50000-200000" @selected(request('amount_filter')=='50000-200000')>50-200 тыс.</option>
                                <option value="200000-500000" @selected(request('amount_filter')=='200000-500000')>200-500 тыс.</option>
                                <option value="500000+" @selected(request('amount_filter')=='500000+')>Свыше 500 тыс.</option>
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
                                
                                <a href="{{ route('deals.create') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-circle"></i> Добавить
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Основной контент с таблицей и статистикой --}}
            <div class="row">
                {{-- Таблица сделок (занимает 9 колонок) --}}
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-dark">Сделка</th>
                                            <th class="text-dark">Клиент</th>
                                            <th class="text-dark">Статус</th>
                                            <th class="text-dark">Сумма</th>
                                            <th class="text-dark">Создана</th>
                                            <th class="text-dark">Ожидаемое закрытие</th>
                                            <th class="text-dark">Автор</th>
                                            <th width="120" class="text-dark">Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($deals as $deal)
                                            <tr>
                                                <td>
                                                    <strong>{{ $deal->title }}</strong>
                                                    @if($deal->description)
                                                        <br><small class="text-muted">{{ Str::limit($deal->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($deal->client)
                                                        <a href="{{ route('clients.show', $deal->client_id) }}" class="text-decoration-none">
                                                            {{ $deal->client->company_name ?? $deal->client->getFullName() }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'new' => 'secondary',
                                                            'lead' => 'info',
                                                            'proposal' => 'primary',
                                                            'negotiation' => 'warning',
                                                            'won' => 'success',
                                                            'lost' => 'danger'
                                                        ];
                                                        $statusLabels = [
                                                            'new' => '🆕 Новая',
                                                            'lead' => '🎯 Лид',
                                                            'proposal' => '📄 Предложение',
                                                            'negotiation' => '🤝 Переговоры',
                                                            'won' => '✅ Выиграна',
                                                            'lost' => '❌ Проиграна'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColors[$deal->status] ?? 'secondary' }}">
                                                        {{ $statusLabels[$deal->status] ?? $deal->status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong>{{ number_format($deal->amount, 0, ',', ' ') }} ₽</strong>
                                                </td>
                                                <td>
                                                    {{ $deal->created_at->format('d.m.Y') }}
                                                </td>
                                                <td>
                                                    @if($deal->expected_close_at)
                                                        @php
                                                            $expectedDate = \Carbon\Carbon::parse($deal->expected_close_at);
                                                        @endphp
                                                        {{ $expectedDate->format('d.m.Y') }}
                                                        @if($expectedDate->isPast() && !in_array($deal->status, ['won', 'lost']))
                                                            <span class="badge bg-danger">Просрочена</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($deal->createdByUser)
                                                        {{ $deal->createdByUser->first_name }} {{ $deal->createdByUser->last_name }}
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        {{-- Редактирование --}}
                                                        <a href="{{ route('deals.edit', $deal->id) }}" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           title="Редактировать"
                                                           data-bs-toggle="tooltip">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        
                                                        {{-- Клиент --}}
                                                        <a href="{{ route('clients.show', $deal->client_id) }}" 
                                                           class="btn btn-sm btn-outline-info" 
                                                           title="Перейти к клиенту"
                                                           data-bs-toggle="tooltip">
                                                            <i class="bi bi-person"></i>
                                                        </a>
                                                        
                                                        {{-- Удаление --}}
                                                        <form action="{{ route('deals.destroy', $deal->id) }}" method="POST" 
                                                              class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-outline-danger" 
                                                                    title="Удалить"
                                                                    data-bs-toggle="tooltip"
                                                                    onclick="return confirm('Удалить сделку?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    <i class="bi bi-briefcase display-4 d-block mb-2"></i>
                                                    Сделки не найдены
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Пагинация --}}
                            @if($deals->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <small class="text-muted">
                                        Показано с {{ $deals->firstItem() }} по {{ $deals->lastItem() }} из {{ $deals->total() }} сделок
                                    </small>
                                </div>
                                <div>
                                    {{ $deals->links() }}
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
                                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Статистика сделок</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small">Всего сделок</span>
                                        <span class="badge bg-primary rounded-pill">{{ $deals->total() }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small text-success">Выиграно</span>
                                        <span class="badge bg-success rounded-pill">{{ $deals->where('status', 'won')->count() }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small text-warning">В работе</span>
                                        <span class="badge bg-warning rounded-pill">{{ $deals->whereIn('status', ['new', 'lead', 'proposal', 'negotiation'])->count() }}</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span class="small text-danger">Проиграно</span>
                                        <span class="badge bg-danger rounded-pill">{{ $deals->where('status', 'lost')->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Финансовая статистика --}}
                        <div class="card">
                            <div class="card-header bg-success text-white py-2">
                                <h6 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Финансы</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item py-2">
                                        <div class="small text-muted">Общая сумма</div>
                                        <div class="fw-bold text-success">{{ number_format($deals->sum('amount'), 0, ',', ' ') }} ₽</div>
                                    </div>
                                    <div class="list-group-item py-2">
                                        <div class="small text-muted">Выиграно на сумму</div>
                                        <div class="fw-bold">{{ number_format($deals->where('status', 'won')->sum('amount'), 0, ',', ' ') }} ₽</div>
                                    </div>
                                    <div class="list-group-item py-2">
                                        <div class="small text-muted">В работе на сумму</div>
                                        <div class="fw-bold">{{ number_format($deals->whereIn('status', ['new', 'lead', 'proposal', 'negotiation'])->sum('amount'), 0, ',', ' ') }} ₽</div>
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
}
</style>
@endsection