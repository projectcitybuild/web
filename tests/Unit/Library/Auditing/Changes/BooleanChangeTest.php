<?php

namespace Tests\Unit\Library\Auditing\Changes;

use Library\Auditing\Changes\BooleanChange;
use Library\Auditing\Changes\Tokens\NotInAudit;
use Tests\TestCase;

class BooleanChangeTest extends TestCase
{
    public function test_boolean_change()
    {
        $change = new BooleanChange();
        $change->setValues(false, true);
        $this->assertEquals(false, $change->getOldValue());
        $this->assertEquals(true, $change->getNewValue());
    }

    public function test_not_in_audit()
    {
        $change = new BooleanChange();
        $change->setValues(new NotInAudit(), true);
        $this->assertInstanceOf(NotInAudit::class, $change->getOldValue());
        $this->assertEquals(true, $change->getNewValue());
    }
}
