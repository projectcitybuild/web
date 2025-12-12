<?php

namespace App\Domains\HoneyPot\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HoneyPotRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! empty($value)) {
            abort(403);
        }
    }
}
