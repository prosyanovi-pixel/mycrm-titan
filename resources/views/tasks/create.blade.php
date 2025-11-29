@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Создание новой задачи</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-3">
                            {{-- Клиент с поиском --}}
                            <div class="col-12">
                                <label for="client_search" class="form-label">Клиент *</label>
                                <div class="position-relative">
                                    <input type="text" 
                                           class="form-control" 
                                           id="client_search"
                                           placeholder="Начните вводить имя клиента или компанию..."
                                           autocomplete="off">
                                    <div class="position-absolute top-100 start-0 end-0 bg-white border mt-1 rounded shadow-lg d-none"
                                         id="client_results"
                                         style="z-index: 1000; max-height: 200px; overflow-y: auto;">
                                    </div>
                                </div>
                                <select name="client_id" id="client_id" class="form-select mt-2" required style="display: none;">
                                    <option value="">Выберите клиента</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" 
                                                {{ old('client_id') == $client->id ? 'selected' : '' }}
                                                data-client-info="{{ $client->company_name ?? $client->getFullName() }}">
                                            {{ $client->company_name ?? $client->getFullName() }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="selected_client" class="mt-2 p-2 bg-light rounded d-none">
                                    <small class="text-muted">Выбранный клиент:</small>
                                    <div class="fw-bold" id="selected_client_name"></div>
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="clearClientSelection()">
                                        <i class="bi bi-x"></i> Изменить выбор
                                    </button>
                                </div>
                                @error('client_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Название --}}
                            <div class="col-12">
                                <label for="title" class="form-label">Название задачи *</label>
                                <input type="text" name="title" id="title" class="form-control" 
                                       value="{{ old('title') }}" required
                                       placeholder="Введите название задачи">
                                @error('title')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Описание --}}
                            <div class="col-12">
                                <label for="description" class="form-label">Описание</label>
                                <textarea name="description" id="description" class="form-control" 
                                          rows="4"
                                          placeholder="Опишите детали задачи...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Статус --}}
                            <div class="col-12 col-md-6">
                                <label for="status" class="form-label">Статус *</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="open" {{ old('status', 'open') == 'open' ? 'selected' : '' }}>📝 Открыта</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>⚡ В работе</option>
                                    <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>✅ Выполнена</option>
                                </select>
                                @error('status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Приоритет --}}
                            <div class="col-12 col-md-6">
                                <label for="priority" class="form-label">Приоритет *</label>
                                <select name="priority" id="priority" class="form-select" required>
                                    <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>🟢 Низкий</option>
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>🟡 Средний</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>🔴 Высокий</option>
                                </select>
                                @error('priority')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Ответственный --}}
                            <div class="col-12 col-md-6">
                                <label for="assigned_to" class="form-label">Ответственный</label>
                                <select name="assigned_to" id="assigned_to" class="form-select">
                                    <option value="">👤 Не назначен</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                                {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Срок --}}
                            <div class="col-12 col-md-6">
                                <label for="due_date" class="form-label">Срок выполнения</label>
                                <input type="date" name="due_date" id="due_date" class="form-control"
                                       value="{{ old('due_date') }}"
                                       min="{{ date('Y-m-d') }}">
                                <div class="form-text">Оставьте пустым, если срок не установлен</div>
                                @error('due_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Кнопки --}}
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-lg"></i> Создать задачу
                                </button>
                                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
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
    const clientSelect = document.getElementById('client_id');
    const selectedClientDiv = document.getElementById('selected_client');
    const selectedClientName = document.getElementById('selected_client_name');

    // Инициализация выбранного клиента из старых данных формы
    const oldClientId = "{{ old('client_id') }}";
    if (oldClientId) {
        const selectedOption = clientSelect.querySelector(`option[value="${oldClientId}"]`);
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

        const options = clientSelect.querySelectorAll('option');
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
            
            // Добавляем обработчики для результатов поиска
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
        clientSelect.value = clientId;
        selectedClientName.textContent = clientName;
        selectedClientDiv.classList.remove('d-none');
        clientSearch.classList.add('d-none');
    }

    // Функция очистки выбора клиента
    window.clearClientSelection = function() {
        clientSelect.value = '';
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

    // Устанавливаем минимальную дату как сегодняшнюю
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('due_date').min = today;
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

.form-select, .form-control {
    border-radius: 0.5rem;
}

.card {
    border-radius: 0.75rem;
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    border-radius: 0.5rem;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}
</style>
@endsection