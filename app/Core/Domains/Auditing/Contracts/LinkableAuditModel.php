<?php

namespace App\Core\Domains\Auditing\Contracts;

/**
 * A model which can be linked to in audits
 */
interface LinkableAuditModel
{
    /**
     * Get a link to this model instance, or null if not applicable
     *
     * @return string|null
     */
    public function getActivitySubjectLink(): ?string;

    /**
     * Get the display name of this model instance, or null if not applicable
     *
     * @return string|null
     */
    public function getActivitySubjectName(): ?string;
}
