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
                                       placeholder="Поиск клиентов..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        {{-- Статус --}}
                        <div class="col-md-2">
                            <select name="status" class="form-select form-select-sm border-0 bg-light">
                                <option value="">Все статусы</option>
                                <option value="lead" @selected(request('status')=='lead')>Лид</option>
                                <option value="active" @selected(request('status')=='active')>Активный</option>
                                <option value="inactive" @selected(request('status')=='inactive')>Неактивный</option>
                            </select>
                        </div>

                        {{-- Тип клиента --}}
                        <div class="col-md-2">
                            <select name="type" class="form-select form-select-sm border-0 bg-light">
                                <option value="">Все типы</option>
                                <option value="individual" @selected(request('type')=='individual')>Физ. лицо</option>
                                <option value="entrepreneur" @selected(request('type')=='entrepreneur')>ИП</option>
                                <option value="legal" @selected(request('type')=='legal')>Юр. лицо</option>
                            </select>
                        </div>

                        {{-- Ответственный --}}
                        <div class="col-md-2">
                            <select name="responsible_id" class="form-select form-select-sm border-0 bg-light">
                                <option value="">Все менеджеры</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(request('responsible_id')==$user->id)>
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
                                
                                <a href="{{ route('clients.create') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-circle"></i> Добавить
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Таблица клиентов --}}
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-dark">{!! sortable_icon('id', 'ID') !!}</th>
                                    <th class="text-dark">{!! sortable_icon('type', 'Тип') !!}</th>
                                    <th class="text-dark">{!! sortable_icon('display_name', 'Имя / Компания') !!}</th>
                                    <th class="text-dark">{!! sortable_icon('inn', 'ИНН') !!}</th>
                                    <th class="text-dark">{!! sortable_icon('email', 'Email') !!}</th>
                                    <th class="text-dark">{!! sortable_icon('phone', 'Телефон') !!}</th>
                                    <th class="text-dark">{!! sortable_icon('status', 'Статус') !!}</th>
                                    <th class="text-dark">{!! sortable_icon('responsible_id', 'Ответственный') !!}</th>
                                    <th width="140" class="text-dark">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                    <tr>
                                        <td class="fw-bold">{{ $client->id }}</td>

                                        {{-- Тип --}}
                                        <td>
                                            @if($client->type === 'individual')
                                                <span class="badge bg-info">👤 ФЛ</span>
                                            @elseif($client->type === 'entrepreneur')
                                                <span class="badge bg-primary">💼 ИП</span>
                                            @else
                                                <span class="badge bg-success">🏢 ЮЛ</span>
                                            @endif
                                        </td>

                                        {{-- Имя / компания --}}
                                        <td>
                                            <strong>{{ $client->display_name }}</strong>
                                            @if($client->address)
                                                <br><small class="text-muted">{{ Str::limit($client->address, 40) }}</small>
                                            @endif
                                        </td>

                                        {{-- ИНН --}}
                                        <td>
                                            @if($client->inn)
                                                <code>{{ $client->inn }}</code>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Email --}}
                                        <td>
                                            @if($client->email)
                                                <a href="mailto:{{ $client->email }}" class="text-decoration-none">
                                                    {{ Str::limit($client->email, 20) }}
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Телефон --}}
                                        <td>
                                            @if($client->phone)
                                                <a href="tel:{{ $client->phone }}" class="text-decoration-none">
                                                    {{ $client->phone }}
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Статус --}}
                                        <td>
                                            @if($client->status === 'active')
                                                <span class="badge bg-success">✅ Активный</span>
                                            @elseif($client->status === 'lead')
                                                <span class="badge bg-secondary">🎯 Лид</span>
                                            @else
                                                <span class="badge bg-dark">💤 Неактивный</span>
                                            @endif
                                        </td>

                                        {{-- Ответственный --}}
                                        <td>
                                            @if($client->responsible)
                                                {{ $client->responsible->first_name }} {{ $client->responsible->last_name }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        {{-- Действия --}}
                                        <td>
                                            <div class="d-flex gap-1 justify-content-center">
                                                {{-- Просмотр --}}
                                                <a href="{{ route('clients.show', $client) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Просмотр"
                                                   data-bs-toggle="tooltip">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                
                                                {{-- Редактирование --}}
                                                <a href="{{ route('clients.edit', $client) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Редактировать"
                                                   data-bs-toggle="tooltip">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                {{-- Контакты --}}
                                                @if($client->phone || $client->email)
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-info dropdown-toggle" 
                                                            type="button" 
                                                            data-bs-toggle="dropdown"
                                                            title="Контакты"
                                                            data-bs-toggle="tooltip">
                                                        <i class="bi bi-telephone"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @if($client->phone)
                                                        <li>
                                                            <a class="dropdown-item" href="tel:{{ $client->phone }}">
                                                                <i class="bi bi-telephone me-2"></i>Позвонить
                                                            </a>
                                                        </li>
                                                        @endif
                                                        @if($client->email)
                                                        <li>
                                                            <a class="dropdown-item" href="mailto:{{ $client->email }}">
                                                                <i class="bi bi-envelope me-2"></i>Написать
                                                            </a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                                @endif
                                                
                                                {{-- Удаление --}}
                                                <form action="{{ route('clients.destroy', $client) }}" method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Удалить"
                                                            data-bs-toggle="tooltip"
                                                            onclick="return confirm('Удалить клиента?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="bi bi-people display-4 d-block mb-2"></i>
                                            Клиенты не найдены
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Пагинация --}}
                    @if($clients->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Показано с {{ $clients->firstItem() }} по {{ $clients->lastItem() }} из {{ $clients->total() }} клиентов
                            </small>
                        </div>
                        <div>
                            {{ $clients->appends(request()->query())->links() }}
                        </div>
                    </div>
                    @endif
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
.btn-outline-warning:hover,
.btn-outline-info:hover,
.btn-outline-danger:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.dropdown-toggle::after {
    margin-left: 0.25rem;
}

.table-responsive {
    border-radius: 0.5rem;
}

.table th a {
    color: #000 !important;
    text-decoration: none !important;
}

.table th a:hover {
    color: #000 !important;
    text-decoration: underline !important;
}
</style>
@endsection