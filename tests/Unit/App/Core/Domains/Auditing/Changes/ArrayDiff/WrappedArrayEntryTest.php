<?php

namespace Tests\Unit\App\Core\Domains\Auditing\Changes\ArrayDiff;

use App\Core\Domains\Auditing\Changes\ArrayDiff\ArrayWrapState;
use App\Core\Domains\Auditing\Changes\ArrayDiff\WrappedArrayEntry;

it('wraps all entries with the requested state', function () {
    $values = ['a', 'b', 'c'];
    $wrapped = WrappedArrayEntry::wrapAll(collect($values), ArrayWrapState::KEPT);
    $this->assertContainsOnlyInstancesOf(WrappedArrayEntry::class, $wrapped->toArray());

    $this->assertEquals(
        $values,
        [$wrapped[0]->unwrap(), $wrapped[1]->unwrap(), $wrapped[2]->unwrap()],
        'Array has different values or order'
    );
});

it('returns an empty collection when wrapping no entries', function () {
    $wrapped = WrappedArrayEntry::wrapAll(collect(), ArrayWrapState::REMOVED);
    $this->assertEquals(0, $wrapped->count());
});

it('wraps a single value with its state', function () {
    $wrapped = new WrappedArrayEntry('a', ArrayWrapState::KEPT);
    $this->assertEquals('a', $wrapped->unwrap());
    $this->assertEquals(ArrayWrapState::KEPT, $wrapped->getStatus());
});
