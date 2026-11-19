<?php

use App\Domains\Permissions\WebManagePermission;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('serializes home list and edit pages through the home DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::HOMES_VIEW->value,
        WebManagePermission::HOMES_EDIT->value,
    ]);
    $player = MinecraftPlayer::factory()->create();
    $home = MinecraftHome::factory()->for($player, 'player')->create(['name' => 'contract_home']);

    $this->actingAs($manager)
        ->get('/manage/homes/'.$home->id.'/edit')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Homes/HomeEdit')
            ->where('home.id', $home->id)
            ->where('home.player.id', $player->id)
        );

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.homes.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'Homes/HomeList',
        'X-Inertia-Partial-Data' => 'homes',
    ])
        ->get(route('manage.homes.index'))
        ->assertOk()
        ->assertJsonPath('props.homes.data.0.id', $home->id)
        ->assertJsonPath('props.homes.data.0.player.id', $player->id);
});
