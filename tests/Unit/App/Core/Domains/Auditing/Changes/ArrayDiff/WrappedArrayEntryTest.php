<?php

namespace App\Core\Domains\Auditing\Changes\ArrayDiff;

use Tests\TestCase;

class WrappedArrayEntryTest extends TestCase
{
    private array $myArray = ['a', 'b', 'c'];

    public function test_wrap_all()
    {
        $wrapped = WrappedArrayEntry::wrapAll(collect($this->myArray), ArrayWrapState::KEPT);
        $this->assertContainsOnlyInstancesOf(WrappedArrayEntry::class, $wrapped->toArray());

        $this->assertEquals(
            $this->myArray,
            [$wrapped[0]->unwrap(), $wrapped[1]->unwrap(), $wrapped[2]->unwrap()],
            'Array has different values or order'
        );
    }

    public function test_wrap_all_empty()
    {
        $wrapped = WrappedArrayEntry::wrapAll(collect(), ArrayWrapState::REMOVED);
        $this->assertEquals(0, $wrapped->count());
    }

    public function test_wrap()
    {
        $wrapped = new WrappedArrayEntry('a', ArrayWrapState::KEPT);
        $this->assertEquals('a', $wrapped->unwrap());
        $this->assertEquals(ArrayWrapState::KEPT, $wrapped->getStatus());
    }
}
