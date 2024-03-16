<?php

namespace App\Models\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MinecraftUUIDRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // TODO
        if (false) {
            $fail(':attribute is not a valid Minecraft UUID');
        }
    }
}
