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
                                                    onclick="loadUserData({{ $user->id }})"
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

        <!-- Правая колонка - Роли и структура -->
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
            <div class="list-group-item" data-role-id="{{ $role->id }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">
                            {{ $role->name }}
                            @if($role->is_system_role)
                                <i class="bi bi-shield-check text-warning ms-1 system-role-icon" title="Системная роль"></i>
                            @else
                                <i class="bi bi-shield-check text-warning ms-1 system-role-icon" style="display: none;" title="Системная роль"></i>
                            @endif
                            <small class="text-muted debug-info">(ID: {{ $role->id }}, system: {{ $role->is_system_role }})</small>
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
                                onclick="editRole({{ $role->id }})"
                                title="Редактировать">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @if(!$role->is_system_role)
                        <button class="btn btn-outline-danger btn-sm delete-role-btn"
                                onclick="confirmRoleDelete({{ $role->id }})"
                                title="Удалить">
                            <i class="bi bi-trash"></i>
                        </button>
                        @else
                        <button class="btn btn-outline-danger btn-sm delete-role-btn" style="display: none;"
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
            <!-- Модальное окно редактирования роли -->
            <div class="modal fade" id="editRoleModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Редактирование роли</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="editRoleForm" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="role_id" id="editRoleId">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Название роли:</label>
                                    <input type="text" name="name" class="form-control" required id="editRoleName">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Описание:</label>
                                    <textarea name="description" class="form-control" rows="3" id="editRoleDescription"></textarea>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_system_role" class="form-check-input" id="editRoleIsSystem" value="1">
                                        <label class="form-check-label" for="editRoleIsSystem">Системная роль</label>
                                    </div>
                                    <small class="text-muted">Системные роли нельзя удалить</small>
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

            <!-- Карточка структуры отделов -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">Структура отделов и должностей</h6>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                            <i class="bi bi-building-plus me-1"></i>Добавить отдел
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="department-structure-container">
                        <div class="department-count p-3 border-bottom">{{ $departments->count() }} отделов, {{ $positions->count() }} должностей</div>
                        
                        <div class="department-tree p-3" id="departmentTree">
                            @foreach($departments->where('parent_id', null) as $department)
                                @include('settings.partials.department-tree-item', ['department' => $department])
                            @endforeach
                            @if($departments->where('parent_id', null)->count() === 0)
                                <div class="empty-state text-center text-muted py-4">
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
</div>

<!-- Модальное окно редактирования пользователя -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактирование сотрудника</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="editUserId">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ФИО:</label>
                                <input type="text" name="name" class="form-control" required id="editUserName">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email:</label>
                                <input type="email" name="email" class="form-control" required id="editUserEmail">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Телефон:</label>
                                <input type="tel" name="phone" class="form-control" id="editUserPhone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Дата приема:</label>
                                <input type="date" name="hire_date" class="form-control" required id="editUserHireDate">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Отдел:</label>
                                <select name="department_id" class="form-select" id="editUserDepartment">
                                    <option value="">-- Выберите отдел --</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Должность:</label>
                                <select name="position_id" class="form-select" id="editUserPosition">
                                    <option value="">-- Выберите должность --</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Роли:</label>
                        <select name="roles[]" class="form-select" multiple id="editUserRoles" style="min-height: 120px;">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Удерживайте Ctrl для выбора нескольких ролей</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Доступ к разделам:</label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            @foreach($modules as $module)
                                <div class="form-check mb-2">
                                    <input class="form-check-input module-access" type="checkbox" 
                                        value="{{ $module['id'] }}" id="module_{{ $module['id'] }}">
                                    <label class="form-check-label" for="module_{{ $module['id'] }}">
                                        {{ $module['name'] }}
                                    </label>
                                    @if(isset($module['description']) && $module['description'])
                                        <small class="text-muted d-block">{{ $module['description'] }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Статус:</label>
                                <select name="is_active" class="form-select" id="editUserStatus">
                                    <option value="1">Активен</option>
                                    <option value="0">Неактивен</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Уровень доступа:</label>
                                <select name="access_level" class="form-select" id="editUserAccessLevel">
                                    <option value="user">Пользователь</option>
                                    <option value="manager">Менеджер</option>
                                    <option value="admin">Администратор</option>
                                </select>
                            </div>
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
                <div class="modal-body">
                    <input type="hidden" name="position_id" id="editPositionId">
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
// Функция для показа уведомлений

function reloadWithCurrentTab() {
    const activeTab = document.querySelector('.nav-link.active');
    if (activeTab) {
        const tabHref = activeTab.getAttribute('href');
        const tabName = tabHref.replace('#', '');
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.location.href = url.toString();
    } else {
        location.reload();
    }
}

function showNotification(message, type = 'success') {
    // Создаем элемент уведомления
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Находим контейнер для уведомлений или создаем его
    let notificationContainer = document.getElementById('notification-container');
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'notification-container';
        notificationContainer.style.position = 'fixed';
        notificationContainer.style.top = '20px';
        notificationContainer.style.right = '20px';
        notificationContainer.style.zIndex = '9999';
        notificationContainer.style.minWidth = '300px';
        document.body.appendChild(notificationContainer);
    }
    
    // Добавляем уведомление
    notificationContainer.appendChild(notification);
    
    // Автоматически скрываем через 5 секунд
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Функция редактирования роли
window.editRole = function(roleId) {
    console.log('=== EDIT ROLE START ===', roleId);
    
    // Загружаем данные роли
    fetch(`api/roles/${roleId}`)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(role => {
            console.log('Role data loaded:', role);
            
            // Заполняем форму данными
            document.getElementById('editRoleId').value = role.id;
            document.getElementById('editRoleName').value = role.name;
            document.getElementById('editRoleDescription').value = role.description || '';
            document.getElementById('editRoleIsSystem').checked = Boolean(role.is_system_role);
            
            console.log('Form filled. is_system_role:', role.is_system_role);
            
            // Устанавливаем action формы
            document.getElementById('editRoleForm').action = `/roles/${roleId}`;
            
            // Показываем модальное окно
            const modal = new bootstrap.Modal(document.getElementById('editRoleModal'));
            modal.show();
            
            console.log('=== EDIT ROLE END ===');
        })
        .catch(error => {
            console.error('Error loading role:', error);
            showNotification('Ошибка при загрузке данных роли', 'error');
        });
};

// Обработчик для формы редактирования роли
const editRoleForm = document.getElementById('editRoleForm');
if (editRoleForm) {
    editRoleForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const roleId = document.getElementById('editRoleId').value;
        
        console.log('=== SAVE ROLE START ===');
        console.log('Role ID:', roleId);
        console.log('Form data:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            console.log('Save response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Save response data:', data);
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('editRoleModal'));
                modal.hide();
                showNotification(data.message || 'Роль успешно обновлена', 'success');
                
                // Загружаем обновленные данные и обновляем интерфейс
                setTimeout(() => {
                    fetch(`/api/roles/${roleId}`)
                        .then(response => response.json())
                        .then(updatedRole => {
                            console.log('Updated role data:', updatedRole);
                            // Обновляем страницу после получения подтверждения
                            reloadWithCurrentTab();
                        })
                        .catch(error => {
                            console.error('Error loading updated role:', error);
                            reloadWithCurrentTab();
                        });
                }, 1000);
            } else {
                showNotification('Ошибка: ' + (data.message || 'Неизвестная ошибка'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при обновлении роли', 'error');
        });
    });
}

// Глобальные функции для кнопок действий
window.toggleUserStatus = function(userId) {
    if (confirm('Вы уверены, что хотите изменить статус пользователя?')) {
        fetch(`/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Статус пользователя успешно изменен', 'success');
               setTimeout(() => {
                    reloadWithCurrentTab();
                }, 1500);
            } else {
                showNotification('Ошибка: ' + (data.message || 'Неизвестная ошибка'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при изменении статуса пользователя', 'error');
        });
    }
};

window.confirmDelete = function(userId) {
    if (confirm('Вы уверены, что хотите удалить пользователя?')) {
        fetch(`/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Пользователь успешно удален', 'success');
               setTimeout(() => {
                    reloadWithCurrentTab();
                }, 1500);
            } else {
                showNotification('Ошибка: ' + (data.message || 'Неизвестная ошибка'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при удалении пользователя', 'error');
        });
    }
};

window.confirmRoleDelete = function(roleId) {
    if (confirm('Вы уверены, что хотите удалить эту роль?')) {
        fetch(`/roles/${roleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Роль успешно удалена', 'success');
               setTimeout(() => {
                    reloadWithCurrentTab();
                }, 1500);
            } else {
                showNotification('Ошибка: ' + (data.message || 'Неизвестная ошибка'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при удалении роли', 'error');
        });
    }
};

window.editDepartment = function(departmentId) {
    // Загружаем данные отдела
    fetch(`api/departments/${departmentId}`)
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
            showNotification('Ошибка при загрузке данных отдела', 'error');
        });
};

// Функция загрузки данных пользователя для редактирования
window.loadUserData = function(userId) {
    fetch(`api/users/${userId}`)
        .then(response => response.json())
        .then(user => {
            // Заполняем форму данными пользователя
            document.getElementById('editUserId').value = user.id;
            document.getElementById('editUserName').value = user.name;
            document.getElementById('editUserEmail').value = user.email;
            document.getElementById('editUserPhone').value = user.phone || '';
            document.getElementById('editUserHireDate').value = user.hire_date;
            document.getElementById('editUserDepartment').value = user.department_id || '';
            document.getElementById('editUserPosition').value = user.position_id || '';
            document.getElementById('editUserStatus').value = user.is_active ? '1' : '0';
            document.getElementById('editUserAccessLevel').value = user.access_level || 'user';
            
            // Устанавливаем выбранные роли
            const rolesSelect = document.getElementById('editUserRoles');
            Array.from(rolesSelect.options).forEach(option => {
                option.selected = user.roles && user.roles.some(role => role.id == option.value);
            });
            
            // Устанавливаем доступ к разделам
            document.querySelectorAll('.module-access').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Устанавливаем action формы
            document.getElementById('editUserForm').action = `/users/${userId}`;
            
            // Показываем модальное окно
            const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error loading user:', error);
            showNotification('Ошибка при загрузке данных пользователя', 'error');
        });
};

// Функция редактирования должности
window.editPosition = function(positionId) {
    // Загружаем данные должности
    fetch(`api/positions/${positionId}`)
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
            showNotification('Ошибка при загрузке данных должности', 'error');
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

    // Инициализация функциональности дерева отделов
    document.querySelectorAll('.node-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const node = this.closest('.department-node');
            const isExpanded = node.classList.contains('expanded');
            
            if (isExpanded) {
                node.classList.remove('expanded');
                this.textContent = '+';
            } else {
                node.classList.add('expanded');
                this.textContent = '−';
            }
        });
    });

    // Обработчики для форм редактирования
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
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Закрываем модальное окно
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editPositionModal'));
                    modal.hide();
                    
                    // Показываем уведомление
                    showNotification(data.message || 'Должность успешно обновлена', 'success');
                    
                    // Обновляем страницу через 1.5 секунды для отображения изменений
                    setTimeout(() => {
                        reloadWithCurrentTab();
                    }, 1500);
                } else {
                    showNotification('Ошибка: ' + (data.message || 'Неизвестная ошибка'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Ошибка при обновлении должности', 'error');
            });
        });
    }

    // Обработчик для формы редактирования пользователя
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                    modal.hide();
                    showNotification(data.message || 'Пользователь успешно обновлен', 'success');
                    setTimeout(() => {
                            reloadWithCurrentTab();
                        }, 1500);
                } else {
                    showNotification('Ошибка: ' + (data.message || 'Неизвестная ошибка'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Ошибка при обновлении пользователя', 'error');
            });
        });
    }

    // Обработчик для формы редактирования отдела
    const editDepartmentForm = document.getElementById('editDepartmentForm');
    if (editDepartmentForm) {
        editDepartmentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editDepartmentModal'));
                    modal.hide();
                    showNotification(data.message || 'Отдел успешно обновлен', 'success');
                    setTimeout(() => {
                        reloadWithCurrentTab();
                    }, 1500);
                } else {
                    showNotification('Ошибка: ' + (data.message || 'Неизвестная ошибка'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Ошибка при обновлении отдела', 'error');
            });
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
            showNotification('Пожалуйста, выберите отдел', 'warning');
            return false;
        }
    } else if (step === 2) {
        const positionSelect = document.getElementById('positionSelect');
        if (!positionSelect.value) {
            showNotification('Пожалуйста, выберите должность', 'warning');
            return false;
        }
    }
    return true;
}

window.setDepartmentForPosition = function(departmentId) {
    document.getElementById('positionDepartmentSelect').value = departmentId;
};

function loadPositions() {
    const departmentId = document.getElementById('departmentSelect').value;
    const positionSelect = document.getElementById('positionSelect');
    
    if (!departmentId) {
        positionSelect.innerHTML = '<option value="">-- Выберите должность --</option>';
        return;
    }
    
    fetch(`api/departments/${departmentId}/positions`)
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
            showNotification('Ошибка при загрузке должностей', 'error');
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

.department-structure-container {
    background: white;
}

.department-count {
    color: #6c757d;
    font-size: 14px;
    background: #f8f9fa;
}

.department-tree {
    position: relative;
}

.department-node {
    margin: 8px 0;
    position: relative;
}

.department-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: white;
    border: 1px solid #e1e8ed;
    border-radius: 8px;
    transition: all 0.2s;
    cursor: pointer;
}

.department-content:hover {
    border-color: #3498db;
    box-shadow: 0 2px 8px rgba(52, 152, 219, 0.1);
}

.department-info {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-grow: 1;
}

.department-icon {
    width: 20px;
    height: 20px;
    background: #3498db;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.department-name {
    font-size: 14px;
    color: #2c3e50;
    font-weight: 500;
}

.department-head {
    font-size: 13px;
    color: #7f8c8d;
    margin-left: 8px;
}

.position-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 16px;
    background: #f8f9fa;
    border: 1px solid #e1e8ed;
    border-radius: 6px;
    transition: all 0.2s;
    cursor: pointer;
}

.position-content:hover {
    border-color: #27ae60;
    box-shadow: 0 2px 8px rgba(39, 174, 96, 0.1);
}

.position-info {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-grow: 1;
}

.position-icon {
    width: 18px;
    height: 18px;
    background: #27ae60;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 10px;
    font-weight: bold;
}

.position-name {
    font-size: 13px;
    color: #2c3e50;
    font-weight: 500;
}

.employee-name {
    font-size: 13px;
    color: #7f8c8d;
    margin-left: 8px;
}

.node-toggle {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ecf0f1;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    color: #7f8c8d;
    transition: all 0.2s;
}

.node-toggle:hover {
    background: #3498db;
    color: white;
}

.children {
    margin-left: 40px;
    padding-left: 20px;
    border-left: 2px solid #e1e8ed;
    display: none;
}

.department-node.expanded > .children {
    display: block;
}

.department-actions, .position-actions {
    display: flex;
    gap: 8px;
    opacity: 0;
    transition: opacity 0.2s;
}

.department-content:hover .department-actions,
.position-content:hover .position-actions {
    opacity: 1;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #7f8c8d;
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

.module-access {
    margin-right: 8px;
}
</style>