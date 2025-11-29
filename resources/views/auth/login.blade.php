<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в CRM</title>

    <!-- Стили твоей CRM -->
    <link rel="stylesheet" href="/css/login.css">

    <!-- Bootstrap (если нужно, можно отключить) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="login-form">
        <div class="header">Вход в CRM систему</div>

        <div class="form-content">
            
            {{-- Ошибки Breeze --}}
            @if ($errors->any())
                <div class="error-message">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Сообщение об успешном logout --}}
            @if (session('status'))
                <div class="success-message">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <input 
                    type="text"
                    name="email"
                    id="email"
                    placeholder="Имя пользователя или Email"
                    required
                    maxlength="100"
                    value="{{ old('email') }}"
                >

                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Пароль"
                    required
                    minlength="6"
                >

                <button type="submit">Войти</button>
            </form>

        </div>
    </div>

</body>
</html>
