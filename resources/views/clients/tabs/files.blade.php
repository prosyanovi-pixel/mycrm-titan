<div class="files-tab">
    <!-- Заголовок -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">📁 Файлы клиента</h4>
    </div>
    
    <!-- Статистика и кнопка в одной линии -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <!-- Статистика -->
        <div class="d-flex flex-wrap gap-2">
            @php
                $allFiles = $client->files;
                $documentsCount = $allFiles->where('type', 'document')->count();
                $imagesCount = $allFiles->where('type', 'image')->count();
                $archivesCount = $allFiles->where('type', 'archive')->count();
                $othersCount = $allFiles->where('type', 'other')->count();
                $totalSizeMB = round($allFiles->sum('size') / 1024 / 1024, 1);
                
                // Получаем текущие фильтры
                $currentType = request('type', 'all');
                $currentPerPage = request('per_page', 10);
                $currentSearch = request('search', '');
                $currentTags = request('tags', '');
                
                // Фильтруем файлы на основе параметров
                $filteredFiles = $allFiles;
                
                if ($currentType != 'all') {
                    $filteredFiles = $filteredFiles->where('type', $currentType);
                }
                
                 if ($currentSearch) {
                    $filteredFiles = $filteredFiles->filter(function($file) use ($currentSearch) {
                        $search = strtolower($currentSearch);
                        $fileName = strtolower($file->custom_name ?: $file->original_name);
                        $fileDescription = strtolower($file->description ?? '');
                        $fileOriginalName = strtolower($file->original_name);
                        
                        // Поиск в названии файла (кастомном и оригинальном)
                        if (str_contains($fileName, $search) || str_contains($fileOriginalName, $search)) {
                            return true;
                        }
                        
                        // Поиск в описании
                        if (str_contains($fileDescription, $search)) {
                            return true;
                        }
                        
                        // Поиск в тегах
                        $tags = $file->tags ?: [];
                        foreach ($tags as $tag) {
                            if (str_contains(strtolower($tag), $search)) {
                                return true;
                            }
                        }
                        
                        return false;
                    });
                }
                
                if ($currentTags) {
                    $filteredFiles = $filteredFiles->filter(function($file) use ($currentTags) {
                        $tags = $file->tags ?: [];
                        $searchTags = array_map('strtolower', array_map('trim', explode(',', $currentTags)));
                        
                        foreach ($searchTags as $searchTag) {
                            foreach ($tags as $tag) {
                                if (str_contains(strtolower($tag), $searchTag)) {
                                    return true;
                                }
                            }
                        }
                        return false;
                    });
                }
                
                // Применяем пагинацию
                $perPage = (int)$currentPerPage;
                $currentPage = request('page', 1);
                $paginatedFiles = new \Illuminate\Pagination\LengthAwarePaginator(
                    $filteredFiles->forPage($currentPage, $perPage),
                    $filteredFiles->count(),
                    $perPage,
                    $currentPage,
                    ['path' => request()->url(), 'query' => request()->query()]
                );
            @endphp
            
            <div class="card bg-light stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $allFiles->count() }}</h5>
                    <small class="text-muted">Всего</small>
                </div>
            </div>
            <div class="card bg-success text-white stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $documentsCount }}</h5>
                    <small>Документы</small>
                </div>
            </div>
            <div class="card bg-warning stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $imagesCount }}</h5>
                    <small>Изображения</small>
                </div>
            </div>
            <div class="card bg-info text-white stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $archivesCount }}</h5>
                    <small>Архивы</small>
                </div>
            </div>
            <div class="card bg-primary text-white stat-card">
                <div class="card-body text-center p-2">
                    <h5 class="mb-0">{{ $totalSizeMB }}</h5>
                    <small>МБ всего</small>
                </div>
            </div>
        </div>
        
        <!-- Кнопка загрузки файла -->
        <button class="btn btn-primary btn-lg" 
                data-bs-toggle="modal" 
                data-bs-target="#fileModal">
            <i class="bi bi-plus-circle"></i> Загрузить файл
        </button>
    </div>

       <!-- Поиск и фильтры -->
        <div class="card mb-3">
            <div class="card-body">
                <form id="filterForm" method="GET" action="{{ route('clients.show', $client) }}">
                    <!-- Скрытые поля для сохранения текущих параметров -->
                    <input type="hidden" name="tab" value="files">
                    
                    <!-- Строка поиска -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control search-input" 
                                    placeholder="Поиск по имени файла, описанию или тегам..."
                                    value="{{ $currentSearch }}">
                                <button class="btn btn-outline-secondary clear-search" type="button">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tags"></i></span>
                                <input type="text" name="tags" class="form-control tag-search" 
                                    placeholder="Поиск по тегам..."
                                    value="{{ $currentTags }}">
                            </div>
                        </div>
                    </div>

                    <!-- Основные фильтры по типу и настройка отображения в одной линии -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                        <!-- Фильтры по типу -->
                        <div class="filter-buttons d-flex flex-wrap gap-1">
                            <button type="button" class="btn btn-outline-secondary btn-sm {{ $currentType == 'all' ? 'active' : '' }}" 
                                    data-filter="all">
                                Все ({{ $allFiles->count() }})
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm {{ $currentType == 'document' ? 'active' : '' }}" 
                                    data-filter="document">
                                📄 Документы ({{ $documentsCount }})
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm {{ $currentType == 'image' ? 'active' : '' }}" 
                                    data-filter="image">
                                🖼️ Изображения ({{ $imagesCount }})
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm {{ $currentType == 'archive' ? 'active' : '' }}" 
                                    data-filter="archive">
                                📦 Архивы ({{ $archivesCount }})
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm {{ $currentType == 'other' ? 'active' : '' }}" 
                                    data-filter="other">
                                📎 Прочие ({{ $othersCount }})
                            </button>
                        </div>
                        
                        <!-- Настройка количества на странице -->
                        <div class="d-flex align-items-center ms-auto">
                            <small class="text-muted me-2">Показывать по:</small>
                            <select name="per_page" class="form-select form-select-sm per-page-selector" style="width: auto;">
                                <option value="5" {{ $currentPerPage == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $currentPerPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $currentPerPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $currentPerPage == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Скрытое поле для типа -->
                    <input type="hidden" name="type" id="typeFilter" value="{{ $currentType }}">
                </form>
            </div>
        </div>
   <!--// индикатор загрузки при поиске -->
    <div class="search-loading" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Поиск...</span>
        </div>
    </div>

    <!-- Сообщение когда файлы не найдены -->
    @if($allFiles->count() > 0 && $filteredFiles->count() == 0)
        <div class="alert alert-info">
            <div class="text-center py-4">
                <i class="bi bi-search" style="font-size: 2rem;"></i>
                <p class="mt-2 mb-0">Файлы по вашему запросу не найдены</p>
                <small class="text-muted">Попробуйте изменить параметры поиска или фильтры</small>
            </div>
        </div>
    @endif

    <!-- Таблица файлов -->
    @if($filteredFiles->count() > 0)
        <div class="table-responsive files-table">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50"></th>
                        <th>Имя файла и описание</th>
                        <th>Теги</th>
                        <th>Тип</th>
                        <th>Размер</th>
                        <th>Загрузил</th>
                        <th>Дата загрузки</th>
                        <th width="150" class="text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paginatedFiles as $file)
                    @php
                        $fileType = $file->type;
                        $badgeColors = [
                            'document' => 'success',
                            'image' => 'warning', 
                            'archive' => 'info',
                            'other' => 'secondary'
                        ];
                        
                        $badgeColor = $badgeColors[$fileType] ?? 'secondary';
                        
                        $typeLabels = [
                            'document' => '📄 Документ',
                            'image' => '🖼️ Изображение', 
                            'archive' => '📦 Архив',
                            'other' => '📎 Прочее'
                        ];
                        $typeLabel = $typeLabels[$fileType] ?? '📎 Прочее';

                        $tags = $file->tags ?: [];
                    @endphp
                    <tr class="file-row" 
                        data-id="{{ $file->id }}"
                        data-type="{{ $fileType }}" 
                        data-size="{{ $file->size }}"
                        data-date="{{ $file->created_at->format('Y-m-d') }}"
                        data-name="{{ strtolower($file->custom_name ?: $file->original_name) }}"
                        data-description="{{ strtolower($file->description ?? '') }}"
                        data-tags="{{ implode(' ', $tags) }}"
                        data-original-name="{{ strtolower($file->original_name) }}"
                        data-custom-name="{{ $file->custom_name }}"
                        data-file-description="{{ $file->description }}"
                        data-file-tags="{{ implode(',', $tags) }}">
                        <td class="text-center">
                            @if($fileType === 'document')
                                <i class="bi bi-file-text text-primary" style="font-size: 1.2rem;"></i>
                            @elseif($fileType === 'image')
                                <i class="bi bi-image text-success" style="font-size: 1.2rem;"></i>
                            @elseif($fileType === 'archive')
                                <i class="bi bi-file-zip text-warning" style="font-size: 1.2rem;"></i>
                            @else
                                <i class="bi bi-file-earmark text-secondary" style="font-size: 1.2rem;"></i>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <strong class="file-name d-block" title="{{ $file->custom_name ?: $file->original_name }}">
                                        {{ $file->custom_name ?: $file->original_name }}
                                    </strong>
                                    
                                    @if($file->custom_name && $file->custom_name != $file->original_name)
                                        <small class="text-muted d-block">
                                            Оригинал: {{ $file->original_name }}
                                        </small>
                                    @endif
                                    
                                    @if($file->description)
                                        <small class="text-muted file-description d-block mt-1">
                                            📝 {{ $file->description }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td>
                            @if(!empty($tags))
                                <div class="file-tags">
                                    @foreach(array_slice($tags, 0, 3) as $tag)
                                        <span class="badge bg-light text-dark border me-1 mb-1 tag-badge">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                    @if(count($tags) > 3)
                                        <span class="badge bg-secondary">+{{ count($tags) - 3 }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-{{ $badgeColor }}">
                                {{ $typeLabel }}
                            </span>
                        </td>

                        <td>
                            <strong>
                                @if($file->size < 1024)
                                    {{ $file->size }} B
                                @elseif($file->size < 1048576)
                                    {{ round($file->size / 1024, 1) }} KB
                                @else
                                    {{ round($file->size / 1024 / 1024, 1) }} MB
                                @endif
                            </strong>
                        </td>

                        <td>
                            @if($file->user)
                                <small>{{ $file->user->name }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>
                            <small>{{ $file->created_at->format('d.m.Y') }}</small>
                            <br>
                            <small class="text-muted">{{ $file->created_at->format('H:i') }}</small>
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                <a href="{{ route('clients.files.download', [$client, $file]) }}" 
                                   class="btn btn-outline-primary btn-sm"
                                   title="Скачать"
                                   style="min-width: 36px;">
                                    <i class="bi bi-download"></i>
                                </a>

                                <button class="btn btn-outline-warning btn-sm edit-file" 
                                        data-file-id="{{ $file->id }}"
                                        title="Редактировать"
                                        style="min-width: 36px;">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <button class="btn btn-outline-danger btn-sm delete-file" 
                                        data-file-id="{{ $file->id }}"
                                        data-file-name="{{ $file->custom_name ?: $file->original_name }}"
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

        <!-- Пагинация -->
        @if($paginatedFiles->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-3">
                <!-- Информация о странице -->
                <div class="text-muted small">
                    Показано с {{ $paginatedFiles->firstItem() }} по {{ $paginatedFiles->lastItem() }} из {{ $paginatedFiles->total() }} файлов
                </div>

                <!-- Пагинация -->
                <nav>
                    {{ $paginatedFiles->appends(request()->query())->links() }}
                </nav>
            </div>
        @endif
    @elseif($allFiles->count() == 0)
        <div class="text-center py-5 no-files-empty">
            <i class="bi bi-folder-x" style="font-size: 3rem; color: #6c757d;"></i>
            <p class="text-muted mt-3">Файлов пока нет. Загрузите первый файл!</p>
        </div>
    @endif
</div>

<!-- Модальное окно загрузки файла -->
<div class="modal fade" id="fileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
           <form action="{{ route('clients.files.store', $client) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">📁 Загрузка файла</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Файл -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Выберите файл *</label>
                        <input type="file" name="file" class="form-control" required 
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.rar"
                               id="fileInput">
                        <div class="form-text">
                            Поддерживаемые форматы: PDF, DOC, XLS, JPG, PNG, ZIP (макс. 20MB)
                        </div>
                        <div class="invalid-feedback" id="fileError"></div>
                    </div>

                    <!-- Имя файла -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Имя файла</label>
                        <input type="text" name="custom_name" class="form-control" 
                               value="{{ old('custom_name') }}"
                               placeholder="Альтернативное имя файла (необязательно)">
                        <div class="form-text">
                            Если не указано, будет использовано оригинальное имя файла
                        </div>
                    </div>

                    <!-- Описание -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Описание файла</label>
                        <textarea name="description" class="form-control" rows="3" 
                                  placeholder="Введите описание файла...">{{ old('description') }}</textarea>
                        <div class="form-text">
                            Необязательное описание для лучшего понимания содержимого
                        </div>
                    </div>

                    <!-- Теги -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Теги</label>
                        <input type="text" name="tags" class="form-control" 
                               value="{{ old('tags') }}"
                               placeholder="Введите теги через запятую">
                        <div class="form-text">
                            Например: договор, скан, отчет, финансовый, важный
                        </div>
                    </div>

                    <!-- Предпросмотр выбранного файла -->
                    <div class="mb-3 file-preview" style="display: none;">
                        <label class="form-label fw-bold">Предпросмотр:</label>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark text-primary me-3" style="font-size: 2rem;"></i>
                                    <div>
                                        <strong class="file-name-preview"></strong>
                                        <div class="text-muted small file-size-preview"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary" id="uploadButton">
                        <i class="bi bi-upload me-2"></i>Загрузить файл
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно редактирования файла -->
<div class="modal fade" id="editFileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editFileForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Редактирование файла</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Информация о файле -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark text-primary me-3" style="font-size: 1.5rem;"></i>
                                <div>
                                    <strong id="editOriginalName" class="d-block"></strong>
                                    <small class="text-muted" id="editFileInfo"></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Имя файла -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Имя файла *</label>
                        <input type="text" name="custom_name" id="editCustomName" class="form-control" required
                               placeholder="Введите имя файла">
                    </div>

                    <!-- Описание -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Описание файла</label>
                        <textarea name="description" id="editDescription" class="form-control" rows="3" 
                                  placeholder="Введите описание файла..."></textarea>
                    </div>

                    <!-- Теги -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Теги</label>
                        <input type="text" name="tags" id="editTags" class="form-control" 
                               placeholder="Введите теги через запятую">
                        <div class="form-text">
                            Например: договор, скан, отчет, финансовый, важный
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div class="modal fade" id="deleteFileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">❌ Удаление файла</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить файл "<strong id="deleteFileName"></strong>"?</p>
                <p class="text-muted small">Это действие нельзя отменить. Файл будет удален безвозвратно.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form id="deleteFileForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Удалить файл</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Фильтрация файлов по типу
    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            document.getElementById('typeFilter').value = filter;
            document.getElementById('filterForm').submit();
        });
    });

    // Авто-отправка формы при изменении количества на странице
    document.querySelector('.per-page-selector').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    // Очистка поиска
    document.querySelector('.clear-search').addEventListener('click', function() {
        document.querySelector('.search-input').value = '';
        document.getElementById('filterForm').submit();
    });

    // Быстрый поиск с задержкой
    const searchInput = document.querySelector('.search-input');
    const tagSearchInput = document.querySelector('.tag-search');
    let searchTimeout;

    function submitFilterForm() {
        // Показываем индикатор загрузки
    const loadingIndicator = document.querySelector('.search-loading');
    if (loadingIndicator) {
        loadingIndicator.style.display = 'block';
    }
        document.getElementById('filterForm').submit();
    }

    // Поиск по названию и описанию с задержкой
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(submitFilterForm, 800); // 800ms задержка
    });

    // Поиск по тегам с задержкой
    tagSearchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(submitFilterForm, 800); // 800ms задержка
    });

    // Enter в поле поиска отправляет форму сразу
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimeout);
            submitFilterForm();
        }
    });

    tagSearchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimeout);
            submitFilterForm();
        }
    });

    // Редактирование файла
    const editModal = new bootstrap.Modal(document.getElementById('editFileModal'));
    document.querySelectorAll('.edit-file').forEach(btn => {
        btn.addEventListener('click', function() {
            const fileId = this.getAttribute('data-file-id');
            const row = this.closest('.file-row');
            
            // Заполняем форму данными
            document.getElementById('editOriginalName').textContent = row.getAttribute('data-original-name');
            document.getElementById('editCustomName').value = row.getAttribute('data-custom-name') || '';
            document.getElementById('editDescription').value = row.getAttribute('data-file-description') || '';
            document.getElementById('editTags').value = row.getAttribute('data-file-tags') || '';
            
            // Информация о файле
            const fileSize = parseInt(row.getAttribute('data-size'));
            let sizeText = '';
            if (fileSize < 1024) {
                sizeText = fileSize + ' B';
            } else if (fileSize < 1048576) {
                sizeText = (fileSize / 1024).toFixed(1) + ' KB';
            } else {
                sizeText = (fileSize / 1048576).toFixed(1) + ' MB';
            }
            
            const fileDate = new Date(row.getAttribute('data-date'));
            document.getElementById('editFileInfo').textContent = 
                `${sizeText} • Загружен ${fileDate.toLocaleDateString('ru-RU')}`;
            
            // Устанавливаем action формы
            document.getElementById('editFileForm').action = `/clients/{{ $client->id }}/files/${fileId}`;
            
            editModal.show();
        });
    });

    // Удаление файла
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteFileModal'));
    document.querySelectorAll('.delete-file').forEach(btn => {
        btn.addEventListener('click', function() {
            const fileId = this.getAttribute('data-file-id');
            const fileName = this.getAttribute('data-file-name');
            const form = document.getElementById('deleteFileForm');
            
            form.action = `/clients/{{ $client->id }}/files/${fileId}`;
            document.getElementById('deleteFileName').textContent = fileName;
            
            deleteModal.show();
        });
    });

    // Валидация размера файла при загрузке
    const fileInput = document.getElementById('fileInput');
    const uploadForm = document.getElementById('uploadForm');
    const fileError = document.getElementById('fileError');
    const uploadButton = document.getElementById('uploadButton');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const maxSize = 20 * 1024 * 1024; // 20MB в байтах
                
                if (file.size > maxSize) {
                    fileError.textContent = 'Размер файла превышает 20MB';
                    fileInput.classList.add('is-invalid');
                    uploadButton.disabled = true;
                } else {
                    fileError.textContent = '';
                    fileInput.classList.remove('is-invalid');
                    uploadButton.disabled = false;
                }
            }
        });
    }

    // Инициализация тултипов
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Предпросмотр выбранного файла
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.querySelector('input[name="file"]');
    const filePreview = document.querySelector('.file-preview');
    const fileNamePreview = document.querySelector('.file-name-preview');
    const fileSizePreview = document.querySelector('.file-size-preview');

    if (fileInput && filePreview) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                filePreview.style.display = 'block';
                fileNamePreview.textContent = file.name;
                
                const fileSize = file.size;
                let sizeText = '';
                if (fileSize < 1024) {
                    sizeText = fileSize + ' B';
                } else if (fileSize < 1048576) {
                    sizeText = (fileSize / 1024).toFixed(1) + ' KB';
                } else {
                    sizeText = (fileSize / 1048576).toFixed(1) + ' MB';
                }
                fileSizePreview.textContent = 'Размер: ' + sizeText;
                
                const fileIcon = filePreview.querySelector('i');
                const extension = file.name.split('.').pop().toLowerCase();
                
                const iconMap = {
                    'pdf': 'bi-file-pdf text-danger',
                    'doc': 'bi-file-word text-primary',
                    'docx': 'bi-file-word text-primary',
                    'xls': 'bi-file-excel text-success',
                    'xlsx': 'bi-file-excel text-success',
                    'jpg': 'bi-file-image text-warning',
                    'jpeg': 'bi-file-image text-warning',
                    'png': 'bi-file-image text-warning',
                    'gif': 'bi-file-image text-warning',
                    'zip': 'bi-file-zip text-secondary',
                    'rar': 'bi-file-zip text-secondary'
                };
                
                fileIcon.className = 'bi ' + (iconMap[extension] || 'bi-file-earmark text-primary') + ' me-3';
                fileIcon.style.fontSize = '2rem';
                
            } else {
                filePreview.style.display = 'none';
            }
        });

        // Очистка предпросмотра при закрытии модального окна
        const modal = document.getElementById('fileModal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                filePreview.style.display = 'none';
                fileInput.value = '';
            });
        }
    }
});
</script>

<style>
/* Стили остаются такими же как в предыдущей версии */
.files-tab {
    padding: 20px 0;
}

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

.btn.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    white-space: nowrap;
}

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

.d-flex.gap-1 {
    gap: 0.25rem !important;
}

.btn-sm {
    min-width: 36px;
    padding: 0.25rem 0.5rem;
}

.file-name {
    cursor: help;
}

.file-tags {
    max-width: 200px;
}

.tag-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}

/* Пагинация */
.pagination {
    margin-bottom: 0;
}

.page-link {
    border-radius: 0.375rem;
    margin: 0 0.1rem;
}

@media (max-width: 768px) {
    .files-tab .d-flex.flex-wrap {
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
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .file-tags {
        max-width: 120px;
    }
    
    .d-flex.justify-content-between.flex-wrap {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}

/* Стили для новой компоновки фильтров */
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

/* Адаптивность для мобильных устройств */
@media (max-width: 768px) {
    .d-flex.justify-content-between.flex-wrap {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem !important;
    }
    
    .d-flex.justify-content-between.flex-wrap .ms-auto {
        margin-left: 0 !important;
        margin-top: 0.5rem;
    }
    
    .filter-buttons {
        justify-content: center;
        width: 100%;
    }
    
    .filter-buttons .btn {
        flex: 1;
        min-width: auto;
        text-align: center;
    }
}
</style>