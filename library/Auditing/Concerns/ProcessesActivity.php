<?php

namespace Library\Auditing\Concerns;

use Library\Auditing\Changes\Tokens\NotInAudit;

trait ProcessesActivity
{
    public function getProcessedChanges($changes): array
    {
        $wrappedChanges = collect();
        foreach ($changes['attributes'] as $attribute => $currentValue) {
            // If this is from a create event, there won't be old data
            $oldValue = $changes->has('old') ? $changes['old'][$attribute] : null;
            // If old data was missing, or was null, replace with the NotInAudit token.
            $oldValue = $oldValue ?? new NotInAudit();

            $wrappedChanges->put(
                $attribute,
                $this->subject->auditAttributeConfig()
                    ->getChangeType($attribute)
                    ->setValues(
                        $oldValue ?? new NotInAudit(),
                        $currentValue
                    )
            );
        }

        return $wrappedChanges->sortKeys()->toArray();
    }
}
