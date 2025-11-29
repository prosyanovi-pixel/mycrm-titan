@extends('layouts.app')

@section('title', 'Настройки системы')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">⚙️ Настройки системы</h4>
            </div>
        </div>
    </div>

    <!-- Навигация по вкладкам -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-bordered">
                        <li class="nav-item">
                            <a href="#general" data-bs-toggle="tab" aria-expanded="false" 
                               class="nav-link {{ $tab === 'general' ? 'active' : '' }}">
                                <i class="bi bi-sliders me-1"></i>
                                <span class="d-none d-md-inline-block">Основные</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#users-roles" data-bs-toggle="tab" aria-expanded="false" 
                               class="nav-link {{ $tab === 'users-roles' ? 'active' : '' }}">
                                <i class="bi bi-people me-1"></i>
                                <span class="d-none d-md-inline-block">Пользователи и роли</span>
                            </a>
                        </li>
                        @if(auth()->user()->is_admin)
                        <li class="nav-item">
                            <a href="#backup" data-bs-toggle="tab" aria-expanded="false" 
                               class="nav-link {{ $tab === 'backup' ? 'active' : '' }}">
                                <i class="bi bi-hdd-stack me-1"></i>
                                <span class="d-none d-md-inline-block">Бэкапы БД</span>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a href="#system" data-bs-toggle="tab" aria-expanded="false" 
                               class="nav-link {{ $tab === 'system' ? 'active' : '' }}">
                                <i class="bi bi-cpu me-1"></i>
                                <span class="d-none d-md-inline-block">Системные</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Основные настройки -->
                        <div class="tab-pane {{ $tab === 'general' ? 'show active' : '' }}" id="general">
                            @include('settings.tabs.general')
                        </div>

                        <!-- Пользователи и роли -->
                        <div class="tab-pane {{ $tab === 'users-roles' ? 'show active' : '' }}" id="users-roles">
                            @include('settings.tabs.users-roles', compact('users', 'roles', 'departments', 'positions', 'modules'))
                        </div>

                        <!-- Бэкапы БД -->
                        @if(auth()->user()->is_admin)
                        <div class="tab-pane {{ $tab === 'backup' ? 'show active' : '' }}" id="backup">
                            @include('settings.tabs.backup')
                        </div>
                        @endif

                        <!-- Системные настройки -->
                        <div class="tab-pane {{ $tab === 'system' ? 'show active' : '' }}" id="system">
                            @include('settings.tabs.system')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Функция для активации вкладки
    function activateTab(tabName) {
        const tabElement = document.querySelector(`a[href="#${tabName}"]`);
        if (tabElement) {
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    }

    // Восстановление активной вкладки из URL параметра
    const urlParams = new URLSearchParams(window.location.search);
    const urlTab = urlParams.get('tab');
    
    if (urlTab && urlTab !== '{{ $tab }}') {
        // Если в URL есть параметр tab и он отличается от текущего, активируем его
        activateTab(urlTab);
    }

    // Обновление URL при переключении вкладок
    const tabEls = document.querySelectorAll('a[data-bs-toggle="tab"]');
    tabEls.forEach(tabEl => {
        tabEl.addEventListener('shown.bs.tab', function (e) {
            const hash = e.target.getAttribute('href');
            const tabName = hash.replace('#', '');
            
            // Обновляем URL без перезагрузки страницы
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('tab', tabName);
            history.replaceState(null, '', newUrl);
        });
    });

    // Сохранение активной вкладки перед отправкой форм
    document.addEventListener('submit', function(e) {
        const activeTab = document.querySelector('.nav-link.active');
        if (activeTab) {
            const tabHref = activeTab.getAttribute('href');
            const tabName = tabHref.replace('#', '');
            
            // Добавляем скрытое поле с активной вкладкой в форму
            let tabInput = e.target.querySelector('input[name="_active_tab"]');
            if (!tabInput) {
                tabInput = document.createElement('input');
                tabInput.type = 'hidden';
                tabInput.name = '_active_tab';
                e.target.appendChild(tabInput);
            }
            tabInput.value = tabName;
        }
    });

    // Восстановление вкладки после AJAX операций (если нужно)
    window.restoreActiveTab = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const urlTab = urlParams.get('tab');
        if (urlTab) {
            activateTab(urlTab);
        }
    };
});

// Функция для обновления страницы с сохранением вкладки
function reloadWithTab(tabName) {
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.location.href = url.toString();
}

// Переопределяем location.reload для сохранения вкладки
const originalReload = window.location.reload;
window.location.reload = function() {
    const activeTab = document.querySelector('.nav-link.active');
    if (activeTab) {
        const tabHref = activeTab.getAttribute('href');
        const tabName = tabHref.replace('#', '');
        reloadWithTab(tabName);
    } else {
        originalReload.call(window.location);
    }
};
</script>
@endpush