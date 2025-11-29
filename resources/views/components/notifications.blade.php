@if(session('success') || session('error') || session('warning') || session('info') || $errors->any())
<div class="notifications-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    
    {{-- Success --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show notification-item" role="alert" data-auto-hide="5000">
        <div class="d-flex">
            <div class="flex-shrink-0">
                <i class="bi bi-check-circle-fill fs-4"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="alert-heading mb-1">Успешно!</h6>
                <div class="small">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    {{-- Error --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show notification-item" role="alert" data-auto-hide="8000">
        <div class="d-flex">
            <div class="flex-shrink-0">
                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="alert-heading mb-1">Ошибка!</h6>
                <div class="small">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    {{-- Warning --}}
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show notification-item" role="alert" data-auto-hide="6000">
        <div class="d-flex">
            <div class="flex-shrink-0">
                <i class="bi bi-exclamation-circle-fill fs-4"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="alert-heading mb-1">Внимание!</h6>
                <div class="small">{{ session('warning') }}</div>
            </div>
            <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    {{-- Info --}}
    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show notification-item" role="alert" data-auto-hide="4000">
        <div class="d-flex">
            <div class="flex-shrink-0">
                <i class="bi bi-info-circle-fill fs-4"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="alert-heading mb-1">Информация</h6>
                <div class="small">{{ session('info') }}</div>
            </div>
            <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show notification-item" role="alert" data-auto-hide="10000">
        <div class="d-flex">
            <div class="flex-shrink-0">
                <i class="bi bi-x-circle-fill fs-4"></i>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="alert-heading mb-2">Ошибки валидации</h6>
                <div class="small">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

</div>

<style>
.notifications-container {
    max-width: 450px;
    width: 100%;
}

@media (max-width: 576px) {
    .notifications-container {
        max-width: calc(100vw - 20px);
        left: 10px;
        right: 10px;
        top: 10px;
    }
}

.notification-item {
    margin-bottom: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    border: none;
    border-radius: 12px;
    backdrop-filter: blur(10px);
    border-left: 4px solid transparent;
}

.alert-success {
    border-left-color: #198754;
}

.alert-danger {
    border-left-color: #dc3545;
}

.alert-warning {
    border-left-color: #ffc107;
}

.alert-info {
    border-left-color: #0dcaf0;
}

.alert {
    animation: slideInRight 0.4s ease-out;
}

.alert.fade {
    animation: slideOutRight 0.3s ease-in;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.alert-heading {
    font-weight: 600;
    font-size: 0.95rem;
}

/* Индикатор времени */
.notification-timer {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: currentColor;
    opacity: 0.3;
    animation: progressBar linear forwards;
}

@keyframes progressBar {
    from { width: 100%; }
    to { width: 0%; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Автоматическое скрытие уведомлений
    document.querySelectorAll('[data-auto-hide]').forEach(function(alert) {
        const delay = parseInt(alert.getAttribute('data-auto-hide'));
        
        // Добавляем индикатор времени
        const timer = document.createElement('div');
        timer.className = 'notification-timer';
        timer.style.animationDuration = delay + 'ms';
        alert.appendChild(timer);
        
        setTimeout(function() {
            if (alert.parentNode) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, delay);
    });

    // Дополнительные функции
    const notificationsContainer = document.querySelector('.notifications-container');
    
    if (notificationsContainer) {
        // Закрыть все уведомления
        window.closeAllNotifications = function() {
            document.querySelectorAll('.notification-item').forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        };

        // Добавить обработчик для Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.closeAllNotifications();
            }
        });
    }
});
</script>
@endif