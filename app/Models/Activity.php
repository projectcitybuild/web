<?php

namespace App\Models;

use App\Core\Domains\Auditing\Causers\SystemCauser;
use App\Core\Domains\Auditing\Concerns\ProcessesActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Activity extends \Spatie\Activitylog\Models\Activity
{
    use ProcessesActivity;

    protected $table = 'activity_log';

    /**
     * Attribute for processed changes of this activity
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
