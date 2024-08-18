<?php

namespace App\Core\Domains\Auditing\Changes\Tokens;

/**
 * Special value to signify a value is missing rather than null
 * This will happen in audits that don't have a diff, i.e. creation event audits
 */
class NotInAudit
{
    public function __toString(): string
    {
        return '!NotInAudit!';
    }
}
