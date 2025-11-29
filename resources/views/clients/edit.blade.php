@extends('layouts.app')

@section('title', 'Редактировать клиента')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Редактирование: {{ $client->display_name }}</h2>
        <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">
            ← Назад к клиенту
        </a>
    </div>

    <form action="{{ route('clients.update', $client) }}" method="POST">
        @csrf
        @method('PUT')

        @include('clients._form')

        <!-- Дополнительные поля, которых нет в _form.blade.php -->
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
                                <option value="lead" {{ $client->status == 'lead' ? 'selected' : '' }}>🎯 Лид</option>
                                <option value="active" {{ $client->status == 'active' ? 'selected' : '' }}>✅ Активный</option>
                                <option value="inactive" {{ $client->status == 'inactive' ? 'selected' : '' }}>❌ Неактивный</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Ответственный</label>
                            <select name="responsible_id" class="form-select">
                                <option value="">— Не выбран —</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                        {{ $client->responsible_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->last_name }} {{ $user->first_name }}
                                    </option>
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
                                <option value="website" {{ $client->source == 'website' ? 'selected' : '' }}>🌐 Сайт</option>
                                <option value="recommendation" {{ $client->source == 'recommendation' ? 'selected' : '' }}>👥 Рекомендация</option>
                                <option value="partner" {{ $client->source == 'partner' ? 'selected' : '' }}>🤝 Партнер</option>
                                <option value="advertising" {{ $client->source == 'advertising' ? 'selected' : '' }}>📢 Реклама</option>
                                <option value="social" {{ $client->source == 'social' ? 'selected' : '' }}>💬 Соцсети</option>
                                <option value="other" {{ $client->source == 'other' ? 'selected' : '' }}>📌 Другое</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Общая выручка</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="total_revenue" class="form-control" 
                                       value="{{ old('total_revenue', $client->total_revenue) }}" placeholder="0.00">
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Теги</label>
                    <input type="text" name="tags" class="form-control" 
                           value="{{ old('tags', $client->tags ? implode(', ', $client->tags) : '') }}"
                           placeholder="Введите теги через запятую">
                    <div class="form-text">Например: vip, строительство, регулярный</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Комментарий</label>
                    <textarea name="notes" class="form-control" rows="4" 
                              placeholder="Дополнительная информация о клиенте...">{{ old('notes', $client->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Отмена
            </a>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check2-circle"></i> Обновить данные клиента
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