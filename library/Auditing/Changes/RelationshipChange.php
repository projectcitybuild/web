<?php

namespace Library\Auditing\Changes;

use Library\Auditing\Changes\Tokens\NotInAudit;
use Library\Auditing\Contracts\LinkableAuditModel;

/**
 * A change to a foreign key column
 */
class RelationshipChange extends Change
{
    /**
     * @param  class-string<LinkableAuditModel>  $model
     */
    public function __construct(private string $model)
    {
    }

    public function setValues(mixed $oldValue, mixed $newValue): self
    {
        $oldValue = $this->findModel($oldValue);
        $newValue = $this->findModel($newValue);

        return parent::setValues($oldValue, $newValue);
    }

    private function findModel(mixed $fromValue): LinkableAuditModel|NotInAudit|null
    {
        if ($fromValue instanceof NotInAudit || $fromValue == null) {
            return $fromValue;
        }

        return $this->model::find($fromValue);
    }
}
