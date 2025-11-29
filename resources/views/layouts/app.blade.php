<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CRM' }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Стили CRM -->
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

    @include('partials.sidebar')

    <div class="main-container" id="mainContainer">

        @include('partials.topbar')

        <main class="main-content">
            @yield('content')
        </main>

    </div>

    <!-- Уведомления -->
    @include('components.notifications')

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://unpkg.com/imask"></script>
    <script src="/js/script.js"></script>
    <script src="/js/masks.js"></script>

</body>
</html>