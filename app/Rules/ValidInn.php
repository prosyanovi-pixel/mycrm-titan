<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidInn implements Rule
{
    public function passes($attribute, $value)
    {
        if (!$value) {
            return true;
        }

        return preg_match('/^\d{10}$|^\d{12}$/', $value);
    }

    public function message()
    {
        return 'Некорректный ИНН. Допустимо 10 или 12 цифр.';
    }
}
