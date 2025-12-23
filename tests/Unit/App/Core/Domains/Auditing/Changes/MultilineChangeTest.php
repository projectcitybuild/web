<?php

namespace App\Core\Domains\Auditing\Changes;

use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MultilineChangeTest extends TestCase
{
    use WithFaker;

    public function test_set_values()
    {
        $change = new MultilineChange;
        $oldPara = $this->faker->paragraph;
        $newPara = $this->faker->paragraph;

        $change->setValues($oldPara, $newPara);

        $this->assertEquals($oldPara, $change->getOldValue());
        $this->assertEquals($newPara, $change->getNewValue());
    }

    public function test_with_not_in_audit()
    {
        $change = new MultilineChange;
        $newPara = $this->faker->paragraph;

        $change->setValues(new NotInAudit, $newPara);
        $this->assertSame('', $change->getOldValue());
        $this->assertEquals($newPara, $change->getNewValue());
    }
}
