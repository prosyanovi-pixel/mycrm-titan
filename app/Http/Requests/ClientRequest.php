<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidInn;
use App\Rules\ValidKpp;
use App\Rules\ValidOgrn;
use App\Rules\ValidOgrnip;
use App\Rules\ValidPhone;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // если нужна ACL — поменяем позже
    }

    public function rules(): array
    {
        $type = $this->input('client_type');

        $rules = [
            'client_type' => ['required', 'in:individual,entrepreneur,legal'],
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['nullable', 'email'],
            'phone'       => ['nullable', new ValidPhone],
            'inn'         => ['nullable', new ValidInn],
            'extra_data'  => ['nullable', 'array'],
        ];

        // Правила для Юридических лиц
        if ($type === 'legal') {
            $rules['inn']  = ['required', new ValidInn];
            $rules['kpp']  = ['required', new ValidKpp];
            $rules['ogrn'] = ['required', new ValidOgrn];
        }

        // Правила для ИП
        if ($type === 'entrepreneur') {
            $rules['inn']    = ['required', new ValidInn];
            $rules['ogrnip'] = ['required', new ValidOgrnip];
        }

        // ФЛ — как правило, простой: паспорт в extra_data
        if ($type === 'individual') {
            // пример: проверка паспорта, если надо
            if ($this->has('extra_data.passport')) {
                $rules['extra_data.passport'] = ['string', 'max:50'];
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'client_type.required' => 'Укажите тип клиента.',
            'name.required'        => 'Введите имя или наименование клиента.',
            'inn.required'         => 'ИНН обязателен для выбранного типа клиента.',
            'kpp.required'         => 'КПП обязателен для юридических лиц.',
            'ogrn.required'        => 'ОГРН обязателен для юридических лиц.',
            'ogrnip.required'      => 'ОГРНИП обязателен для индивидуальных предпринимателей.',
        ];
    }
}
