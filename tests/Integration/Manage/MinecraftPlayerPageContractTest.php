<?php

use App\Domains\Permissions\WebManagePermission;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerSession;
use App\Models\Server;
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

it('lists sessions belonging to the requested player', function () {
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::PLAYERS_VIEW->value]);
    $player = MinecraftPlayer::factory()->create();
    $otherPlayer = MinecraftPlayer::factory()->create();
    $olderSession = MinecraftPlayerSession::create([
        'player_id' => $player->id,
        'seconds' => 3600,
        'starts_at' => '2026-01-01 10:00:00',
        'ends_at' => '2026-01-01 11:00:00',
    ]);
    $newerSession = MinecraftPlayerSession::create([
        'player_id' => $player->id,
        'seconds' => 600,
        'starts_at' => '2026-01-02 10:00:00',
        'ends_at' => '2026-01-02 10:10:00',
    ]);

    MinecraftPlayerSession::create([
        'player_id' => $otherPlayer->id,
        'seconds' => 1200,
        'starts_at' => '2026-01-03 10:00:00',
        'ends_at' => '2026-01-03 10:20:00',
    ]);

    $this->actingAs($manager)
        ->getJson('/manage/players/'.$player->id.'/sessions')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $newerSession->id)
        ->assertJsonPath('data.0.seconds', 600)
        ->assertJsonPath('data.1.id', $olderSession->id);

    $this->actingAs(Account::factory()->create())
        ->getJson('/manage/players/'.$player->id.'/sessions')
        ->assertForbidden();
});

it('lists audits performed by the linked account or made to the player', function () {
    config(['audit.console' => true]);

    $admin = $this->adminAccount();
    $playerAccount = Account::factory()->create();
    $player = MinecraftPlayer::factory()->for($playerAccount)->create();
    $auditToPlayer = $player->audits()->where('event', 'created')->firstOrFail();

    $this->actingAs($playerAccount);
    $serverCreatedByPlayer = Server::factory()->create(['name' => 'Created by player']);
    $auditByPlayer = $serverCreatedByPlayer->audits()->where('event', 'created')->firstOrFail();

    $this->actingAs(Account::factory()->create());
    $unrelatedServer = Server::factory()->create(['name' => 'Unrelated server']);
    $unrelatedAudit = $unrelatedServer->audits()->where('event', 'created')->firstOrFail();

    $this->actingAs($admin)
        ->getJson('/manage/players/'.$player->id.'/audits')
        ->assertOk()
        ->assertJsonFragment(['id' => $auditToPlayer->id])
        ->assertJsonFragment(['id' => $auditByPlayer->id])
        ->assertJsonMissing(['id' => $unrelatedAudit->id]);
});

it('forbids non-admins from viewing player audits', function () {
    $player = MinecraftPlayer::factory()->create();

    $this->actingAs(Account::factory()->create())
        ->getJson('/manage/players/'.$player->id.'/audits')
        ->assertForbidden();
});
