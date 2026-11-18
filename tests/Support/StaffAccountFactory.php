<?php

namespace Tests\Support;

use App\Models\Account;
use App\Models\Permission;
use App\Models\Role;

trait StaffAccountFactory
{
    protected function createStaffAccountWithPermissions(array $permissionNames): Account
    {
        $account = Account::factory()
            ->hasFinishedTotp()
            ->create();
        $role = Role::factory()->staff()->create();
        $account->roles()->attach($role->id);

        $permissionIds = collect($permissionNames)
            ->map(fn (string $name): int => Permission::create(['name' => $name])->id)
            ->all();

        $role->permissions()->attach($permissionIds);

        return $account;
    }
}
