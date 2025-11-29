<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="bi bi-graph-up"></i>
            <span class="logo-text">CRM</span>
        </div>

        <button class="sidebar-collapse-btn" id="sidebarCollapse">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>

    <ul class="sidebar-menu">
        {{-- Динамическое меню на основе прав доступа --}}
        @foreach($userMenu as $section)
            <li>
                <a href="{{ $section->url }}"
                   class="menu-link {{ $section->isActive() ? 'active' : '' }}"
                   title="{{ $section->name }}">
                    <i class="{{ $section->icon }}"></i>
                    <span class="menu-text">{{ $section->name }}</span>
                </a>
            </li>
        @endforeach

        {{-- Резервные пункты меню (если нет данных из БД) --}}
        @if($userMenu->count() === 0)
            {{-- Рабочий стол --}}
            <li>
                <a href="{{ route('dashboard') }}"
                   class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span class="menu-text">Рабочий стол</span>
                </a>
            </li>

            {{-- Клиенты --}}
            <li>
                <a href="{{ route('clients.index') }}"
                   class="menu-link {{ request()->is('clients*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span class="menu-text">Клиенты</span>
                </a>
            </li>

            {{-- Задачи --}}
            <li>
                <a href="#"
                   class="menu-link {{ request()->is('tasks*') ? 'active' : '' }}">
                    <i class="bi bi-list-task"></i>
                    <span class="menu-text">Задачи</span>
                </a>
            </li>

            {{-- Отчёты --}}
            <li>
                <a href="#"
                   class="menu-link {{ request()->is('reports*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i>
                    <span class="menu-text">Отчеты</span>
                </a>
            </li>

            {{-- Настройки --}}
            <li>
                <a href="{{ route('settings.index') }}"
                class="menu-link {{ request()->is('settings*') || request()->is('backup*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    <span class="menu-text">Настройки</span>
                </a>
            </li>
        @endif
    </ul>

    <div class="sidebar-footer">
        {{-- Профиль пользователя --}}
        <div class="user-info">
            <a href="#" class="user-link">
                <div class="user-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="user-details">
                    <span class="username">
                        {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                    </span>
                    <span class="user-role">
                        @if(auth()->user()->role)
                            {{ auth()->user()->role->name }}
                        @else
                            {{ auth()->user()->is_admin ? 'Администратор' : 'Пользователь' }}
                        @endif
                    </span>
                </div>
            </a>
        </div>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST"
              onsubmit="return confirm('Вы уверены, что хотите выйти?');">
            @csrf
            <button class="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                <span class="logout-text">Выйти</span>
            </button>
        </form>
    </div>
</nav>