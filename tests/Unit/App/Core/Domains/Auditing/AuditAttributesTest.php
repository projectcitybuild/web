<?php

namespace App\Core\Domains\Auditing;

use App\Core\Domains\Auditing\Changes\ArrayChange;
use App\Core\Domains\Auditing\Changes\BooleanChange;
use App\Core\Domains\Auditing\Changes\Change;
use App\Core\Domains\Auditing\Changes\MultilineChange;
use App\Core\Domains\Auditing\Changes\RelationshipChange;
use Tests\TestCase;

class AuditAttributesTest extends TestCase
{
    public function test_build_returns_empty_instance()
    {
        $this->assertEquals([], AuditAttributes::build()->getAttributeNames());
    }

    private function assertAdded($attributes, $className)
    {
        $this->assertEquals(
            ['foo'],
            $attributes->getAttributeNames()
        );

        $this->assertInstanceOf($className, $attributes->getChangeType('foo'));
    }

    public function test_add_plain_attribute()
    {
        $attrs = AuditAttributes::build()->add('foo');
        $this->assertAdded($attrs, Change::class);
    }

    public function test_add_multiple_plain_attributes()
    {
        $attrs = AuditAttributes::build()->add('foo', 'bar');
        $this->assertEquals(
            ['foo', 'bar'],
            $attrs->getAttributeNames()
        );

        $this->assertContainsOnlyInstancesOf(Change::class,
            [$attrs->getChangeType('foo'), $attrs->getChangeType('bar')]
        );
    }

    public function test_add_relationship()
    {
        $attrs = AuditAttributes::build()
            ->addRelationship('foo', DummyLinkable::class);

        $this->assertAdded($attrs, RelationshipChange::class);
    }

    public function test_add_array()
    {
        $attrs = AuditAttributes::build()
            ->addArray('foo');

        $this->assertAdded($attrs, ArrayChange::class);
    }

    public function test_add_boolean()
    {
        $attrs = AuditAttributes::build()
            ->addBoolean('foo');
        $this->assertAdded($attrs, BooleanChange::class);
    }

    public function test_add_multiline()
    {
        $attrs = AuditAttributes::build()
            ->addMultiline('foo');
        $this->assertAdded($attrs, MultilineChange::class);
    }
}
