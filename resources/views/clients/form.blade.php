<div class="mb-3">
    <label class="form-label">Тип клиента</label>
    <select name="type" id="clientType" class="form-select" required>
        <option value="individual" @selected(old('type', $client->type ?? '') === 'individual')>Физическое лицо</option>
        <option value="entrepreneur" @selected(old('type', $client->type ?? '') === 'entrepreneur')>ИП</option>
        <option value="legal" @selected(old('type', $client->type ?? '') === 'legal')>Юридическое лицо</option>
    </select>
</div>

{{-- ФЛ --}}
<div id="block-individual">
    <div class="row">
        <div class="col-4 mb-3"><input class="form-control" name="last_name" placeholder="Фамилия" value="{{ old('last_name', $client->last_name ?? '') }}"></div>
        <div class="col-4 mb-3"><input class="form-control" name="first_name" placeholder="Имя" value="{{ old('first_name', $client->first_name ?? '') }}"></div>
        <div class="col-4 mb-3"><input class="form-control" name="middle_name" placeholder="Отчество" value="{{ old('middle_name', $client->middle_name ?? '') }}"></div>
    </div>
</div>

{{-- ИП --}}
<div id="block-entrepreneur">
    <div class="mb-3">
        <input class="form-control" name="last_name" placeholder="Фамилия" value="{{ old('last_name', $client->last_name ?? '') }}">
    </div>
    <div class="mb-3">
        <input class="form-control" name="first_name" placeholder="Имя" value="{{ old('first_name', $client->first_name ?? '') }}">
    </div>
    <div class="mb-3">
        <input class="form-control" name="ogrnip" placeholder="ОГРНИП" value="{{ old('ogrnip', $client->ogrnip ?? '') }}">
    </div>
</div>

{{-- Юрлицо --}}
<div id="block-legal">
    <div class="mb-3">
        <input class="form-control" name="company_name" placeholder="Название организации" value="{{ old('company_name', $client->company_name ?? '') }}">
    </div>
    <div class="row">
        <div class="col mb-3"><input class="form-control" name="legal_type" placeholder="Правовая форма (ООО, АО...)" value="{{ old('legal_type', $client->legal_type ?? '') }}"></div>
        <div class="col mb-3"><input class="form-control" name="ogrn" placeholder="ОГРН" value="{{ old('ogrn', $client->ogrn ?? '') }}"></div>
        <div class="col mb-3"><input class="form-control" name="kpp" placeholder="КПП" value="{{ old('kpp', $client->kpp ?? '') }}"></div>
    </div>
</div>

<hr>

{{-- Общие --}}
<div class="row">
    <div class="col mb-3"><input class="form-control" name="inn" placeholder="ИНН" value="{{ old('inn', $client->inn ?? '') }}"></div>
    <div class="col mb-3"><input class="form-control" name="phone" placeholder="Телефон" value="{{ old('phone', $client->phone ?? '') }}"></div>
    <div class="col mb-3"><input class="form-control" name="email" placeholder="Email" value="{{ old('email', $client->email ?? '') }}"></div>
</div>

<div class="mb-3">
    <input class="form-control" name="address" placeholder="Адрес" value="{{ old('address', $client->address ?? '') }}">
</div>

<script>
function toggleBlocks() {
    const type = document.getElementById('clientType').value;

    document.getElementById('block-individual').style.display = type === 'individual' ? 'block' : 'none';
    document.getElementById('block-entrepreneur').style.display = type === 'entrepreneur' ? 'block' : 'none';
    document.getElementById('block-legal').style.display = type === 'legal' ? 'block' : 'none';
}

document.getElementById('clientType').addEventListener('change', toggleBlocks);
document.addEventListener('DOMContentLoaded', toggleBlocks);
</script>
