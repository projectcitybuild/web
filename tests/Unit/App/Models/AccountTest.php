<?php

namespace App\Models;

use Tests\TestCase;

class AccountTest extends TestCase
{
    private $adminGroup;
    private $normalGroup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminGroup = Group::factory()->create([
            'name' => 'administrator',
            'alias' => 'Admin',
            'is_staff' => true,
            'is_admin' => true,
        ]);

        $this->normalGroup = Group::factory()->create([
            'name' => 'trusted',
        ]);
    }

    public function test_admin_is_admin()
    {
        $account = Account::factory()->create();
        $account->groups()->attach($this->adminGroup->group_id);

        $this->assertTrue($account->isAdmin());
    }

    public function test_non_admin_is_not_admin()
    {
        $account = Account::factory()->create();
        $account->groups()->attach($this->normalGroup->group_id);

        $this->assertFalse($account->isAdmin());
    }

    public function test_group_scopes()
    {
        $scope1 = GroupScope::factory()->create();
        $scope2 = GroupScope::factory()->create();
        $scope3 = GroupScope::factory()->create();
        $scope4 = GroupScope::factory()->create();

        $group1 = Group::factory()->create();
        $group1->groupScopes()->attach([$scope1->getKey(), $scope2->getKey()]);

        $group2 = Group::factory()->create();
        $group2->groupScopes()->attach($scope3->getKey());

        $account = Account::factory()->create();
        $account->groups()->attach([$group1->getKey(), $group2->getKey()]);

        $this->assertTrue($account->hasAbility($scope1->scope));
        $this->assertTrue($account->hasAbility($scope2->scope));
        $this->assertTrue($account->hasAbility($scope3->scope));
        $this->assertFalse($account->hasAbility($scope4->scope));
    }
}
