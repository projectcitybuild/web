<?php

namespace App\Core\Domains\Auditing\Changes;

use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;
use App\Core\Domains\Auditing\DummyLinkable;
use Tests\TestCase;

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

    /**
     * @testdox tests the creation of a model with nullable relationship
     */
    public function test_created_and_set_to_null()
    {
        $change = new RelationshipChange(DummyLinkable::class);

        $change->setValues(new NotInAudit, null);

        $this->assertInstanceOf(NotInAudit::class, $change->getOldValue());
        $this->assertNull($change->getNewValue());
    }
}
