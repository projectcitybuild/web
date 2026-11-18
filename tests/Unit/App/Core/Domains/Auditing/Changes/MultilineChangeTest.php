<?php

namespace Tests\Unit\App\Core\Domains\Auditing\Changes;

use App\Core\Domains\Auditing\Changes\MultilineChange;
use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

it('stores old and new multiline values', function () {
    $change = new MultilineChange;
    $oldPara = $this->faker->paragraph;
    $newPara = $this->faker->paragraph;

    $change->setValues($oldPara, $newPara);

    $this->assertEquals($oldPara, $change->getOldValue());
    $this->assertEquals($newPara, $change->getNewValue());
});

it('uses an empty old value when it is not in the audit', function () {
    $change = new MultilineChange;
    $newPara = $this->faker->paragraph;

    $change->setValues(new NotInAudit, $newPara);
    $this->assertSame('', $change->getOldValue());
    $this->assertEquals($newPara, $change->getNewValue());
});
