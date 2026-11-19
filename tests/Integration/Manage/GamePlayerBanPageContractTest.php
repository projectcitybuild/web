<?php

use App\Domains\Permissions\WebManagePermission;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('serializes player ban list and edit pages through the ban DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::UUID_BANS_VIEW->value,
        WebManagePermission::UUID_BANS_EDIT->value,
    ]);
    $bannedPlayer = MinecraftPlayer::factory()->create(['alias' => 'ContractBanned']);
    $bannerPlayer = MinecraftPlayer::factory()->create(['alias' => 'ContractBanner']);
    $ban = GamePlayerBan::factory()
        ->bannedPlayer($bannedPlayer)
        ->bannedBy($bannerPlayer)
        ->create(['reason' => 'Contract reason']);

    $this->actingAs($manager)
        ->get(route('manage.player-bans.edit', $ban))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('PlayerBans/PlayerBanEdit')
            ->where('ban.id', $ban->id)
            ->where('ban.banned_player.id', $bannedPlayer->id)
            ->where('ban.banner_player.id', $bannerPlayer->id)
        );

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.player-bans.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'PlayerBans/PlayerBanList',
        'X-Inertia-Partial-Data' => 'bans',
    ])
        ->get(route('manage.player-bans.index'))
        ->assertOk()
        ->assertJsonPath('props.bans.data.0.id', $ban->id)
        ->assertJsonPath('props.bans.data.0.banned_player.alias', 'ContractBanned')
        ->assertJsonPath('props.bans.data.0.banner_player.alias', 'ContractBanner');
});
