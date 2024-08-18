<?php

namespace App\Core\Domains\Auditing\Concerns;

use App\Core\Domains\Auditing\Changes\Change;
use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;
use Illuminate\Support\Collection;

trait ProcessesActivity
{
    /**
     * Gets instances of the Change class for the given changes.
     *
     * @param Collection{attributes: array, old?: array} $changes
     * @return Collection<Change>
     */
    public function getProcessedChanges(Collection $changes): Collection
    {
        $wrappedChanges = collect();
        foreach ($changes['attributes'] as $attribute => $currentValue) {
            // If this is from a create event, there won't be old data
            $oldValue = $changes->has('old') ? $changes['old'][$attribute] : new NotInAudit();

            $wrappedChanges->put(
                $attribute,
                $this->subject->auditAttributeConfig()
                    ->getChangeType($attribute)
                    ->setValues(
                        $oldValue,
                        $currentValue
                    )
            );
        }

        return $wrappedChanges->sortKeys();
    }
}
