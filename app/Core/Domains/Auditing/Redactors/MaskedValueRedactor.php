<?php

namespace App\Core\Domains\Auditing\Redactors;

use OwenIt\Auditing\Contracts\AttributeRedactor;

class MaskedValueRedactor implements AttributeRedactor
{
    public static function redact($value): string
    {
        return '[REDACTED]';
    }
}
