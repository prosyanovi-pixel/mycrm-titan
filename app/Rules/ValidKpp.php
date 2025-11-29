<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidKpp implements Rule
{
    public function passes($attribute, $value)
    {
        if (!$value) {
            return true;
        }

        return preg_match('/^\d{9}$/', $value);
    }

    public function message()
    {
        return 'Некорректный КПП. Должно быть 9 цифр.';
    }
}
