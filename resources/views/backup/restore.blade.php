@extends('layouts.app')

@section('title', 'Восстановление из бэкапа')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">🔄 Восстановление базы данных</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <form action="{{ route('backup.restore.submit') }}" method="POST" id="restoreForm">
                                @csrf
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Выберите файл бэкапа *</label>
                                    <select name="backup_file" class="form-select" required>
                                        <option value="">-- Выберите файл бэкапа --</option>
                                        @php
                                            $backupFiles = [];
                                            $backupPath = storage_path('app/backups');
                                            if (file_exists($backupPath)) {
                                                $files = scandir($backupPath);
                                                foreach ($files as $file) {
                                                    if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                                                        $backupFiles[] = $file;
                                                    }
                                                }
                                            }
                                        @endphp
                                        @foreach($backupFiles as $file)
                                            <option value="{{ $file }}">{{ $file }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <div class="alert alert-warning">
                                        <h6 class="alert-heading">⚠️ Внимание!</h6>
                                        <p class="mb-0">Восстановление из бэкапа полностью заменит текущую базу данных.</p>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="confirm_database_overwrite" 
                                               id="confirm_database_overwrite" required>
                                        <label class="form-check-label fw-bold text-danger" for="confirm_database_overwrite">
                                            Я понимаю, что текущая база данных будет полностью перезаписана
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('settings.index') }}#backup" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Назад
                                    </a>
                                    <button type="submit" class="btn btn-warning" id="restoreButton">
                                        <i class="bi bi-arrow-clockwise"></i> Восстановить
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('restoreForm').addEventListener('submit', function(e) {
        const confirmOverwrite = document.getElementById('confirm_database_overwrite').checked;
        
        if (!confirmOverwrite) {
            e.preventDefault();
            alert('Пожалуйста, подтвердите что понимаете последствия восстановления');
            return;
        }
        
        const button = document.getElementById('restoreButton');
        button.disabled = true;
        button.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Восстановление...';
        
        if (!confirm('⚠️ ВНИМАНИЕ! Это действие полностью перезапишет текущую базу данных. Вы уверены?')) {
            e.preventDefault();
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Восстановить';
        }
    });
});
</script>
@endsection