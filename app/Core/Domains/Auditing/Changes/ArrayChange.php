<?php

namespace App\Core\Domains\Auditing\Changes;

use App\Core\Domains\Auditing\Changes\ArrayDiff\ArrayWrapState;
use App\Core\Domains\Auditing\Changes\ArrayDiff\WrappedArrayEntry;
use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;

/**
 * A change to an array which should be presented with kept/added/removed data
 */
class ArrayChange extends Change
{
    /**
     * @param  mixed|NotInAudit  $oldValue
     * @param  mixed  $newValue
     * @return Change
     */
    public function setValues(mixed $oldValue, mixed $newValue): Change
    {
        if ($oldValue instanceof NotInAudit) {
            $oldValue = [];
        }

        $oldValue = collect($oldValue);
        $newValue = collect($newValue);

        $addedValues = $newValue->diff($oldValue);
        $removedValues = $oldValue->diff($newValue);
        $keptValues = $newValue->intersect($oldValue);

        // Tag the state of the values
        $addedValues = WrappedArrayEntry::wrapAll($addedValues, ArrayWrapState::ADDED);
        $removedValues = WrappedArrayEntry::wrapAll($removedValues, ArrayWrapState::REMOVED);
        $keptValues = WrappedArrayEntry::wrapAll($keptValues, ArrayWrapState::KEPT);

        // Order old and new values with kept values first
        $oldValue = $keptValues->merge($removedValues)->toArray();
        $newValue = $keptValues->merge($addedValues)->toArray();

        return parent::setValues($oldValue, $newValue);
    }
}
