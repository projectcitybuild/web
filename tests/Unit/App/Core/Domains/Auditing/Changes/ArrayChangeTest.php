<?php

namespace Tests\Unit\App\Core\Domains\Auditing\Changes;

use App\Core\Domains\Auditing\Changes\ArrayChange;
use App\Core\Domains\Auditing\Changes\ArrayDiff\ArrayWrapState;
use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;

it('preserves the ordering of retained values', function () {
    $change = new ArrayChange;
    $change->setValues(['kept1', 'removed', 'kept2'], ['kept1', 'kept2', 'added']);

    $this->assertEquals(
        ['kept1', 'kept2', 'removed'],
        collect($change->getOldValue())->map->unwrap()->toArray()
    );

    $this->assertEquals(
        ['kept1', 'kept2', 'added'],
        collect($change->getNewValue())->map->unwrap()->toArray()
    );
});

it('tags added and removed values', function () {
    $change = new ArrayChange;
    $change->setValues(['kept1', 'removed', 'kept2'], ['kept1', 'kept2', 'added']);

    $this->assertEquals(
        [ArrayWrapState::KEPT, ArrayWrapState::KEPT, ArrayWrapState::REMOVED],
        collect($change->getOldValue())->map->getStatus()->toArray()
    );

    $this->assertEquals(
        [ArrayWrapState::KEPT, ArrayWrapState::KEPT, ArrayWrapState::ADDED],
        collect($change->getNewValue())->map->getStatus()->toArray()
    );
});

it('marks values added when the old value is not in the audit', function () {
    $change = new ArrayChange;
    $change->setValues(new NotInAudit, ['added1', 'added2']);
    $this->assertCount(0, $change->getOldValue());
    $this->assertEquals(
        ['added1', 'added2'],
        collect($change->getNewValue())->map->unwrap()->toArray()
    );
    $this->assertEquals(
        [ArrayWrapState::ADDED, ArrayWrapState::ADDED],
        collect($change->getNewValue())->map->getStatus()->toArray()
    );
});
