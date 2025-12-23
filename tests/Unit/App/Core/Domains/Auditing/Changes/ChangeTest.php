<?php

namespace App\Core\Domains\Auditing\Changes;

use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;

it('sets value', function ($old, $new) {
    $change = new Change;
    $change->setValues($old, $new);
    $this->assertEquals($old, $change->getOldValue());
    $this->assertEquals($new, $change->getNewValue());
})->with([
    'string' => ['foo', 'bar'],
    'integer' => [1, 3],
    'decimal' => [0.3, 0.7],
]);

it('sets value with NotInAudit', function ($old, $new) {
    $change = new Change;
    $change->setValues(new NotInAudit, $new);
    $this->assertInstanceOf(NotInAudit::class, $change->getOldValue());
    $this->assertEquals($new, $change->getNewValue());
})->with([
    'string' => ['foo', 'bar'],
    'integer' => [1, 3],
    'decimal' => [0.3, 0.7],
]);
