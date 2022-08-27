<?php

namespace Entities\Models\Eloquent;

use Library\Auditing\Concerns\ProcessesActivity;

class Activity extends \Spatie\Activitylog\Models\Activity
{
    use ProcessesActivity;

    public function getProcessedChangesAttribute(): array
    {
        return $this->getProcessedChanges($this->changes());
    }
}
