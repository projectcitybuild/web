<?php

namespace Library\Auditing\Traits;

trait Linkable
{
    public abstract function getActivitySubjectLink(): ?string;
    public abstract function getActivitySubjectName(): ?string;
}
