<div class="p-3">
    <h3 class="mb-3">{{ $client->display_name }}</h3>

    <p><strong>Тип:</strong>
        @if($client->type === 'legal') Юридическое лицо
        @elseif($client->type === 'entrepreneur') Индивидуальный предприниматель
        @else Физическое лицо @endif
    </p>

    <p><strong>Статус:</strong>
        @if($client->status === 'active')
            <span class="badge bg-success">Активный</span>
        @elseif($client->status === 'lead')
            <span class="badge bg-warning">Лид</span>
        @else
            <span class="badge bg-dark">Неактивный</span>
        @endif
    </p>

    <p><strong>Ответственный:</strong> 
        {{ $client->responsible->name ?? '—' }}
    </p>

    @if($client->email)
        <p><strong>Email:</strong> {{ $client->email }}</p>
    @endif

    @if($client->phone)
        <p><strong>Телефон:</strong> {{ $client->phone }}</p>
    @endif

    @if($client->address)
        <p><strong>Адрес:</strong> {{ $client->address }}</p>
    @endif

    <div class="mt-3">
        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-primary">Редактировать</a>
        <a href="{{ route('clients.show', $client->id) }}" class="btn btn-secondary">Подробнее</a>
    </div>
</div>
