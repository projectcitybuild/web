<?php

namespace Tests\Unit\App\Library\Auditing;

use App\Entities\Accounts\Models\Account;
use App\Library\Auditing\AuditableClassResolver;
use Tests\TestCase;

class AuditableClassResolver_Test extends TestCase
{
    public function testLabelCanBeResolvedToClass()
    {
        $resolver = new AuditableClassResolver();
        $class = $resolver->resolveLabelToClass('account');
        $this->assertEquals('App\Entities\Accounts\Models\Account', $class);
    }

    public function testCanGetLabelForClass()
    {
        $resolver = new AuditableClassResolver();
        $account = Account::factory()->make();
        $label = $resolver->getAuditListLabel($account);
        $this->assertEquals('account', $label);
    }
}
