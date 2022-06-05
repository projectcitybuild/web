<?php

namespace Library\Auditing\Traits;

trait Linkable
{
    public function getActivitySubjectLink(): ?string {
        return null;
    }

    public function getActivitySubjectName(): ?string {
        return null;
    }
}
