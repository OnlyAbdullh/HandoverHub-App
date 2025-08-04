<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DecimalFlexibleRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        return is_numeric($value) && $value >= 0 && preg_match('/^\d+(\.\d{1,2})?$/', $value);
    }

    public function message(): string
    {
        return 'The :attribute must be a number with up to 2 decimal places and not negative.';
    }
}
