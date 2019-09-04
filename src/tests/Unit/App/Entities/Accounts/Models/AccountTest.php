<?php


namespace Tests\Unit\App\Entities\Accounts\Models;


use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\GroupEnum;
use App\Entities\Groups\Models\Group;
use Tests\TestCase;

class AccountTest extends TestCase
{
    private $adminGroup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminGroup = Group::create([
            'name' => GroupEnum::Administrator,
            'alias' => 'Admin',
            'is_staff' => true,
            'is_admin' => true,
        ]);

        $this->staffGroup = Group::create([
            'name' => GroupEnum::Operator,
            'alias' => 'OP',
            'is_staff' => true,
        ]);

        $this->normalGroup = Group::create([
            'name' => GroupEnum::Trusted,
        ]);
    }

    public function testIsAdminForAdmin()
    {
        $account = factory(Account::class)->create();
        $account->groups()->attach($this->adminGroup->group_id);

        $this->assertTrue($account->isAdmin());
    }

    public function testIsAdminForNonAdmin()
    {
        $account = factory(Account::class)->create();
        $account->groups()->attach($this->normalGroup->group_id);

        $this->assertFalse($account->isAdmin());
    }
}
