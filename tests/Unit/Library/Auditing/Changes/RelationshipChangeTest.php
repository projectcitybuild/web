<?php

namespace Tests\Unit\Library\Auditing\Changes;

use Library\Auditing\Changes\RelationshipChange;
use Tests\TestCase;
use Tests\Unit\Library\Auditing\DummyLinkable;

class RelationshipChangeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_set_values()
    {
        $change = new RelationshipChange(DummyLinkable::class);

        $change->setValues(1, 2);

        $this->assertEquals(1, $change->getOldValue()->getFakeId());
        $this->assertEquals(2, $change->getNewValue()->getFakeId());
    }
}
