<div class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">📦 Управление бэкапами базы данных</h5>
        <div>
            <a href="{{ route('backup.restore') }}" class="btn btn-warning me-2">
                <i class="bi bi-arrow-clockwise"></i> Восстановить
            </a>
            <form action="{{ route('backup.create') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Создать бэкап
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @php
                $backupFiles = [];
                $backupPath = storage_path('app/backups');
                
                if (file_exists($backupPath)) {
                    $files = scandir($backupPath);
                    foreach ($files as $file) {
                        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                            $filePath = $backupPath . '/' . $file;
                            $backupFiles[] = [
                                'name' => $file,
                                'size' => filesize($filePath),
                                'date' => date('Y-m-d H:i:s', filemtime($filePath)),
                                'path' => $filePath
                            ];
                        }
                    }
                    
                    usort($backupFiles, function($a, $b) {
                        return strtotime($b['date']) - strtotime($a['date']);
                    });
                }
            @endphp

            @if(count($backupFiles) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Имя файла</th>
                                <th>Размер</th>
                                <th>Дата создания</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backupFiles as $backup)
                            <tr>
                                <td>
                                    <i class="bi bi-file-earmark-zip text-primary me-2"></i>
                                    {{ $backup['name'] }}
                                </td>
                                <td>
                                    @if($backup['size'] < 1024)
                                        {{ $backup['size'] }} B
                                    @elseif($backup['size'] < 1048576)
                                        {{ round($backup['size'] / 1024, 1) }} KB
                                    @else
                                        {{ round($backup['size'] / 1024 / 1024, 1) }} MB
                                    @endif
                                </td>
                                <td>{{ $backup['date'] }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('backup.download', $backup['name']) }}" 
                                           class="btn btn-outline-primary" title="Скачать">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <a href="{{ route('backup.restore') }}" 
                                           class="btn btn-outline-warning" title="Восстановить">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </a>
                                        <form action="{{ route('backup.delete', $backup['name']) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" 
                                                    onclick="return confirm('Удалить бэкап {{ $backup['name'] }}?')"
                                                    title="Удалить">
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
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="text-muted mt-3">Бэкапы не найдены</p>
                    <p class="text-muted">Создайте первый бэкап базы данных</p>
                </div>
            @endif
        </div>
    </div>
</div>