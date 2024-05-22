<?php

namespace App\Models\Rules;

use App\Models\MinecraftUUID;
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
        $uuid = MinecraftUUID::tryParse($value);

        if ($uuid === null) {
            $fail(':attribute is not a valid Minecraft UUID');
        }
    }
}
