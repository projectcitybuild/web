<?php

namespace Library\Auditing\Traits;

trait Linkable
{
    abstract public function getActivitySubjectLink(): ?string;
    abstract public function getActivitySubjectName(): ?string;
}
