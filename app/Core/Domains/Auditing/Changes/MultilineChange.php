<?php

namespace App\Core\Domains\Auditing\Changes;

use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;

/**
 * A change where the diff should be shown with a diff viewer intended for long text
 */
class MultilineChange extends Change
{
    public function setValues(mixed $oldValue, mixed $newValue): Change
    {
        return parent::setValues(
            $oldValue instanceof NotInAudit ? '' : $oldValue,
            $newValue,
        );
    }
}
