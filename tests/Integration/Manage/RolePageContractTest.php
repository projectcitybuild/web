<?php

use App\Domains\Permissions\WebManagePermission;
use App\Models\Account;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('serializes role and assigned permission pages through their DTOs', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::ROLES_VIEW->value,
        WebManagePermission::ROLES_EDIT->value,
        WebManagePermission::PERMISSIONS_ASSIGN->value,
    ]);
    $role = Role::factory()->create([
        'name' => 'contract-role',
        'role_type' => 'build',
        'display_priority' => 3,
    ]);
    $permission = Permission::create(['name' => 'web.manage.contract.view']);
    $role->permissions()->attach($permission->id);

    $this->actingAs($manager)
        ->get('/manage/roles/'.$role->id.'/edit')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Roles/RoleEdit')
            ->where('role.id', $role->id)
            ->where('role.name', 'contract-role')
        );

    $this->get('/manage/roles/'.$role->id.'/permissions')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Roles/RolePermissionSelect')
            ->where('permissions.0.id', $permission->id)
            ->where('permissions.0.name', 'web.manage.contract.view')
            ->where('role_permission_ids.0', $permission->id)
        );

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.roles.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'Roles/RoleList',
        'X-Inertia-Partial-Data' => 'roles',
    ])
        ->get(route('manage.roles.index'))
        ->assertOk()
        ->assertJsonFragment([
            'id' => $role->id,
            'name' => 'contract-role',
            'permissions_count' => 1,
        ]);
});

it('serializes role members through the account DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ROLES_VIEW->value]);
    $role = Role::factory()->create(['name' => 'contract-members']);
    $account = Account::factory()->create([
        'username' => 'contract-member',
        'email' => 'contract-member@example.test',
    ]);
    $role->accounts()->attach($account->id);

    $this->actingAs($manager)
        ->get('/manage/roles/'.$role->id.'/accounts')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Roles/RoleAccountList')
            ->where('role.id', $role->id)
            ->where('accounts.data.0.id', $account->id)
            ->where('accounts.data.0.username', 'contract-member')
            ->where('accounts.data.0.email', 'contract-member@example.test')
        );
});
