<div wire:ignore.self class="modal fade" id="taskFormModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form wire:submit.prevent="save">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $taskId ? 'Редактировать задачу' : 'Создать задачу' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-2">
                        <label class="form-label">Название</label>
                        <input type="text" class="form-control" wire:model.defer="title">
                        @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Описание</label>
                        <textarea class="form-control" wire:model.defer="description"></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Статус</label>
                        <select class="form-select" wire:model="status">
                            <option value="open">Открыта</option>
                            <option value="in_progress">В работе</option>
                            <option value="done">Готово</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Приоритет</label>
                        <select class="form-select" wire:model="priority">
                            <option value="low">Низкий</option>
                            <option value="medium">Средний</option>
                            <option value="high">Высокий</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Крайний срок</label>
                        <input type="datetime-local" class="form-control" wire:model="due_date">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button class="btn btn-primary">Сохранить</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
window.addEventListener('showTaskForm', () => {
    new bootstrap.Modal('#taskFormModal').show();
});
window.addEventListener('hideTaskForm', () => {
    bootstrap.Modal.getInstance('#taskFormModal').hide();
});
</script>
