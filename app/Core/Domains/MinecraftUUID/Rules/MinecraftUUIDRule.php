<?php

namespace App\Core\Domains\MinecraftUUID\Rules;

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MinecraftUUIDRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $uuid = MinecraftUUID::tryParse($value);

        if ($uuid === null) {
            $fail(':attribute is not a valid Minecraft UUID');
        }
    }
}
