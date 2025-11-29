<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidOgrn implements Rule
{
    public function passes($attribute, $value)
    {
        if (!$value) {
            return true;
        }

        return preg_match('/^\d{13}$/', $value);
    }

    public function message()
    {
        return 'Некорректный ОГРН. Должно быть 13 цифр.';
    }
}
