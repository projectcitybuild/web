<?php

use App\Domains\Permissions\WebManagePermission;

it('creates missing permissions and skips existing ones', function () {
    $this->artisan('permissions:seed');

    foreach (WebManagePermission::cases() as $permission) {
        $this->assertDatabaseHas('permissions', [
            'name' => $permission->value,
        ]);
    }
});
