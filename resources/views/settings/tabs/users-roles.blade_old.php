<div class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">👥 Управление пользователями и ролями</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
            <i class="bi bi-person-plus me-1"></i>Добавить сотрудника
        </button>
    </div>

    <div class="row">
        <!-- Левая колонка - Пользователи -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Список сотрудников</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Сотрудник</th>
                                    <th>Должность/Отдел</th>
                                    <th>Роли</th>
                                    <th>Статус</th>
                                    <th>Дата приема</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title bg-light rounded">
                                                    <i class="bi bi-person text-primary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->position && $user->department)
                                            <div>
                                                <strong>{{ $user->position->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $user->department->name }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Не назначено</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->roles && $user->roles->count() > 0)
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-{{ $role->is_system_role ? 'warning' : 'info' }} mb-1">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-secondary">Нет ролей</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                            {{ $user->is_active ? 'Активен' : 'Неактивен' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $user->hire_date ? $user->hire_date->format('d.m.Y') : '—' }}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editUserModal"
                                                    data-user-id="{{ $user->id }}"
                                                    title="Редактировать">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                                                    onclick="toggleUserStatus({{ $user->id }})"
                                                    title="{{ $user->is_active ? 'Деактивировать' : 'Активировать' }}">
                                                <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                            @if(!$user->is_admin)
                                            <button class="btn btn-outline-danger" 
                                                    onclick="confirmDelete({{ $user->id }})"
                                                    title="Удалить">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Правая колонка - Роли и права -->
        <div class="col-md-4">
            <!-- Карточка ролей -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Роли системы</h6>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($roles as $role)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        {{ $role->name }}
                                        @if($role->is_system_role)
                                            <i class="bi bi-shield-check text-warning ms-1" title="Системная роль"></i>
                                        @endif
                                    </h6>
                                    <small class="text-muted">{{ $role->description }}</small>
                                    <div class="mt-1">
                                        <small class="text-muted">
                                            {{ $role->users_count ?? $role->users->count() }} пользователей • 
                                            {{ $role->permissions_count ?? $role->permissions->count() }} прав
                                        </small>
                                    </div>
                                </div>
                                <div class="btn-group btn-group-sm ms-2">
                                    <button class="btn btn-outline-primary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editRoleModal"
                                            data-role-id="{{ $role->id }}"
                                            title="Редактировать">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if(!$role->is_system_role)
                                    <button class="btn btn-outline-danger btn-sm"
                                            onclick="confirmRoleDelete({{ $role->id }})"
                                            title="Удалить">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Карточка отделов и должностей -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Структура отделов и должностей</h6>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                            <i class="bi bi-building-plus me-1"></i>Добавить отдел
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="department-tree">
                        @foreach($departments->where('parent_id', null) as $department)
                            @include('settings.partials.department-tree-item', ['department' => $department])
                        @endforeach
                        @if($departments->where('parent_id', null)->count() === 0)
                            <div class="text-center text-muted py-3">
                                <i class="bi bi-folder-x fs-1"></i>
                                <p class="mt-2">Нет отделов</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно создания сотрудника -->
<div class="modal fade" id="createEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавление сотрудника</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Шаг 1: Выбор отдела -->
                    <div class="onboarding-step" id="step1">
                        <h6>Шаг 1: Выбор отдела</h6>
                        <div class="mb-3">
                            <label class="form-label">Отдел:</label>
                            <select name="department_id" class="form-select" required id="departmentSelect">
                                <option value="">-- Выберите отдел --</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Шаг 2: Выбор должности -->
                    <div class="onboarding-step d-none" id="step2">
                        <h6>Шаг 2: Выбор должности</h6>
                        <div class="mb-3">
                            <label class="form-label">Должность:</label>
                            <select name="position_id" class="form-select" required id="positionSelect">
                                <option value="">-- Выберите должность --</option>
                                <!-- Позиции будут загружены динамически -->
                            </select>
                        </div>
                    </div>

                    <!-- Шаг 3: Данные сотрудника -->
                    <div class="onboarding-step d-none" id="step3">
                        <h6>Шаг 3: Данные сотрудника</h6>
                        <div class="mb-3">
                            <label class="form-label">ФИО:</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email:</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Дата приема:</label>
                            <input type="date" name="hire_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Роли:</label>
                            <select name="roles[]" class="form-select" multiple>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Удерживайте Ctrl для выбора нескольких ролей</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">Назад</button>
                    <button type="button" class="btn btn-primary" id="nextBtn">Далее</button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">Создать сотрудника</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно создания отдела -->
<div class="modal fade" id="createDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Создание отдела</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Название отдела:</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание:</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Родительский отдел:</label>
                        <select name="parent_id" class="form-select">
                            <option value="">-- Без родительского отдела --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Менеджер:</label>
                        <select name="manager_id" class="form-select">
                            <option value="">-- Не назначен --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать отдел</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования отдела -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактирование отдела</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editDepartmentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="department_id" id="editDepartmentId">
                    <div class="mb-3">
                        <label class="form-label">Название отдела:</label>
                        <input type="text" name="name" class="form-control" required id="editDepartmentName">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание:</label>
                        <textarea name="description" class="form-control" rows="3" id="editDepartmentDescription"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Родительский отдел:</label>
                        <select name="parent_id" class="form-select" id="editDepartmentParent">
                            <option value="">-- Без родительского отдела --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Менеджер:</label>
                        <select name="manager_id" class="form-select" id="editDepartmentManager">
                            <option value="">-- Не назначен --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
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

<!-- Модальное окно создания должности -->
<div class="modal fade" id="createPositionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Создание должности</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('positions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Название должности:</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Отдел:</label>
                        <select name="department_id" class="form-select" required id="positionDepartmentSelect">
                            <option value="">-- Выберите отдел --</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Руководитель:</label>
                        <select name="parent_position_id" class="form-select">
                            <option value="">-- Без руководителя --</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}">
                                    {{ $position->name }} 
                                    ({{ $position->department->name ?? 'Отдел не указан' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_manager" class="form-check-input" id="isManagerCheck" value="1">
                            <label class="form-check-label" for="isManagerCheck">Руководящая должность</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать должность</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования должности -->
<div class="modal fade" id="editPositionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактирование должности</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPositionForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="position_id" id="editPositionId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Название должности:</label>
                        <input type="text" name="name" class="form-control" required id="editPositionName">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Отдел:</label>
                        <select name="department_id" class="form-select" required id="editPositionDepartment">
                            <option value="">-- Выберите отдел --</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Руководитель:</label>
                        <select name="parent_position_id" class="form-select" id="editPositionParent">
                            <option value="">-- Без руководителя --</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}">
                                    {{ $position->name }} ({{ $position->department->name ?? 'Отдел не указан' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_manager" class="form-check-input" id="editPositionIsManager" value="1">
                            <label class="form-check-label" for="editPositionIsManager">Руководящая должность</label>
                        </div>
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

<!-- Модальное окно создания роли -->
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Создание роли</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Название роли:</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание:</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_system_role" class="form-check-input" id="systemRoleCheck" value="1">
                            <label class="form-check-label" for="systemRoleCheck">Системная роль</label>
                        </div>
                        <small class="text-muted">Системные роли нельзя удалить</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать роль</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Глобальные функции для кнопок действий
window.toggleUserStatus = function(userId) {
    if (confirm('Вы уверены, что хотите изменить статус пользователя?')) {
        fetch(`/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при изменении статуса пользователя');
        });
    }
};

window.confirmDelete = function(userId) {
    if (confirm('Вы уверены, что хотите удалить пользователя?')) {
        fetch(`/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при удалении пользователя');
        });
    }
};

window.confirmRoleDelete = function(roleId) {
    if (confirm('Вы уверены, что хотите удалить эту роль?')) {
        fetch(`/roles/${roleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при удалении роли');
        });
    }
};

window.editDepartment = function(departmentId) {
    // Загружаем данные отдела
    fetch(`/api/departments/${departmentId}`)
        .then(response => response.json())
        .then(department => {
            // Заполняем форму данными
            document.getElementById('editDepartmentId').value = department.id;
            document.getElementById('editDepartmentName').value = department.name;
            document.getElementById('editDepartmentDescription').value = department.description || '';
            document.getElementById('editDepartmentParent').value = department.parent_id || '';
            document.getElementById('editDepartmentManager').value = department.manager_id || '';
            
            // Устанавливаем action формы
            document.getElementById('editDepartmentForm').action = `/departments/${departmentId}`;
            
            // Показываем модальное окно
            const modal = new bootstrap.Modal(document.getElementById('editDepartmentModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error loading department:', error);
            alert('Ошибка при загрузке данных отдела');
        });
};

// Код для мастера создания сотрудника
let currentStep = 1;

document.addEventListener('DOMContentLoaded', function() {
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
            }
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            currentStep--;
            showStep(currentStep);
        });
    }

    const modal = document.getElementById('createEmployeeModal');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            currentStep = 1;
            showStep(currentStep);
            document.querySelector('form').reset();
        });
    }
});

function showStep(step) {
    document.querySelectorAll('.onboarding-step').forEach(el => {
        el.classList.add('d-none');
    });
    
    const currentStepEl = document.getElementById('step' + step);
    if (currentStepEl) {
        currentStepEl.classList.remove('d-none');
    }
    
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    if (prevBtn) prevBtn.style.display = step > 1 ? 'inline-block' : 'none';
    if (nextBtn) nextBtn.style.display = step < 3 ? 'inline-block' : 'none';
    if (submitBtn) submitBtn.style.display = step === 3 ? 'inline-block' : 'none';
    
    if (step === 2) {
        loadPositions();
    }
}

function validateStep(step) {
    if (step === 1) {
        const departmentSelect = document.getElementById('departmentSelect');
        if (!departmentSelect.value) {
            alert('Пожалуйста, выберите отдел');
            return false;
        }
    } else if (step === 2) {
        const positionSelect = document.getElementById('positionSelect');
        if (!positionSelect.value) {
            alert('Пожалуйста, выберите должность');
            return false;
        }
    }
    return true;
}

window.setDepartmentForPosition = function(departmentId) {
    document.getElementById('positionDepartmentSelect').value = departmentId;
};

// Функция редактирования должности
    window.editPosition = function(positionId) {
        // Загружаем данные должности
        fetch(`/api/positions/${positionId}`)
            .then(response => response.json())
            .then(position => {
                // Заполняем форму данными
                document.getElementById('editPositionId').value = position.id;
                document.getElementById('editPositionName').value = position.name;
                document.getElementById('editPositionDepartment').value = position.department_id || '';
                document.getElementById('editPositionParent').value = position.parent_position_id || '';
                document.getElementById('editPositionIsManager').checked = position.is_manager || false;
                
                // Устанавливаем action формы
                document.getElementById('editPositionForm').action = `/positions/${positionId}`;
                
                // Показываем модальное окно
                const modal = new bootstrap.Modal(document.getElementById('editPositionModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error loading position:', error);
                alert('Ошибка при загрузке данных должности');
            });
    };

    // Обработчик отправки формы редактирования должности
    document.addEventListener('DOMContentLoaded', function() {
        const editPositionForm = document.getElementById('editPositionForm');
        if (editPositionForm) {
            editPositionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const positionId = document.getElementById('editPositionId').value;
                
                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Закрываем модальное окно
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editPositionModal'));
                        modal.hide();
                        
                        // Показываем уведомление
                        alert(data.message || 'Должность успешно обновлена');
                        
                        // Обновляем страницу через 1 секунду для отображения изменений
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при обновлении должности');
                });
            });
        }
    });

function loadPositions() {
    const departmentId = document.getElementById('departmentSelect').value;
    const positionSelect = document.getElementById('positionSelect');
    
    if (!departmentId) {
        positionSelect.innerHTML = '<option value="">-- Выберите должность --</option>';
        return;
    }
    
    fetch(`/api/departments/${departmentId}/positions`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(positions => {
            positionSelect.innerHTML = '<option value="">-- Выберите должность --</option>';
            positions.forEach(position => {
                positionSelect.innerHTML += `<option value="${position.id}">${position.name}</option>`;
            });
        })
        .catch(error => {
            console.error('Error loading positions:', error);
            positionSelect.innerHTML = '<option value="">Ошибка загрузки должностей</option>';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('departmentSelect');
    if (departmentSelect) {
        departmentSelect.addEventListener('change', function() {
            if (currentStep >= 2) {
                loadPositions();
            }
        });
    }
});
</script>

<style>
.onboarding-step {
    transition: all 0.3s ease;
    min-height: 200px;
}

.department-tree .department-item {
    padding: 8px 12px;
    margin: 2px 0;
    border-radius: 4px;
    background: #f8f9fa;
    border-left: 3px solid #0d6efd;
}

.department-tree .department-children {
    margin-left: 20px;
    border-left: 2px solid #dee2e6;
    padding-left: 15px;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-select[multiple] {
    min-height: 120px;
}
</style>