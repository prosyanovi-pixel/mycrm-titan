<div class="department-item mb-3 border rounded p-3">
    <!-- Заголовок отдела -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="flex-grow-1">
            <h6 class="mb-1">
                <i class="bi bi-building me-1 text-primary"></i>
                <strong>{{ $department->name }}</strong>
                @if($department->manager)
                    <small class="text-muted ms-2">
                        <i class="bi bi-person-badge me-1"></i>рук: {{ $department->manager->name }}
                    </small>
                @endif
            </h6>
            @if($department->description)
                <small class="text-muted">{{ $department->description }}</small>
            @endif
            <div class="mt-1">
                <small class="text-muted">
                    <i class="bi bi-people me-1"></i>{{ $department->users_count ?? $department->users->count() }} сотрудников
                    @if($department->positions && $department->positions->count() > 0)
                        • <i class="bi bi-person-badge me-1"></i>{{ $department->positions->count() }} должностей
                    @endif
                </small>
            </div>
        </div>
        <div class="btn-group btn-group-sm ms-3">
            <button class="btn btn-outline-success btn-sm" 
                    data-bs-toggle="modal" 
                    data-bs-target="#createPositionModal"
                    data-department-id="{{ $department->id }}"
                    onclick="setDepartmentForPosition({{ $department->id }})"
                    title="Добавить должность">
                <i class="bi bi-person-plus"></i>
            </button>
            <button class="btn btn-outline-primary btn-sm" 
                    onclick="editDepartment({{ $department->id }})"
                    title="Редактировать отдел">
                <i class="bi bi-pencil"></i>
            </button>
            @if(!$department->users || $department->users->count() === 0)
            <button class="btn btn-outline-danger btn-sm" 
                    onclick="deleteDepartment({{ $department->id }})"
                    title="Удалить отдел">
                <i class="bi bi-trash"></i>
            </button>
            @endif
        </div>
    </div>

    <!-- Должности в отделе -->
    @if($department->positions && $department->positions->count() > 0)
        <div class="positions-list ms-3 mt-2">
            <h6 class="text-muted mb-2"><small>Должности:</small></h6>
            <div class="row g-2">
                @foreach($department->positions as $position)
                    <div class="col-12 col-md-6">
                        <div class="position-item border rounded p-2 bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $position->name }}</strong>
                                    @if($position->is_manager)
                                        <i class="bi bi-star-fill text-warning ms-1" title="Руководящая должность"></i>
                                    @endif
                                    @if($position->parent_position_id)
                                        <small class="text-muted ms-2">
                                            <i class="bi bi-arrow-up me-1"></i>подчинена
                                        </small>
                                    @endif
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-sm" 
                                            onclick="editPosition({{ $position->id }})"
                                            title="Редактировать">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" 
                                            onclick="deletePosition({{ $position->id }})"
                                            title="Удалить">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center text-muted py-2">
            <small>Нет должностей в отделе</small>
        </div>
    @endif

    <!-- Подотделы -->
    @if($department->children && $department->children->count() > 0)
        <div class="department-children mt-3 ms-3 border-start border-3 ps-3">
            @foreach($department->children as $child)
                @include('settings.partials.department-tree-item', ['department' => $child])
            @endforeach
        </div>
    @endif
</div>