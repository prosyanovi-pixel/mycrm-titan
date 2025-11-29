<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidOgrnip implements Rule
{
    public function passes($attribute, $value)
    {
        if (!$value) {
            return true;
        }

        return preg_match('/^\d{15}$/', $value);
    }

    public function message()
    {
        return 'Некорректный ОГРНИП. Должно быть 15 цифр.';
    }
}

