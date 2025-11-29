<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidPhone implements Rule
{
    public function passes($attribute, $value)
    {
        if (!$value) {
            return true;
        }

        $clean = preg_replace('/\D+/', '', $value);

        // Российские номера:
        // 10 цифр — без кода страны
        // 11 цифр — начинается с 7 или 8
        if (strlen($clean) === 10) {
            return true;
        }

        if (strlen($clean) === 11 && in_array($clean[0], ['7', '8'])) {
            return true;
        }

        return false;
    }

    public function message()
    {
        return 'Некорректный формат телефона.';
    }
}
