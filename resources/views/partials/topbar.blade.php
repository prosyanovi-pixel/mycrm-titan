<div class="main-header">
    <div class="header-content">
        <button class="menu-toggle" id="menuToggle">
            <i class="bi bi-list"></i>
        </button>
        
        <div class="header-title">
            <h1>{{ App\Models\MenuSection::getCurrentPageTitle() }}</h1>
        </div>

        <div class="header-actions">
            <button class="header-btn" title="Уведомления">
                <i class="bi bi-bell"></i>
                <span class="notification-badge">3</span>
            </button>
            
            <button class="header-btn" title="Поиск">
                <i class="bi bi-search"></i>
            </button>
            
            <button class="header-btn" title="Настройки">
                <i class="bi bi-gear"></i>
            </button>
        </div>
    </div>
</div>