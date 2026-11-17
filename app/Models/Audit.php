<?php

namespace App\Models;

use App\Core\Domains\Auditing\Changes\Change;
use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;
use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Models\Audit as BaseAudit;

class Audit extends BaseAudit
{
    protected $table = 'audits';

    public function getSubjectAttribute(): ?Model
    {
        return $this->auditable;
    }

    public function getCauserAttribute(): ?Model
    {
        return $this->user;
    }

    public function getHumanActionAttribute(): string
    {
        $subjectType = $this->auditable
            ? class_basename($this->auditable)
            : class_basename($this->auditable_type);

        return $subjectType.' '.$this->event;
    }

    public function getProcessedChangesAttribute(): Collection
    {
        $oldValues = $this->old_values ?? [];
        $newValues = $this->new_values ?? [];
        $attributes = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
        $subject = $this->auditable;
        $attributeConfig = $subject instanceof LinkableAuditModel
            && method_exists($subject, 'auditAttributeConfig')
                ? $subject->auditAttributeConfig()
                : null;

        return collect($attributes)
            ->mapWithKeys(function (string $attribute) use ($attributeConfig, $oldValues, $newValues): array {
                $change = $attributeConfig?->getChangeType($attribute) ?? new Change;
                $oldValue = array_key_exists($attribute, $oldValues)
                    ? $oldValues[$attribute]
                    : new NotInAudit;
                $newValue = array_key_exists($attribute, $newValues)
                    ? $newValues[$attribute]
                    : new NotInAudit;

                return [$attribute => $change->setValues($oldValue, $newValue)];
            })
            ->sortKeys();
    }
}
