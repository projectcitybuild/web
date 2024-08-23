<?php

namespace App\Core\Domains\Auditing\Changes;

use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;
use Tests\TestCase;

class ChangeTest extends TestCase
{
    /**
     * @dataProvider setValueDataProvider
     */
    public function test_set_value($old, $new)
    {
        $change = new Change();
        $change->setValues($old, $new);
        $this->assertEquals($old, $change->getOldValue());
        $this->assertEquals($new, $change->getNewValue());
    }

    /**
     * @dataProvider setValueDataProvider
     */
    public function test_set_value_with_not_in_audit($old, $new)
    {
        $change = new Change();
        $change->setValues(new NotInAudit(), $new);
        $this->assertInstanceOf(NotInAudit::class, $change->getOldValue());
        $this->assertEquals($new, $change->getNewValue());
    }

    public function setValueDataProvider(): array
    {
        return [
            'string' => ['foo', 'bar'],
            'integer' => [1, 3],
            'decimal' => [0.3, 0.7],
        ];
    }
}
