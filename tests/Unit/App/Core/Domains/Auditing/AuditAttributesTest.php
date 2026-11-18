<?php

namespace Tests\Unit\App\Core\Domains\Auditing;

use App\Core\Domains\Auditing\AuditAttributes;
use App\Core\Domains\Auditing\Changes\ArrayChange;
use App\Core\Domains\Auditing\Changes\BooleanChange;
use App\Core\Domains\Auditing\Changes\Change;
use App\Core\Domains\Auditing\Changes\MultilineChange;
use App\Core\Domains\Auditing\Changes\RelationshipChange;
use App\Core\Domains\Auditing\DummyLinkable;

it('builds an empty attribute set', function () {
    $this->assertEquals([], AuditAttributes::build()->getAttributeNames());
});

it('adds a plain attribute', function () {
    $attrs = AuditAttributes::build()->add('foo');
    expect($attrs->getAttributeNames())->toBe(['foo']);
    expect($attrs->getChangeType('foo'))->toBeInstanceOf(Change::class);
});

it('adds multiple plain attributes', function () {
    $attrs = AuditAttributes::build()->add('foo', 'bar');
    $this->assertEquals(
        ['foo', 'bar'],
        $attrs->getAttributeNames()
    );

    $this->assertContainsOnlyInstancesOf(Change::class,
        [$attrs->getChangeType('foo'), $attrs->getChangeType('bar')]
    );
});

it('adds a relationship attribute', function () {
    $attrs = AuditAttributes::build()
        ->addRelationship('foo', DummyLinkable::class);

    expect($attrs->getAttributeNames())->toBe(['foo']);
    expect($attrs->getChangeType('foo'))->toBeInstanceOf(RelationshipChange::class);
});

it('adds an array attribute', function () {
    $attrs = AuditAttributes::build()
        ->addArray('foo');

    expect($attrs->getAttributeNames())->toBe(['foo']);
    expect($attrs->getChangeType('foo'))->toBeInstanceOf(ArrayChange::class);
});

it('adds a boolean attribute', function () {
    $attrs = AuditAttributes::build()
        ->addBoolean('foo');
    expect($attrs->getAttributeNames())->toBe(['foo']);
    expect($attrs->getChangeType('foo'))->toBeInstanceOf(BooleanChange::class);
});

it('adds a multiline attribute', function () {
    $attrs = AuditAttributes::build()
        ->addMultiline('foo');
    expect($attrs->getAttributeNames())->toBe(['foo']);
    expect($attrs->getChangeType('foo'))->toBeInstanceOf(MultilineChange::class);
});
