@extends('layouts.app')

@section('title', 'Добавить клиента')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Добавить клиента</h2>
        <a href="{{ route('clients.index') }}" class="btn btn-secondary">
            ← Назад к списку
        </a>
    </div>

    <form action="{{ route('clients.store') }}" method="POST">
        @csrf

        @include('clients._form')

        <!-- Дополнительные поля -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">📋 Дополнительная информация</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Статус *</label>
                            <select name="status" class="form-select" required>
                                <option value="lead" selected>🎯 Лид</option>
                                <option value="active">✅ Активный</option>
                                <option value="inactive">❌ Неактивный</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Ответственный</label>
                            <select name="responsible_id" class="form-select">
                                <option value="">— Не выбран —</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->last_name }} {{ $user->first_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Источник</label>
                            <select name="source" class="form-select">
                                <option value="">— Не выбран —</option>
                                <option value="website">🌐 Сайт</option>
                                <option value="recommendation">👥 Рекомендация</option>
                                <option value="partner">🤝 Партнер</option>
                                <option value="advertising">📢 Реклама</option>
                                <option value="social">💬 Соцсети</option>
                                <option value="other">📌 Другое</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Общая выручка</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="total_revenue" class="form-control" 
                                       value="{{ old('total_revenue', 0) }}" placeholder="0.00">
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Теги</label>
                    <input type="text" name="tags" class="form-control" 
                           value="{{ old('tags') }}"
                           placeholder="Введите теги через запятую">
                    <div class="form-text">Например: vip, строительство, регулярный</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Комментарий</label>
                    <textarea name="notes" class="form-control" rows="4" 
                              placeholder="Дополнительная информация о клиенте...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Отмена
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2"></i> Создать клиента
            </button>
        </div>
    </form>
</div>

<script>
function toggleBlocks() {
    const type = document.getElementById('clientType').value;

    document.getElementById('block-individual').style.display = type === 'individual' ? 'block' : 'none';
    document.getElementById('block-entrepreneur').style.display = type === 'entrepreneur' ? 'block' : 'none';
    document.getElementById('block-legal').style.display = type === 'legal' ? 'block' : 'none';
}

document.getElementById('clientType').addEventListener('change', toggleBlocks);
document.addEventListener('DOMContentLoaded', toggleBlocks);
</script>
@endsection