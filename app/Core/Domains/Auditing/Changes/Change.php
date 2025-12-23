<?php

namespace App\Core\Domains\Auditing\Changes;

use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;

/**
 * A change in a single attribute.
 *
 * This parent class applies no processing to attribute values,
 *  use subclasses for special data types.
 */
class Change
{
    private mixed $oldValue;
    private mixed $newValue;

    /**
     * @param  mixed|NotInAudit  $oldValue
     * @return $this
     */
    public function setValues(mixed $oldValue, mixed $newValue): self
    {
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;

        return $this;
    }

    public function getOldValue(): mixed
    {
        return $this->oldValue;
    }

    public function getNewValue(): mixed
    {
        return $this->newValue;
    }
}
