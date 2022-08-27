<?php

namespace Entities\Models\Eloquent;

use Library\Auditing\Causers\SystemCauser;
use Library\Auditing\Concerns\ProcessesActivity;

class Activity extends \Spatie\Activitylog\Models\Activity
{
    use ProcessesActivity;

    public function getProcessedChangesAttribute(): array
    {
        return $this->getProcessedChanges($this->changes());
    }

    public function getSystemCauser(): ?SystemCauser
    {
        return SystemCauser::tryFromName($this->properties->get('system_causer'));
    }
}
