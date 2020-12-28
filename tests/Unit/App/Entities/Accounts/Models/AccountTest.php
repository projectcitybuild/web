<?php

namespace Tests\Unit\App\Entities\Accounts\Models;

use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use Tests\TestCase;

class AccountTest extends TestCase
{
    private $adminGroup;
    private $staffGroup;
    private $normalGroup;
    private $memberGroup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminGroup = Group::create([
            'name' => 'administrator',
            'alias' => 'Admin',
            'is_staff' => true,
            'is_admin' => true,
            'discourse_name' => 'administrator',
        ]);

        $this->staffGroup = Group::create([
            'name' => 'operator',
            'alias' => 'OP',
            'is_staff' => true,
            'discourse_name' => 'operator',
        ]);

        $this->normalGroup = Group::create([
            'name' => 'trusted',
            'discourse_name' => 'trusted',
        ]);

        $this->memberGroup = Group::create([
            'name' => 'member',
            'discourse_name' => '',
        ]);
    }

    public function testIsAdminForAdmin()
    {
        $account = Account::factory()->create();
        $account->groups()->attach($this->adminGroup->group_id);

        $this->assertTrue($account->isAdmin());
    }

    public function testIsAdminForNonAdmin()
    {
        $account = Account::factory()->create();
        $account->groups()->attach($this->normalGroup->group_id);

        $this->assertFalse($account->isAdmin());
    }

    public function testDiscourseGroupString()
    {
        $account = Account::factory()->create();

        $account->groups()->attach($this->normalGroup->group_id);

        $this->assertEquals('trusted', $account->discourseGroupString());

        $account->groups()->attach($this->staffGroup->group_id);
        $account->refresh();

        $this->assertEquals('trusted,operator', $account->discourseGroupString());
    }

    public function testDiscourseGroupStringForNullGroup()
    {
        $account = Account::factory()->create();

        $account->groups()->attach($this->memberGroup->group_id);
        $account->groups()->attach($this->normalGroup->group_id);

        $this->assertEquals('trusted', $account->discourseGroupString());
    }
}
