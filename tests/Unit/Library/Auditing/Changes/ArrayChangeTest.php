<?php

namespace Tests\Unit\Library\Auditing\Changes;

use App\Core\Domains\Auditing\Changes\ArrayChange;
use App\Core\Domains\Auditing\Changes\ArrayDiff\ArrayWrapState;
use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;
use Tests\TestCase;

class ArrayChangeTest extends TestCase
{
    public function test_value_reordering()
    {
        $change = new ArrayChange();
        $change->setValues(['kept1', 'removed', 'kept2'], ['kept1', 'kept2', 'added']);

        $this->assertEquals(
            ['kept1', 'kept2', 'removed'],
            collect($change->getOldValue())->map->unwrap()->toArray()
        );

        $this->assertEquals(
            ['kept1', 'kept2', 'added'],
            collect($change->getNewValue())->map->unwrap()->toArray()
        );
    }

    public function test_value_tagging()
    {
        $change = new ArrayChange();
        $change->setValues(['kept1', 'removed', 'kept2'], ['kept1', 'kept2', 'added']);

        $this->assertEquals(
            [ArrayWrapState::KEPT, ArrayWrapState::KEPT, ArrayWrapState::REMOVED],
            collect($change->getOldValue())->map->getStatus()->toArray()
        );

        $this->assertEquals(
            [ArrayWrapState::KEPT, ArrayWrapState::KEPT, ArrayWrapState::ADDED],
            collect($change->getNewValue())->map->getStatus()->toArray()
        );
    }

    public function test_not_in_audit()
    {
        $change = new ArrayChange();
        $change->setValues(new NotInAudit(), ['added1', 'added2']);
        $this->assertCount(0, $change->getOldValue());
        $this->assertEquals(
            ['added1', 'added2'],
            collect($change->getNewValue())->map->unwrap()->toArray()
        );
        $this->assertEquals(
            [ArrayWrapState::ADDED, ArrayWrapState::ADDED],
            collect($change->getNewValue())->map->getStatus()->toArray()
        );
    }
}
