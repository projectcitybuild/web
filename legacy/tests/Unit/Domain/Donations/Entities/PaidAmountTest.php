<?php

namespace Tests\Unit\Domain\Donations\Entities;

use Domain\Donations\Entities\Denomination;
use Domain\Donations\Entities\PaidAmount;
use Tests\TestCase;

final class PaidAmountTest extends TestCase
{
    public function test_denominations()
    {
        $paidAmount = new PaidAmount(amount: 175, denomination: Denomination::CENTS);
        $this->assertEquals(expected: 175, actual: $paidAmount->toCents());
        $this->assertEquals(expected: 1.75, actual: $paidAmount->toDollars());

        $paidAmount = new PaidAmount(amount: 1.75, denomination: Denomination::DOLLARS);
        $this->assertEquals(expected: 175, actual: $paidAmount->toCents());
        $this->assertEquals(expected: 1.75, actual: $paidAmount->toDollars());
    }

    public function test_create_from_cents()
    {
        $paidAmount = PaidAmount::fromCents(100);

        $this->assertEquals(expected: 100, actual: $paidAmount->toCents());
    }
}
