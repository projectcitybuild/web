<?php

use App\Domains\Permissions\WebManagePermission;
use App\Models\MinecraftWarp;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('serializes warp list and edit pages through the warp DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::WARPS_VIEW->value,
        WebManagePermission::WARPS_EDIT->value,
    ]);
    $warp = MinecraftWarp::factory()->create(['name' => 'contract_warp']);

    $this->actingAs($manager)
        ->get(route('manage.minecraft.warps.edit', $warp))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Warps/WarpEdit')
            ->where('warp.id', $warp->id)
            ->where('warp.name', 'contract_warp')
        );

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.minecraft.warps.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'Warps/WarpList',
        'X-Inertia-Partial-Data' => 'warps',
    ])
        ->get(route('manage.minecraft.warps.index'))
        ->assertOk()
        ->assertJsonPath('props.warps.data.0.id', $warp->id)
        ->assertJsonPath('props.warps.data.0.name', 'contract_warp');
});
