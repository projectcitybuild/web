<?php

namespace App\Core\Domains\Auditing;

use App\Core\Domains\Auditing\Contracts\LinkableAuditModel;

class DummyLinkable implements LinkableAuditModel
{
    public function __construct(
        private int $fakeId
    ) {}

    public function getActivitySubjectLink(): ?string
    {
        return 'link';
    }

    public function getActivitySubjectName(): ?string
    {
        return 'name';
    }

    public static function find($value): DummyLinkable
    {
        return new DummyLinkable($value);
    }

    public function getFakeId(): int
    {
        return $this->fakeId;
    }
}
