<?php

use App\Domains\Permissions\WebManagePermission;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('serializes player list, show, and edit pages through the player DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::PLAYERS_VIEW->value,
        WebManagePermission::PLAYERS_EDIT->value,
    ]);
    $account = Account::factory()->create(['username' => 'contract-player-account']);
    $player = MinecraftPlayer::factory()->create([
        'alias' => 'ContractPlayer',
        'account_id' => $account->id,
    ]);

    $this->actingAs($manager)
        ->get(route('manage.players.show', $player))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Players/PlayerShow')
            ->where('player.id', $player->id)
            ->where('player.alias', 'ContractPlayer')
            ->where('player.account.username', 'contract-player-account')
        );

    $this->get(route('manage.players.edit', $player))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Players/PlayerEdit')
            ->where('player.id', $player->id)
            ->where('player.uuid', $player->uuid)
        );

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.players.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'Players/PlayerList',
        'X-Inertia-Partial-Data' => 'players',
    ])
        ->get(route('manage.players.index'))
        ->assertOk()
        ->assertJsonPath('props.players.data.0.id', $player->id)
        ->assertJsonPath('props.players.data.0.account.username', 'contract-player-account');
});
