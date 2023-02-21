<?php

namespace Entities\Models\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Library\Auditing\Causers\SystemCauser;
use Library\Auditing\Concerns\ProcessesActivity;

class Activity extends \Spatie\Activitylog\Models\Activity
{
    use ProcessesActivity;

    protected $table = 'activity_log';

    /**
     * Attribute for processed changes of this activity
     *
     * @return Collection
     */
    public function getProcessedChangesAttribute(): Collection
    {
        return $this->getProcessedChanges($this->changes());
    }

    public function getSystemCauserAttribute(): ?SystemCauser
    {
        return SystemCauser::tryFromName($this->properties->get('system_causer'));
    }

    public function getHumanActionAttribute(): string
    {
        return str_replace('_', ' ', $this->subject_type).' '.$this->description;
    }

    public function scopeDistinctActions(Builder $query): Builder
    {
        return $query->select(['subject_type', 'description'])->distinct();
    }
}
