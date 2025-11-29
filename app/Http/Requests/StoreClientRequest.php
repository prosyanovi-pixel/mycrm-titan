<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:individual,entrepreneur,legal',

            'last_name' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',

            'company_name' => 'nullable|string|max:255',
            'legal_type' => 'nullable|string|max:50',

            'inn' => 'nullable|string|max:12',
            'ogrn' => 'nullable|string|max:15',
            'kpp' => 'nullable|string|max:9',

            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',

            'responsible_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:lead,active,inactive',
        ];
    }
}
