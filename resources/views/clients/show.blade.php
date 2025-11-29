@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Клиент: {{ $client->display_name }}</h2>

        <a href="{{ route('clients.index') }}" class="btn btn-secondary">
            ← Назад к списку
        </a>
    </div>

    {{-- Вкладки --}}
    <ul class="nav nav-tabs mb-3" id="clientTabs" role="tablist">

        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="info-tab" data-bs-toggle="tab"
                data-bs-target="#info" type="button" role="tab">
                📋 Информация
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="banking-tab" data-bs-toggle="tab"
                data-bs-target="#banking" type="button" role="tab">
                🏦 Банковские реквизиты
            </button>
        </li>


        <li class="nav-item" role="presentation">
            <button class="nav-link" id="interactions-tab" data-bs-toggle="tab"
                data-bs-target="#interactions" type="button" role="tab">
                💬 Взаимодействия
                 <span class="badge bg-secondary ms-1">{{ $client->interactions->count() }}</span>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="files-tab" data-bs-toggle="tab"
                data-bs-target="#files" type="button" role="tab">
                📁 Файлы
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tasks-tab" data-bs-toggle="tab"
                data-bs-target="#tasks" type="button" role="tab">
                ✅ Задачи
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="deals-tab" data-bs-toggle="tab"
                data-bs-target="#deals" type="button" role="tab">
                💰 Сделки
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="invoices-tab" data-bs-toggle="tab"
                data-bs-target="#invoices" type="button" role="tab">
                🧾 Счета
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="logs-tab" data-bs-toggle="tab"
                data-bs-target="#logs" type="button" role="tab">
                📊 История изменений
            </button>
        </li>

    </ul>

    <div class="tab-content" id="clientTabsContent">

        {{-- ВКЛАДКА 1: Информация --}}
        <div class="tab-pane fade show active" id="info" role="tabpanel">
            <div class="card">
                <div class="card-body">

                    <h4 class="mb-3">📋 Основные данные</h4>

                    <table class="table table-bordered">
                        <tr>
                            <th>Тип клиента</th>
                            <td>
                                @if($client->type === 'individual')
                                    <span class="badge bg-info">👤 Физическое лицо</span>
                                @elseif($client->type === 'entrepreneur')
                                    <span class="badge bg-primary">💼 Индивидуальный предприниматель</span>
                                @else
                                    <span class="badge bg-success">🏢 Юридическое лицо</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Имя / Компания</th>
                            <td>{{ $client->display_name }}</td>
                        </tr>

                        <tr>
                            <th>Телефон</th>
                            <td>
                                @if($client->phone)
                                    <a href="tel:{{ $client->phone }}" class="text-decoration-none">
                                        📞 {{ $client->phone }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td>
                                @if($client->email)
                                    <a href="mailto:{{ $client->email }}" class="text-decoration-none">
                                        ✉️ {{ $client->email }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Статус</th>
                            <td>
                                @if($client->status === 'active')
                                    <span class="badge bg-success">✅ Активный</span>
                                @elseif($client->status === 'lead')
                                    <span class="badge bg-warning text-dark">🎯 Лид</span>
                                @else
                                    <span class="badge bg-secondary">❌ Неактивный</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Адрес</th>
                            <td>{{ $client->address ?? '—' }}</td>
                        </tr>

                        <tr>
                            <th>Ответственный</th>
                            <td>
                                @if($client->responsible)
                                    👤 {{ $client->responsible->name }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Теги</th>
                            <td>
                                @if($client->tags && count($client->tags) > 0)
                                    @foreach($client->tags as $tag)
                                        <span class="badge bg-primary">🏷️ {{ $tag }}</span>
                                    @endforeach
                                @else
                                    —
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Источник</th>
                            <td>{{ $client->source ?? '—' }}</td>
                        </tr>

                        <tr>
                            <th>Общая выручка</th>
                            <td>
                                <strong class="text-success">💰 {{ number_format($client->total_revenue, 2, ',', ' ') }} ₽</strong>
                            </td>
                        </tr>

                        <tr>
                            <th>Активность</th>
                            <td>
                                @if($client->last_activity_at)
                                    @php
                                        $lastActivity = is_string($client->last_activity_at) 
                                            ? \Carbon\Carbon::parse($client->last_activity_at)
                                            : $client->last_activity_at;
                                    @endphp
                                    🕒 {{ $lastActivity->format('d.m.Y H:i') }}
                                    <small class="text-muted">(балл: {{ $client->activity_score ?? 0 }})</small>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Комментарий</th>
                            <td>{{ $client->notes ?? '—' }}</td>
                        </tr>

                        <tr>
                            <th>Создан</th>
                            <td>📅 {{ $client->created_at->format('d.m.Y H:i') }}</td>
                        </tr>

                        <tr>
                            <th>Обновлен</th>
                            <td>✏️ {{ $client->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Редактировать
                        </a>
                    </div>

                </div>
            </div>
        </div>

       {{-- ВКЛАДКА 2: Банковские реквизиты --}}
        <div class="tab-pane fade" id="banking" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">🏦 Банковские реквизиты</h4>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBankAccountModal">
                            <i class="bi bi-plus-circle"></i> Добавить счет
                        </button>
                    </div>
                    
                    @if($client->bankAccounts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Основной</th>
                                        <th>Банк</th>
                                        <th>Счет</th>
                                        <th>Валюта</th>
                                        <th>БИК</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($client->bankAccounts as $account)
                                        <tr>
                                            <td>
                                                @if($account->is_default)
                                                    <span class="badge bg-success" title="Основной счет">★</span>
                                                @else
                                                    <form action="{{ route('clients.bank-accounts.set-default', [$client, $account]) }}" 
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" 
                                                                title="Сделать основным">
                                                            ☆
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>{{ $account->bank_name }}</td>
                                            <td>
                                                <small class="text-muted">Р/с:</small> {{ $account->account_number }}<br>
                                                @if($account->correspondent_account)
                                                    <small class="text-muted">К/с:</small> {{ $account->correspondent_account }}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $account->currency }}
                                                </span>
                                            </td>
                                            <td>{{ $account->bik ?? '—' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-warning" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editBankAccountModal"
                                                            data-account-id="{{ $account->id }}"
                                                            data-bank-name="{{ $account->bank_name }}"
                                                            data-account-number="{{ $account->account_number }}"
                                                            data-correspondent-account="{{ $account->correspondent_account }}"
                                                            data-bik="{{ $account->bik }}"
                                                            data-inn="{{ $account->inn }}"
                                                            data-kpp="{{ $account->kpp }}"
                                                            data-currency="{{ $account->currency }}"
                                                            data-is-default="{{ $account->is_default }}"
                                                            data-notes="{{ $account->notes }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('clients.bank-accounts.destroy', [$client, $account]) }}" 
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" 
                                                                onclick="return confirm('Удалить этот счет?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bank" style="font-size: 3rem; color: #6c757d;"></i>
                            <p class="text-muted mt-3">Банковские реквизиты не добавлены</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ВКЛАДКА 3: Взаимодействия --}}
        <div class="tab-pane fade" id="interactions" role="tabpanel">
            @include('clients.tabs.interactions')
        </div>

        {{-- ВКЛАДКА 4: Файлы --}}
        <div class="tab-pane fade" id="files" role="tabpanel">
            @include('clients.tabs.files')
        </div>

        {{-- ВКЛАДКА 5: Задачи --}}
        <div class="tab-pane fade" id="tasks" role="tabpanel">
            @include('clients.tabs.tasks')
        </div>

        {{-- ВКЛАДКА 6: Сделки --}}
        <div class="tab-pane fade" id="deals" role="tabpanel">
            @include('clients.tabs.deals')
        </div>

        {{-- ВКЛАДКА 7: Счета --}}
        <div class="tab-pane fade" id="invoices" role="tabpanel">
            @include('clients.tabs.invoices')
        </div>

        {{-- ВКЛАДКА 8: История изменений --}}
        <div class="tab-pane fade" id="logs" role="tabpanel">
            @include('clients.tabs.logs')
        </div>
    </div>

    {{-- Модальное окно добавления счета --}}
    <div class="modal fade" id="addBankAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('clients.bank-accounts.store', $client) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Добавить банковский счет</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Наименование банка --}}
                        <div class="mb-3">
                            <label class="form-label">Наименование банка *</label>
                            <input type="text" class="form-control" name="bank_name" required placeholder="Введите полное название банка">
                        </div>

                        {{-- Расчетный счет --}}
                        <div class="mb-3">
                            <label class="form-label">Расчетный счет *</label>
                            <input type="text" class="form-control" name="account_number" required placeholder="Введите номер расчетного счета">
                        </div>

                        {{-- Корреспондентский счет --}}
                        <div class="mb-3">
                            <label class="form-label">Корреспондентский счет</label>
                            <input type="text" class="form-control" name="correspondent_account" placeholder="Введите номер корреспондентского счета">
                        </div>

                        {{-- БИК --}}
                        <div class="mb-3">
                            <label class="form-label">БИК банка</label>
                            <input type="text" class="form-control" name="bik" placeholder="Введите БИК банка">
                        </div>

                        {{-- ИНН --}}
                        <div class="mb-3">
                            <label class="form-label">ИНН</label>
                            <input type="text" class="form-control" name="inn" placeholder="Введите ИНН клиента">
                        </div>

                        {{-- КПП --}}
                        <div class="mb-3">
                            <label class="form-label">КПП</label>
                            <input type="text" class="form-control" name="kpp" placeholder="Введите КПП (для юридических лиц)">
                        </div>

                        {{-- Валюта --}}
                        <div class="mb-3">
                            <label class="form-label">Валюта счета *</label>
                            <select class="form-select" name="currency" required>
                                @foreach(App\Models\ClientBankAccount::getCurrencies() as $key => $value)
                                    <option value="{{ $key }}" {{ $key == 'RUB' ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Примечания --}}
                        <div class="mb-3">
                            <label class="form-label">Примечания</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Дополнительная информация о счете"></textarea>
                        </div>

                        {{-- Основной счет --}}
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_default" value="1" id="isDefaultAdd">
                            <label class="form-check-label" for="isDefaultAdd">
                                Сделать основным счетом
                            </label>
                            <div class="form-text">Если отмечено, этот счет будет использоваться по умолчанию для операций</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Добавить счет</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Модальное окно редактирования счета --}}
    <div class="modal fade" id="editBankAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editBankAccountForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Редактировать банковский счет</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Наименование банка --}}
                        <div class="mb-3">
                            <label class="form-label">Наименование банка *</label>
                            <input type="text" class="form-control" name="bank_name" required id="editBankName" placeholder="Введите полное название банка">
                        </div>

                        {{-- Расчетный счет --}}
                        <div class="mb-3">
                            <label class="form-label">Расчетный счет *</label>
                            <input type="text" class="form-control" name="account_number" required id="editAccountNumber" placeholder="Введите номер расчетного счета">
                        </div>

                        {{-- Корреспондентский счет --}}
                        <div class="mb-3">
                            <label class="form-label">Корреспондентский счет</label>
                            <input type="text" class="form-control" name="correspondent_account" id="editCorrespondentAccount" placeholder="Введите номер корреспондентского счета">
                        </div>

                        {{-- БИК --}}
                        <div class="mb-3">
                            <label class="form-label">БИК банка</label>
                            <input type="text" class="form-control" name="bik" id="editBik" placeholder="Введите БИК банка">
                        </div>

                        {{-- ИНН --}}
                        <div class="mb-3">
                            <label class="form-label">ИНН</label>
                            <input type="text" class="form-control" name="inn" id="editInn" placeholder="Введите ИНН клиента">
                        </div>

                        {{-- КПП --}}
                        <div class="mb-3">
                            <label class="form-label">КПП</label>
                            <input type="text" class="form-control" name="kpp" id="editKpp" placeholder="Введите КПП (для юридических лиц)">
                        </div>

                        {{-- Валюта --}}
                        <div class="mb-3">
                            <label class="form-label">Валюта счета *</label>
                            <select class="form-select" name="currency" required id="editCurrency">
                                @foreach(App\Models\ClientBankAccount::getCurrencies() as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Примечания --}}
                        <div class="mb-3">
                            <label class="form-label">Примечания</label>
                            <textarea class="form-control" name="notes" rows="3" id="editNotes" placeholder="Дополнительная информация о счете"></textarea>
                        </div>

                        {{-- Основной счет --}}
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_default" value="1" id="editIsDefault">
                            <label class="form-check-label" for="editIsDefault">
                                Сделать основным счетом
                            </label>
                            <div class="form-text">Если отмечено, этот счет будет использоваться по умолчанию для операций</div>
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Восстанавливаем из URL hash
        const hash = window.location.hash;
        if (hash && hash !== '#') {
            const tab = document.querySelector(`[data-bs-target="${hash}"]`);
            if (tab) {
                new bootstrap.Tab(tab).show();
            }
        }

        // 2. Восстанавливаем из localStorage (резервный вариант)
        const activeTab = localStorage.getItem('clientActiveTab');
        if (activeTab && !hash) {
            const tab = document.querySelector(`[data-bs-target="${activeTab}"]`);
            if (tab) {
                new bootstrap.Tab(tab).show();
            }
        }

        // 3. Сохраняем при переключении
        document.querySelectorAll('#clientTabs button[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function (e) {
                const target = e.target.getAttribute('data-bs-target');
                localStorage.setItem('clientActiveTab', target);
                history.replaceState(null, null, target);
            });
        });

        // 4. Сохраняем при отправке форм
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const activeTab = document.querySelector('#clientTabs .nav-link.active');
                if (activeTab) {
                    const target = activeTab.getAttribute('data-bs-target');
                    localStorage.setItem('clientActiveTab', target);
                    
                    // Добавляем hash к action, если его нет
                    if (!form.getAttribute('action').includes('#')) {
                        form.setAttribute('action', form.getAttribute('action') + target);
                    }
                }
            });
        });

        // Обработка модального окна редактирования счета
        const editModal = document.getElementById('editBankAccountModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const accountId = button.getAttribute('data-account-id');
                const form = document.getElementById('editBankAccountForm');
                
                // Обновляем action формы
                form.action = `/clients/{{ $client->id }}/bank-accounts/${accountId}`;
                
                // Заполняем поля данными
                document.getElementById('editBankName').value = button.getAttribute('data-bank-name');
                document.getElementById('editAccountNumber').value = button.getAttribute('data-account-number');
                document.getElementById('editCorrespondentAccount').value = button.getAttribute('data-correspondent-account') || '';
                document.getElementById('editBik').value = button.getAttribute('data-bik') || '';
                document.getElementById('editInn').value = button.getAttribute('data-inn') || '';
                document.getElementById('editKpp').value = button.getAttribute('data-kpp') || '';
                document.getElementById('editCurrency').value = button.getAttribute('data-currency');
                document.getElementById('editNotes').value = button.getAttribute('data-notes') || '';
                
                // Устанавливаем чекбокс основного счета
                const isDefault = button.getAttribute('data-is-default') === '1';
                document.getElementById('editIsDefault').checked = isDefault;
            });
        }

        // Очистка формы добавления при закрытии модального окна
        const addModal = document.getElementById('addBankAccountModal');
        if (addModal) {
            addModal.addEventListener('hidden.bs.modal', function() {
                const form = this.querySelector('form');
                form.reset();
                // Устанавливаем валюту по умолчанию обратно на RUB
                form.querySelector('select[name="currency"]').value = 'RUB';
            });
        }
    });
    </script>

<style>
.modal-content {
    border-radius: 10px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.modal-header {
    background: #f8f9fa;
    color: #212529;
    border-bottom: 1px solid #dee2e6;
    border-radius: 10px 10px 0 0;
    padding: 1rem 1.5rem;
}

.modal-header .btn-close {
    filter: none;
    opacity: 0.7;
}

.modal-header .btn-close:hover {
    opacity: 1;
}

.modal-title {
    font-weight: 600;
    font-size: 1.25rem;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    border-radius: 0 0 10px 10px;
    padding: 1rem 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #ced4da;
    padding: 10px 12px;
    font-size: 0.9rem;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.form-text {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 4px;
}

/* Кастомные стили для кнопок */
.btn-primary {
    --bs-btn-color: #fff;
    --bs-btn-bg: #0d6efd;
    --bs-btn-border-color: #0d6efd;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: #0b5ed7;
    --bs-btn-hover-border-color: #0a58ca;
    --bs-btn-focus-shadow-rgb: 49, 132, 253;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg: #0a58ca;
    --bs-btn-active-border-color: #0a53be;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #fff;
    --bs-btn-disabled-bg: #0d6efd;
    --bs-btn-disabled-border-color: #0d6efd;
    
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    font-weight: 500;
    transition: all 0.15s ease-in-out;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
}

.btn-primary:active {
    transform: translateY(0);
    box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
}

.btn-secondary {
    --bs-btn-color: #fff;
    --bs-btn-bg: #6c757d;
    --bs-btn-border-color: #6c757d;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: #5c636a;
    --bs-btn-hover-border-color: #565e64;
    --bs-btn-focus-shadow-rgb: 130, 138, 145;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg: #565e64;
    --bs-btn-active-border-color: #51585e;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #fff;
    --bs-btn-disabled-bg: #6c757d;
    --bs-btn-disabled-border-color: #6c757d;
    
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    font-weight: 500;
    transition: all 0.15s ease-in-out;
}

.btn-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
}

.btn-secondary:active {
    transform: translateY(0);
    box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
}

.btn-warning {
    --bs-btn-color: #000;
    --bs-btn-bg: #ffc107;
    --bs-btn-border-color: #ffc107;
    --bs-btn-hover-color: #000;
    --bs-btn-hover-bg: #ffca2c;
    --bs-btn-hover-border-color: #ffc720;
    --bs-btn-focus-shadow-rgb: 217, 164, 6;
    --bs-btn-active-color: #000;
    --bs-btn-active-bg: #ffcd39;
    --bs-btn-active-border-color: #ffc720;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #000;
    --bs-btn-disabled-bg: #ffc107;
    --bs-btn-disabled-border-color: #ffc107;
    
    border: none;
    border-radius: 6px;
    padding: 8px 16px;
    font-weight: 500;
    transition: all 0.15s ease-in-out;
}

.btn-outline-warning {
    --bs-btn-color: #ffc107;
    --bs-btn-bg: transparent;
    --bs-btn-border-color: #ffc107;
    --bs-btn-hover-color: #000;
    --bs-btn-hover-bg: #ffc107;
    --bs-btn-hover-border-color: #ffc107;
    --bs-btn-focus-shadow-rgb: 255, 193, 7;
    --bs-btn-active-color: #000;
    --bs-btn-active-bg: #ffc107;
    --bs-btn-active-border-color: #ffc107;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #ffc107;
    --bs-btn-disabled-bg: transparent;
    --bs-btn-disabled-border-color: #ffc107;
    
    border-radius: 6px;
    padding: 6px 12px;
    transition: all 0.15s ease-in-out;
}

.btn-outline-danger {
    --bs-btn-color: #dc3545;
    --bs-btn-bg: transparent;
    --bs-btn-border-color: #dc3545;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: #dc3545;
    --bs-btn-hover-border-color: #dc3545;
    --bs-btn-focus-shadow-rgb: 220, 53, 69;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg: #dc3545;
    --bs-btn-active-border-color: #dc3545;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #dc3545;
    --bs-btn-disabled-bg: transparent;
    --bs-btn-disabled-border-color: #dc3545;
    
    border-radius: 6px;
    padding: 6px 12px;
    transition: all 0.15s ease-in-out;
}

.btn-outline-secondary {
    --bs-btn-color: #6c757d;
    --bs-btn-bg: transparent;
    --bs-btn-border-color: #6c757d;
    --bs-btn-hover-color: #fff;
    --bs-btn-hover-bg: #6c757d;
    --bs-btn-hover-border-color: #6c757d;
    --bs-btn-focus-shadow-rgb: 108, 117, 125;
    --bs-btn-active-color: #fff;
    --bs-btn-active-bg: #6c757d;
    --bs-btn-active-border-color: #6c757d;
    --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
    --bs-btn-disabled-color: #6c757d;
    --bs-btn-disabled-bg: transparent;
    --bs-btn-disabled-border-color: #6c757d;
    
    border-radius: 6px;
    padding: 6px 12px;
    transition: all 0.15s ease-in-out;
}

/* Стили для чекбоксов */
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Анимация появления модального окна */
.modal.fade .modal-dialog {
    transform: translate(0, -50px);
    transition: transform 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: none;
}
</style>

</div>
@endsection