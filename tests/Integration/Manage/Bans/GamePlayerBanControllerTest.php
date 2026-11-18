<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Permissions\WebManagePermission;
use App\Models\Account;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('filters player bans by player, reason, status, and dates', function () {
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::UUID_BANS_VIEW->value]);
    $targetPlayer = MinecraftPlayer::factory()
        ->for(Account::factory())
        ->create(['alias' => 'FilterTarget']);
    $otherPlayer = MinecraftPlayer::factory()
        ->for(Account::factory())
        ->create(['alias' => 'OtherPlayer']);
    $bannedByPlayer = MinecraftPlayer::factory()
        ->for(Account::factory())
        ->create(['alias' => 'StaffPlayer']);

    $activeBan = GamePlayerBan::factory()
        ->bannedPlayer($targetPlayer)
        ->bannedBy($bannedByPlayer)
        ->temporary()
        ->create([
            'reason' => 'Repeated chat spam',
            'created_at' => now()->startOfDay()->addHours(10),
            'expires_at' => now()->addDays(3)->setTime(12, 0),
        ]);
    $inactiveBan = GamePlayerBan::factory()
        ->bannedPlayer($targetPlayer)
        ->inactive()
        ->create([
            'reason' => 'Inappropriate language',
            'created_at' => now()->subDay(),
        ]);
    $otherBan = GamePlayerBan::factory()
        ->bannedPlayer($otherPlayer)
        ->temporary()
        ->create([
            'reason' => 'Another reason',
            'created_at' => now()->startOfDay()->addHours(9),
            'expires_at' => now()->addDays(6)->setTime(12, 0),
        ]);

    $this->actingAs($manager)
        ->getJson(route('manage.player-bans.index', ['player' => 'FilterTarget']))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['id' => $activeBan->id])
        ->assertJsonFragment(['id' => $inactiveBan->id]);

    $this->getJson(route('manage.player-bans.index', ['reason' => 'Repeated chat']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $activeBan->id);

    $this->getJson(route('manage.player-bans.index', [
        'banner_uuid' => (new MinecraftUUID($bannedByPlayer->uuid))->full(),
    ]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $activeBan->id);

    $this->getJson(route('manage.player-bans.index', ['status' => 'active']))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['id' => $activeBan->id])
        ->assertJsonFragment(['id' => $otherBan->id]);

    $this->getJson(route('manage.player-bans.index', ['status' => 'inactive']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $inactiveBan->id);

    $this->getJson(route('manage.player-bans.index', ['has_expiry' => '1']))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['id' => $activeBan->id])
        ->assertJsonFragment(['id' => $otherBan->id]);

    $this->getJson(route('manage.player-bans.index', ['has_expiry' => '0']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $inactiveBan->id);

    $this->getJson(route('manage.player-bans.index', ['created_at' => now()->toDateString()]))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['id' => $activeBan->id])
        ->assertJsonFragment(['id' => $otherBan->id]);

    $this->getJson(route('manage.player-bans.index', ['expires_at' => now()->addDays(3)->toDateString()]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $activeBan->id);
});
