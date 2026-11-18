<?php

namespace Tests\Unit\App\Core\Domains\Auditing\Changes;

use App\Core\Domains\Auditing\Changes\RelationshipChange;
use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;
use App\Core\Domains\Auditing\DummyLinkable;

it('sets old and new relationship values', function () {
    $change = new RelationshipChange(DummyLinkable::class);

    $change->setValues(1, 2);

    $this->assertEquals(1, $change->getOldValue()->getFakeId());
    $this->assertEquals(2, $change->getNewValue()->getFakeId());
});

it('keeps a null relationship value when the old value is not in the audit', function () {
    $change = new RelationshipChange(DummyLinkable::class);

    $change->setValues(new NotInAudit, null);

    $this->assertInstanceOf(NotInAudit::class, $change->getOldValue());
    $this->assertNull($change->getNewValue());
});
