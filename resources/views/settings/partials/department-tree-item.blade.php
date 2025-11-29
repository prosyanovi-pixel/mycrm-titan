<!-- resources/views/settings/partials/department-tree-item.blade.php -->
<div class="department-node" data-department-id="{{ $department->id }}">
    <div class="department-content">
        <div class="department-info">
            <div class="node-toggle">
                @if(($department->positions && $department->positions->count() > 0) || ($department->children && $department->children->count() > 0))
                    +
                @endif
            </div>
            <div class="department-icon" style="background: {{ $department->color ?? '#3498db' }};">
                {{ substr($department->name, 0, 2) }}
            </div>
            <div class="flex-grow-1">
                <div class="department-name">{{ $department->name }}</div>
                @if($department->manager)
                    <div class="department-head">
                        <small>Руководитель: {{ $department->manager->name }}</small>
                    </div>
                @endif
                <div class="department-stats">
                    <small class="text-muted">
                        {{ $department->positions ? $department->positions->count() : 0 }} должностей • 
                        {{ $department->getEmployeesCount() ?? 0 }} сотрудников
                    </small>
                </div>
            </div>
        </div>
        <div class="department-actions">
            <button class="btn btn-outline-success btn-sm" 
                    onclick="quickAddEmployee({{ $department->id }})"
                    title="Добавить сотрудника">
                <i class="bi bi-person-plus"></i>
            </button>
            <button class="btn btn-outline-primary btn-sm" 
                    onclick="editDepartment({{ $department->id }})"
                    title="Редактировать">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-outline-danger btn-sm" 
                    onclick="confirmDepartmentDelete({{ $department->id }})"
                    title="Удалить">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
    
    <div class="children">
        <!-- Должности в отделе -->
        @if($department->positions && $department->positions->count() > 0)
            @foreach($department->positions as $position)
                <div class="position-node" data-position-id="{{ $position->id }}">
                    <div class="position-content">
                        <div class="position-info">
                            <div class="position-icon" style="background: {{ $position->is_manager ? '#e74c3c' : '#27ae60' }};">
                                {{ $position->is_manager ? 'Р' : substr($position->name, 0, 1) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="position-name">{{ $position->name }}</div>
                                @if($position->is_manager)
                                    <span class="badge bg-warning ms-1">Руководящая</span>
                                @endif
                            </div>
                        </div>
                        <div class="position-actions">
                            <button class="btn btn-outline-success btn-sm" 
                                    onclick="quickAddEmployee({{ $department->id }}, {{ $position->id }})"
                                    title="Добавить сотрудника">
                                <i class="bi bi-person-plus"></i>
                            </button>
                            <button class="btn btn-outline-primary btn-sm" 
                                    onclick="editPosition({{ $position->id }})"
                                    title="Редактировать">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" 
                                    onclick="confirmPositionDelete({{ $position->id }})"
                                    title="Удалить">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Сотрудники на должности -->
                    <div class="position-employees">
                        @if($position->users && $position->users->count() > 0)
                            @foreach($position->users as $user)
                                <div class="employee-item" data-user-id="{{ $user->id }}">
                                    <div class="employee-info" onclick="showEmployeeInfo({{ $user->id }})">
                                        <div class="employee-avatar">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="employee-details">
                                            <div class="employee-name">{{ $user->name }}</div>
                                            <div class="employee-email">{{ $user->email }}</div>
                                            <div class="employee-status">
                                                <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                                    {{ $user->is_active ? 'Активен' : 'Неактивен' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="employee-actions">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="moveEmployee({{ $user->id }}, {{ $department->id }}, {{ $position->id }})"
                                                title="Переместить сотрудника">
                                            <i class="bi bi-arrow-right"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" 
                                                onclick="removeEmployeeFromPosition({{ $user->id }})"
                                                title="Убрать с должности">
                                            <i class="bi bi-person-dash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-position">
                                <small class="text-muted">Нет сотрудников на этой должности</small>
                            </div>
                        @endif
                        
                        <!-- Кнопка добавления сотрудника в конкретную должность -->
                        <button class="add-employee-btn" 
                                onclick="quickAddEmployee({{ $department->id }}, {{ $position->id }})">
                            <i class="bi bi-person-plus"></i>
                            Добавить сотрудника
                        </button>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-positions">
                <small class="text-muted">Нет должностей в отделе</small>
            </div>
        @endif
        
        <!-- Кнопка добавления должности в отдел -->
        <div class="add-position-container">
            <button class="btn btn-sm btn-outline-primary w-100" 
                    onclick="setDepartmentForPosition({{ $department->id }})"
                    data-bs-toggle="modal" 
                    data-bs-target="#createPositionModal">
                <i class="bi bi-person-badge-plus"></i>
                Добавить должность в отдел
            </button>
        </div>
        
        <!-- Подотделы -->
        @if($department->children && $department->children->count() > 0)
            @foreach($department->children as $childDepartment)
                @include('settings.partials.department-tree-item', ['department' => $childDepartment])
            @endforeach
        @endif
    </div>
</div>