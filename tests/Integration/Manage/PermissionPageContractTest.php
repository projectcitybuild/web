<?php

use App\Domains\Permissions\WebManagePermission;
use App\Models\Permission;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('serializes permission list and edit pages through the permission DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::PERMISSIONS_VIEW->value,
        WebManagePermission::PERMISSIONS_EDIT->value,
    ]);
    $permission = Permission::create(['name' => 'web.manage.contract.permission']);

    $this->actingAs($manager)
        ->get(route('manage.permissions.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Permissions/PermissionList')
            ->where('permissions.0.id', $permission->id)
            ->where('permissions.0.name', 'web.manage.contract.permission')
        );

    $this->get('/manage/permissions/'.$permission->id.'/edit')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Permissions/PermissionEdit')
            ->where('permission.id', $permission->id)
            ->where('permission.name', 'web.manage.contract.permission')
        );
});
