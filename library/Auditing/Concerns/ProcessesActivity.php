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

        return $wrappedChanges->sortKeys()->toArray();
    }
}
