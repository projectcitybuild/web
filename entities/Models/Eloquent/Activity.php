<?php

namespace Entities\Models\Eloquent;

class Activity extends \Spatie\Activitylog\Models\Activity
{
    public function getChangesZippedAttribute()
    {
        $changes = $this->changes();
        if (! $changes->has('old')) {
            return [];
        }
        $onlyChanges = [];
        foreach ($changes['attributes'] as $attribute => $newValue) {
            $oldValue = $changes['old'][$attribute];
            $onlyChanges[$attribute] = [
                'new' => $newValue,
                'old' => $oldValue,
            ];
        }

        return $onlyChanges;
    }
}
