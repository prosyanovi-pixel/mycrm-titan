@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Редактирование сделки</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('deals.update', $deal) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            {{-- Клиент с поиском --}}
                            <div class="col-12">
                                <label for="client_search" class="form-label">Клиент *</label>
                                <div class="position-relative">
                                    <input type="text" 
                                        class="form-control {{ $deal->client_id ? 'd-none' : '' }}" 
                                        id="client_search"
                                        placeholder="Начните вводить имя клиента или компанию..."
                                        autocomplete="off"
                                        value="{{ $deal->client ? ($deal->client->company_name ?? $deal->client->getFullName()) : '' }}">
                                    <div class="position-absolute top-100 start-0 end-0 bg-white border mt-1 rounded shadow-lg d-none"
                                        id="client_results"
                                        style="z-index: 1000; max-height: 200px; overflow-y: auto;">
                                    </div>
                                </div>
                                
                                {{-- Скрытое поле для валидации --}}
                                <input type="hidden" name="client_id" id="client_id" value="{{ old('client_id', $deal->client_id) }}" required>
                                
                                {{-- Селект только для данных --}}
                                <select id="client_options" class="d-none">
                                    <option value="">Выберите клиента</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" 
                                                data-client-info="{{ $client->company_name ?? $client->getFullName() }}">
                                            {{ $client->company_name ?? $client->getFullName() }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                <div id="selected_client" class="mt-2 p-2 bg-light rounded {{ $deal->client_id ? '' : 'd-none' }}">
                                    @if($deal->client_id)
                                        <small class="text-muted">Выбранный клиент:</small>
                                        <div class="fw-bold" id="selected_client_name">
                                            {{ $deal->client->company_name ?? $deal->client->getFullName() }}
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="clearClientSelection()">
                                            <i class="bi bi-x"></i> Изменить выбор
                                        </button>
                                    @else
                                        <small class="text-muted">Выбранный клиент:</small>
                                        <div class="fw-bold" id="selected_client_name"></div>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="clearClientSelection()">
                                            <i class="bi bi-x"></i> Изменить выбор
                                        </button>
                                    @endif
                                </div>
                                @error('client_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Название сделки --}}
                            <div class="col-12">
                                <label for="title" class="form-label">Название сделки *</label>
                                <input type="text" name="title" id="title" class="form-control" 
                                       value="{{ old('title', $deal->title) }}" required
                                       placeholder="Введите название сделки">
                                @error('title')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Описание --}}
                            <div class="col-12">
                                <label for="description" class="form-label">Описание</label>
                                <textarea name="description" id="description" class="form-control" 
                                          rows="4"
                                          placeholder="Опишите детали сделки...">{{ old('description', $deal->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Сумма --}}
                            <div class="col-12 col-md-6">
                                <label for="amount" class="form-label">Сумма сделки *</label>
                                <div class="input-group">
                                    <input type="number" name="amount" id="amount" class="form-control" 
                                           value="{{ old('amount', $deal->amount) }}" required step="0.01" min="0"
                                           placeholder="0.00">
                                    <span class="input-group-text">₽</span>
                                </div>
                                @error('amount')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Статус --}}
                            <div class="col-12 col-md-6">
                                <label for="status" class="form-label">Статус *</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="new" {{ old('status', $deal->status) == 'new' ? 'selected' : '' }}>🆕 Новая</option>
                                    <option value="lead" {{ old('status', $deal->status) == 'lead' ? 'selected' : '' }}>🎯 Лид</option>
                                    <option value="proposal" {{ old('status', $deal->status) == 'proposal' ? 'selected' : '' }}>📄 Предложение</option>
                                    <option value="negotiation" {{ old('status', $deal->status) == 'negotiation' ? 'selected' : '' }}>🤝 Переговоры</option>
                                    <option value="won" {{ old('status', $deal->status) == 'won' ? 'selected' : '' }}>✅ Выиграна</option>
                                    <option value="lost" {{ old('status', $deal->status) == 'lost' ? 'selected' : '' }}>❌ Проиграна</option>
                                </select>
                                @error('status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Ожидаемая дата закрытия --}}
                            <div class="col-12">
                                <label for="expected_close_at" class="form-label">Ожидаемая дата закрытия</label>
                                <input type="date" name="expected_close_at" id="expected_close_at" class="form-control"
                                       value="{{ old('expected_close_at', $deal->expected_close_at ? $deal->expected_close_at->format('Y-m-d') : '') }}">
                                <div class="form-text">Оставьте пустым, если дата не определена</div>
                                @error('expected_close_at')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Информация о сделке --}}
                        <div class="mt-3 p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Создана:</small>
                                    <div class="fw-medium">{{ $deal->created_at->format('d.m.Y H:i') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Последнее обновление:</small>
                                    <div class="fw-medium">{{ $deal->updated_at->format('d.m.Y H:i') }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Кнопки --}}
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Сохранить изменения
                                </button>
                                <a href="{{ route('deals.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Отмена
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const clientSearch = document.getElementById('client_search');
    const clientResults = document.getElementById('client_results');
    const clientHidden = document.getElementById('client_id'); // теперь hidden поле
    const clientOptions = document.getElementById('client_options'); // селект с опциями
    const selectedClientDiv = document.getElementById('selected_client');
    const selectedClientName = document.getElementById('selected_client_name');

    // Инициализация выбранного клиента
    const currentClientId = "{{ $deal->client_id }}";
    if (currentClientId) {
        const selectedOption = clientOptions.querySelector(`option[value="${currentClientId}"]`);
        if (selectedOption) {
            selectClient(selectedOption.value, selectedOption.textContent);
        }
    }

    // Поиск клиентов
    clientSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        if (searchTerm.length < 2) {
            clientResults.classList.add('d-none');
            return;
        }

        const options = clientOptions.querySelectorAll('option');
        let resultsHTML = '';
        let foundCount = 0;

        options.forEach(option => {
            if (option.value && option.textContent.toLowerCase().includes(searchTerm)) {
                resultsHTML += `
                    <div class="p-2 border-bottom hover-bg-light cursor-pointer client-option"
                         data-client-id="${option.value}"
                         data-client-name="${option.textContent}">
                        <div class="fw-medium">${option.textContent}</div>
                        <small class="text-muted">ID: ${option.value}</small>
                    </div>
                `;
                foundCount++;
            }
        });

        if (foundCount > 0) {
            clientResults.innerHTML = resultsHTML;
            clientResults.classList.remove('d-none');
            
            document.querySelectorAll('.client-option').forEach(option => {
                option.addEventListener('click', function() {
                    const clientId = this.getAttribute('data-client-id');
                    const clientName = this.getAttribute('data-client-name');
                    selectClient(clientId, clientName);
                    clientResults.classList.add('d-none');
                    clientSearch.value = '';
                });
            });
        } else {
            clientResults.innerHTML = '<div class="p-2 text-muted">Клиенты не найдены</div>';
            clientResults.classList.remove('d-none');
        }
    });

    // Скрываем результаты при клике вне поля
    document.addEventListener('click', function(e) {
        if (!clientSearch.contains(e.target) && !clientResults.contains(e.target)) {
            clientResults.classList.add('d-none');
        }
    });

    // Функция выбора клиента
    function selectClient(clientId, clientName) {
        clientHidden.value = clientId;
        selectedClientName.textContent = clientName;
        selectedClientDiv.classList.remove('d-none');
        clientSearch.classList.add('d-none');
    }

    // Функция очистки выбора клиента
    window.clearClientSelection = function() {
        clientHidden.value = '';
        selectedClientDiv.classList.add('d-none');
        clientSearch.classList.remove('d-none');
        clientSearch.value = '';
        clientSearch.focus();
    }

    // Обработка клавиш в поле поиска
    clientSearch.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            clientResults.classList.add('d-none');
        }
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });

    // Валидация формы - убираем стандартные сообщения
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!clientHidden.value) {
            e.preventDefault();
            alert('Пожалуйста, выберите клиента');
            clearClientSelection();
            return false;
        }
    });
});
</script>

<style>
.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}

.cursor-pointer {
    cursor: pointer;
}

#client_results {
    scrollbar-width: thin;
    scrollbar-color: #dee2e6 #f8f9fa;
}

#client_results::-webkit-scrollbar {
    width: 6px;
}

#client_results::-webkit-scrollbar-track {
    background: #f8f9fa;
}

#client_results::-webkit-scrollbar-thumb {
    background-color: #dee2e6;
    border-radius: 3px;
}
</style>
@endsection