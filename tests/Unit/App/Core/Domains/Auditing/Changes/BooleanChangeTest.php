<?php

namespace Tests\Unit\App\Core\Domains\Auditing\Changes;

use App\Core\Domains\Auditing\Changes\BooleanChange;
use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;

it('stores old and new boolean values', function () {
    $change = new BooleanChange;
    $change->setValues(false, true);
    $this->assertEquals(false, $change->getOldValue());
    $this->assertEquals(true, $change->getNewValue());
});

it('preserves a not-in-audit old value', function () {
    $change = new BooleanChange;
    $change->setValues(new NotInAudit, true);
    $this->assertInstanceOf(NotInAudit::class, $change->getOldValue());
    $this->assertEquals(true, $change->getNewValue());
});
