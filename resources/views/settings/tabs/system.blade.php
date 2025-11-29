<!-- system.blade.php -->
<div class="p-3">
    <h5 class="mb-3">Системные настройки</h5>
    
    <div class="card">
        <div class="card-body">
            <h6 class="card-title">Информация о системе</h6>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Версия PHP:</strong> {{ phpversion() }}</p>
                    <p><strong>Laravel:</strong> {{ app()->version() }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Окружение:</strong> {{ app()->environment() }}</p>
                    <p><strong>Debug mode:</strong> {{ config('app.debug') ? 'Включен' : 'Выключен' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>