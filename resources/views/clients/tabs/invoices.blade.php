<div class="invoices-tab">
    <!-- Заголовок -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">🧾 Счета клиента</h4>
    </div>

    <!-- Статистика и кнопка в одной линии -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <!-- Статистика -->
        <div class="d-flex flex-wrap gap-2">
            <div class="card bg-light stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->invoices->count() }}</h5>
                    <small class="text-muted">Всего</small>
                </div>
            </div>
            <div class="card bg-success text-white stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->invoices->where('status', 'paid')->count() }}</h5>
                    <small>Оплачено</small>
                </div>
            </div>
            <div class="card bg-info text-white stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->invoices->where('status', 'sent')->count() }}</h5>
                    <small>Отправлено</small>
                </div>
            </div>
            <div class="card bg-warning stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->invoices->where('status', 'draft')->count() }}</h5>
                    <small>Черновики</small>
                </div>
            </div>
            <div class="card bg-danger text-white stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $client->invoices->where('status', 'overdue')->count() }}</h5>
                    <small>Просрочено</small>
                </div>
            </div>
        </div>
        
        <!-- Кнопка создания счета -->
        <button class="btn btn-primary btn-lg" 
                data-bs-toggle="modal" 
                data-bs-target="#invoiceModal">
            <i class="bi bi-plus-circle"></i> Новый счет
        </button>
    </div>

    <!-- Фильтры -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="filter-buttons d-flex flex-wrap gap-1">
                <button class="btn btn-outline-secondary btn-sm active" data-filter="all">
                    Все ({{ $client->invoices->count() }})
                </button>
                <button class="btn btn-outline-success btn-sm" data-filter="paid">
                    Оплачено ({{ $client->invoices->where('status', 'paid')->count() }})
                </button>
                <button class="btn btn-outline-info btn-sm" data-filter="sent">
                    Отправлено ({{ $client->invoices->where('status', 'sent')->count() }})
                </button>
                <button class="btn btn-outline-warning btn-sm" data-filter="draft">
                    Черновики ({{ $client->invoices->where('status', 'draft')->count() }})
                </button>
                <button class="btn btn-outline-danger btn-sm" data-filter="overdue">
                    Просрочено ({{ $client->invoices->where('status', 'overdue')->count() }})
                </button>
            </div>
        </div>
    </div>

    <!-- Таблица счетов -->
@if($client->invoices->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Дата выставления</th>
                    <th>Дата оплаты</th>
                    <th width="200" class="text-center">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($client->invoices as $invoice)
                <tr class="invoice-row" data-status="{{ $invoice->status }}">
                    <td>
                        <strong>#{{ $invoice->id }}</strong>
                    </td>

                    <td>
                        <strong>{{ number_format($invoice->amount, 0, ',', ' ') }} ₽</strong>
                    </td>

                    <td>
                        <div class="dropdown">
                            <button class="badge bg-{{ [
                                'draft' => 'warning',
                                'sent' => 'info', 
                                'paid' => 'success',
                                'overdue' => 'danger'
                            ][$invoice->status] }} dropdown-toggle border-0 d-inline-flex align-items-center" 
                                    type="button"
                                    data-bs-toggle="dropdown" 
                                    data-bs-auto-close="true"
                                    style="cursor: pointer; font-size: 0.75em; padding: 0.35em 0.65em;"
                                    aria-expanded="false">
                                @if($invoice->status === 'draft') 📝 Черновик
                                @elseif($invoice->status === 'sent') 📤 Отправлен
                                @elseif($invoice->status === 'paid') ✅ Оплачен
                                @elseif($invoice->status === 'overdue') ⚠️ Просрочен
                                @endif
                            </button>
                            <ul class="dropdown-menu">
                                <li><button type="button" class="dropdown-item change-invoice-status" data-status="draft">📝 Черновик</button></li>
                                <li><button type="button" class="dropdown-item change-invoice-status" data-status="sent">📤 Отправлен</button></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button type="button" class="dropdown-item change-invoice-status text-success" data-status="paid">✅ Оплачен</button></li>
                                <li><button type="button" class="dropdown-item change-invoice-status text-danger" data-status="overdue">⚠️ Просрочен</button></li>
                            </ul>
                        </div>
                    </td>

                    <td>
                        @if($invoice->issued_at)
                            <small>{{ $invoice->issued_at->format('d.m.Y') }}</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td>
                        @if($invoice->paid_at)
                            <small class="text-success">{{ $invoice->paid_at->format('d.m.Y') }}</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td>
                        <div class="d-flex justify-content-center gap-1 flex-nowrap">
                            <!-- Отправить счет -->
                            @if($invoice->status === 'draft')
                                <button class="btn btn-outline-info btn-sm send-invoice" 
                                        data-invoice-id="{{ $invoice->id }}"
                                        title="Отправить счет"
                                        style="min-width: 36px;">
                                    <i class="bi bi-envelope"></i>
                                </button>
                            @endif

                            <!-- Пометить как оплачено -->
                            @if($invoice->status !== 'paid')
                                <button class="btn btn-outline-success btn-sm pay-invoice" 
                                        data-invoice-id="{{ $invoice->id }}"
                                        title="Пометить как оплачено"
                                        style="min-width: 36px;">
                                    <i class="bi bi-check2-circle"></i>
                                </button>
                            @endif

                            <!-- Редактировать -->
                            <button class="btn btn-outline-primary btn-sm edit-invoice" 
                                    data-invoice-id="{{ $invoice->id }}"
                                    data-invoice-amount="{{ $invoice->amount }}"
                                    data-invoice-issued_at="{{ $invoice->issued_at?->format('Y-m-d') }}"
                                    title="Редактировать"
                                    style="min-width: 36px;">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <!-- Удалить -->
                            <button class="btn btn-outline-danger btn-sm delete-invoice" 
                                    data-invoice-id="{{ $invoice->id }}"
                                    data-invoice-amount="{{ $invoice->amount }}"
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
        <i class="bi bi-receipt" style="font-size: 3rem; color: #6c757d;"></i>
        <p class="text-muted mt-3">Счетов пока нет. Создайте первый счет!</p>
    </div>
@endif



</div>

<!-- Модальное окно создания счета -->
<div class="modal fade" id="invoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('clients.invoices.store', $client) }}" method="POST" id="createInvoiceForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">🧾 Новый счет</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Поле для отображения ошибок -->
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Сумма *</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" name="amount" 
                                   class="form-control @error('amount') is-invalid @enderror" 
                                   value="{{ old('amount') }}" required placeholder="0.00">
                            <span class="input-group-text">₽</span>
                        </div>
                        @error('amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Дата выставления</label>
                        <input type="date" name="issued_at" 
                               class="form-control @error('issued_at') is-invalid @enderror" 
                               value="{{ old('issued_at') ?? now()->format('Y-m-d') }}">
                        @error('issued_at')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Описание (необязательно)</label>
                        <textarea name="description" class="form-control" rows="2" 
                                  placeholder="Описание счета...">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать счет</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования счета -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editInvoiceForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Редактирование счета</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Сумма *</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="amount" class="form-control" required id="editInvoiceAmount">
                            <span class="input-group-text">₽</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Дата выставления</label>
                        <input type="date" name="issued_at" class="form-control" id="editInvoiceIssuedAt">
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
<div class="modal fade" id="deleteInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">❌ Удаление счета</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить счет на сумму <strong id="deleteInvoiceAmount"></strong>?</p>
                <p class="text-muted small">Это действие нельзя отменить.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form id="deleteInvoiceForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить счет</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Фильтрация счетов по статусу
    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Активная кнопка
            document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Показываем/скрываем строки
            document.querySelectorAll('.invoice-row').forEach(row => {
                if (filter === 'all' || row.getAttribute('data-status') === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // ИНИЦИАЛИЗАЦИЯ ВЫПАДАЮЩИХ МЕНЮ СТАТУСОВ
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (toggle && menu) {
            // Обработчик для изменения статуса
            menu.querySelectorAll('.change-invoice-status').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation(); // ВАЖНО: останавливаем всплытие
                    
                    const newStatus = this.getAttribute('data-status');
                    const invoiceId = this.closest('tr').querySelector('.edit-invoice').getAttribute('data-invoice-id');
                    
                    // Закрываем выпадающее меню
                    const dropdownInstance = bootstrap.Dropdown.getInstance(toggle);
                    if (dropdownInstance) {
                        dropdownInstance.hide();
                    }
                    
                    if (confirm('Изменить статус счета?')) {
                        fetch(`/clients/{{ $client->id }}/invoices/${invoiceId}/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                status: newStatus
                            })
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
        }
    });

    // Отправка счета
    document.querySelectorAll('.send-invoice').forEach(btn => {
        btn.addEventListener('click', function() {
            const invoiceId = this.getAttribute('data-invoice-id');
            
            if (confirm('Отправить счет клиенту?')) {
                fetch(`/clients/{{ $client->id }}/invoices/${invoiceId}/send`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Ошибка при отправке счета');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при отправке счета');
                });
            }
        });
    });

    // Отметка как оплачено
    document.querySelectorAll('.pay-invoice').forEach(btn => {
        btn.addEventListener('click', function() {
            const invoiceId = this.getAttribute('data-invoice-id');
            
            if (confirm('Пометить счет как оплаченный?')) {
                fetch(`/clients/{{ $client->id }}/invoices/${invoiceId}/pay`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Ошибка при отметке оплаты');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при отметке оплаты');
                });
            }
        });
    });

    // Редактирование счета
    const editInvoiceModal = new bootstrap.Modal(document.getElementById('editInvoiceModal'));
    document.querySelectorAll('.edit-invoice').forEach(btn => {
        btn.addEventListener('click', function() {
            const invoiceId = this.getAttribute('data-invoice-id');
            const form = document.getElementById('editInvoiceForm');
            
            form.action = `/clients/{{ $client->id }}/invoices/${invoiceId}`;
            document.getElementById('editInvoiceAmount').value = this.getAttribute('data-invoice-amount');
            document.getElementById('editInvoiceIssuedAt').value = this.getAttribute('data-invoice-issued_at') || '';
            
            editInvoiceModal.show();
        });
    });

    // Удаление счета
    const deleteInvoiceModal = new bootstrap.Modal(document.getElementById('deleteInvoiceModal'));
    document.querySelectorAll('.delete-invoice').forEach(btn => {
        btn.addEventListener('click', function() {
            const invoiceId = this.getAttribute('data-invoice-id');
            const invoiceAmount = this.getAttribute('data-invoice-amount');
            const form = document.getElementById('deleteInvoiceForm');
            
            form.action = `/clients/{{ $client->id }}/invoices/${invoiceId}`;
            document.getElementById('deleteInvoiceAmount').textContent = `${parseFloat(invoiceAmount).toLocaleString('ru-RU')} ₽`;
            
            deleteInvoiceModal.show();
        });
    });
});
</script>

<style>
.invoices-tab {
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

/* РЕШЕНИЕ ДЛЯ ВЫПАДАЮЩИХ МЕНЮ */
.table-responsive {
    position: relative;
    overflow-x: auto;
    /* Разрешаем overflow по вертикали для выпадающих меню */
    overflow-y: visible;
}

/* Сбрасываем стандартное поведение для выпадающих меню в таблицах */
.table .dropdown {
    position: relative;
}

.table .dropdown-menu {
    position: absolute;
    /* Принудительно позиционируем вниз от кнопки */
    top: 100%;
    left: 0;
    margin-top: 0.125rem;
    z-index: 1060;
}

/* Базовые стили для выпадающих меню */
.dropdown-menu {
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    padding: 0.5rem 0;
    min-width: 160px;
    background: white;
}

.dropdown-menu.show {
    display: block;
    animation: dropdownFadeIn 0.15s ease-out;
}

@keyframes dropdownFadeIn {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown-item {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    transition: all 0.15s ease-in-out;
    cursor: pointer;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #16181b;
}

.dropdown-item.text-success:hover {
    background-color: #d1e7dd;
    color: #0f5132 !important;
}

.dropdown-item.text-danger:hover {
    background-color: #f8d7da;
    color: #721c24 !important;
}

.dropdown-divider {
    margin: 0.5rem 0;
    border-top: 1px solid #dee2e6;
}

/* Баджи статусов */
.badge {
    font-size: 0.75em;
    padding: 0.5em 0.75em;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.25em;
}

.badge.bg-warning {
    color: #000 !important;
}

.badge.dropdown-toggle::after {
    margin-left: 0.255em;
    vertical-align: 0.155em;
    border-top: 0.3em solid;
    border-right: 0.3em solid transparent;
    border-left: 0.3em solid transparent;
}

/* Для модальных окон */
.modal .dropdown-menu {
    z-index: 1070;
}

/* Адаптивность */
@media (max-width: 768px) {
    .invoices-tab .d-flex.flex-wrap {
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

    /* На мобильных - центрируем меню */
    .table-responsive .dropdown-menu {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        width: 90vw;
        max-width: 280px;
    }
}

/* Гарантируем, что меню поверх всего */
.dropdown-menu {
    z-index: 1060 !important;
}
</style>