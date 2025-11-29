<div class="card shadow-sm">
    <div class="card-body">

        {{-- Тип клиента --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Тип клиента *</label>
            <select name="type" id="clientType" class="form-select" required>
                <option value="individual" {{ old('type', $client->type ?? '') == 'individual' ? 'selected' : '' }}>
                    Физическое лицо
                </option>
                <option value="entrepreneur" {{ old('type', $client->type ?? '') == 'entrepreneur' ? 'selected' : '' }}>
                    Индивидуальный предприниматель
                </option>
                <option value="legal" {{ old('type', $client->type ?? '') == 'legal' ? 'selected' : '' }}>
                    Юридическое лицо
                </option>
            </select>
        </div>

        {{-- ОБЩЕЕ поле ИНН для всех типов --}}
        <div class="mb-3">
            <label class="form-label">ИНН</label>
            <input type="text" name="inn" class="form-control"
                value="{{ old('inn', $client->inn ?? '') }}"
                placeholder="Введите ИНН (обязателен для ЮЛ и ИП)">
        </div>

        {{-- Блок ФЛ + ИП --}}
        <div id="block-fio" class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Фамилия *</label>
                <input type="text" name="last_name" class="form-control" required
                       value="{{ old('last_name', $client->last_name ?? '') }}"
                       placeholder="Обязательно для ФЛ и ИП">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Имя *</label>
                <input type="text" name="first_name" class="form-control" required
                       value="{{ old('first_name', $client->first_name ?? '') }}"
                       placeholder="Обязательно для ФЛ и ИП">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Отчество</label>
                <input type="text" name="middle_name" class="form-control"
                       value="{{ old('middle_name', $client->middle_name ?? '') }}"
                       placeholder="Необязательно">
            </div>
        </div>

        {{-- Блок ИП --}}
        <div id="block-ip" style="display:none">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ОГРНИП</label>
                    <input type="text" name="ogrnip" class="form-control"
                           value="{{ old('ogrnip', $client->ogrnip ?? '') }}"
                           placeholder="Для ИП">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Организационно-правовая форма</label>
                    <input type="text" name="legal_form" class="form-control" readonly
                           value="{{ old('legal_form', $client->legal_form ?? 'Индивидуальный предприниматель') }}">
                </div>
            </div>
        </div>

        {{-- Блок ЮЛ --}}
        <div id="block-yl" style="display:none">
            <div class="mb-3">
                <label class="form-label">Название организации *</label>
                <input type="text" name="company_name" class="form-control" required
                       value="{{ old('company_name', $client->company_name ?? '') }}"
                       placeholder="Обязательно для ЮЛ">
            </div>

            <div class="mb-3">
                <label class="form-label">Организационно-правовая форма</label>
                <input type="text" name="legal_type" class="form-control"
                       value="{{ old('legal_type', $client->legal_type ?? '') }}"
                       placeholder="Например: ООО, АО">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ОГРН</label>
                    <input type="text" name="ogrn" class="form-control"
                           value="{{ old('ogrn', $client->ogrn ?? '') }}"
                           placeholder="Для ЮЛ">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">КПП</label>
                    <input type="text" name="kpp" class="form-control"
                           value="{{ old('kpp', $client->kpp ?? '') }}"
                           placeholder="Для ЮЛ">
                </div>
            </div>
        </div>

        <hr class="my-4">

        {{-- Общие поля --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $client->email ?? '') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Телефон</label>
                <input type="text" name="phone" class="form-control"
                       value="{{ old('phone', $client->phone ?? '') }}">
            </div>
        </div>

        <div class="mb-3">
            <label>Адрес</label>
            <textarea name="address" class="form-control" rows="2">{{ old('address', $client->address ?? '') }}</textarea>
        </div>

    </div>
</div>

{{-- JS логика переключения --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const typeSelect = document.getElementById('clientType');
    
    if (!typeSelect) {
        console.error('clientType element not found');
        return;
    }

    function toggleBlocks() {
        const type = typeSelect.value;

        const blockFio = document.getElementById('block-fio');
        const blockIp = document.getElementById('block-ip');
        const blockYl = document.getElementById('block-yl');

        if (blockFio) blockFio.style.display = (type === 'individual' || type === 'entrepreneur') ? 'flex' : 'none';
        if (blockIp) blockIp.style.display = (type === 'entrepreneur') ? 'block' : 'none';
        if (blockYl) blockYl.style.display = (type === 'legal') ? 'block' : 'none';

        // Динамически меняем обязательность полей
        const firstName = document.querySelector('input[name="first_name"]');
        const lastName = document.querySelector('input[name="last_name"]');
        const companyName = document.querySelector('input[name="company_name"]');
        const innField = document.querySelector('input[name="inn"]');

        if (type === 'legal') {
            if (companyName) companyName.required = true;
            if (firstName) firstName.required = false;
            if (lastName) lastName.required = false;
            if (innField) innField.required = true;
        } else {
            if (companyName) companyName.required = false;
            if (firstName) firstName.required = true;
            if (lastName) lastName.required = true;
            if (innField) innField.required = false; // ИНН необязателен для ФЛ
        }
    }

    typeSelect.addEventListener('change', toggleBlocks);

    // Вызов при загрузке
    toggleBlocks();
});
</script>