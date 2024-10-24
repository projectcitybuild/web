<?php

use App\Models\Account;
use App\Models\Badge;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\DonationTier;
use App\Models\GamePlayerBan;
use App\Models\Group;
use App\Models\MinecraftPlayer;
use App\Models\Server;

it('requires server token', function () {
    $this->get('api/v2/minecraft/player/069a79f444e94726a5befca90e38aaf5')
        ->assertUnauthorized();

    $status = $this->withServerToken()
        ->get('api/v2/minecraft/player/069a79f444e94726a5befca90e38aaf5')
        ->status();

    expect($status)->not->toEqual(401);
});

it('throws exception for invalid Minecraft UUID', function () {
    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/invalid')
        ->assertInvalid(['uuid']);
});

it('aggregates all data', function () {
    $account = Account::factory()->create();
    $player = MinecraftPlayer::factory()
        ->for($account)
        ->create();

    $group = Group::factory()->create();
    $account->groups()->attach($group);

    $server = Server::factory()->create();
    $staffPlayer = MinecraftPlayer::factory()->create();
    $ban = GamePlayerBan::factory()
        ->bannedBy($staffPlayer)
        ->bannedPlayer($player)
        ->server($server)
        ->create();

    $badge = Badge::factory()->create();
    $account->badges()->attach($badge);

    $tier = DonationTier::factory()->create();
    $donation = Donation::factory()->create();
    $perk = DonationPerk::factory()
        ->notExpired()
        ->create([
            'account_id' => $account->getKey(),
            'donation_id' => $donation->getKey(),
            'donation_tier_id' => $tier->getKey(),
        ]);

    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/'.$player->uuid)
        ->assertJson([
            'account' => [
                'account_id' => $account->getKey(),
                'username' => $account->username,
                'last_login_at' => $account->last_login_at,
                'created_at' => $account->created_at,
                'updated_at' => $account->updated_at,
                'groups' => [
                    [
                        'group_id' => $group->getKey(),
                        'name' => $group->name,
                        'alias' => $group->alias,
                        'minecraft_name' => $group->minecraft_name,
                        'is_default' => false,
                        'is_staff' => false,
                        'is_admin' => false,
                    ],
                ],
            ],
            'ban' => [
                'id' => $ban->getKey(),
                'server_id' => $server->getKey(),
                'banned_player_id' => $player->getKey(),
                'banner_player_id' => $staffPlayer->getKey(),
                'reason' => $ban->reason,
                'expires_at' => $ban->expires_at,
                'created_at' => $ban->created_at,
                'updated_at' => $ban->updated_at,
                'unbanned_at' => null,
                'unbanner_player_id' => null,
                'unban_type' => null,
            ],
            'badges' => [
                [
                    'id' => $badge->getKey(),
                    'display_name' => $badge->display_name,
                    'unicode_icon' => $badge->unicode_icon,
                ],
            ],
            'donation_tiers' => [
                [
                    'donation_perks_id' => $perk->getKey(),
                    'is_active' => true,
                    'expires_at' => $perk->expires_at,
                    'donation_tier' => [
                        'donation_tier_id' => $tier->getKey(),
                        'name' => $tier->name,
                    ],
                ],
            ],
        ]);
});

it('shows linked player with no data', function () {
    $account = Account::factory()->create();
    $player = MinecraftPlayer::factory()
        ->for($account)
        ->create();

    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/'.$player->uuid)
        ->assertJson([
            'account' => [
                'account_id' => $account->getKey(),
                'username' => $account->username,
                'last_login_at' => $account->last_login_at,
                'created_at' => $account->created_at,
                'updated_at' => $account->updated_at,
                'groups' => [],
            ],
            'ban' => [],
            'badges' => [],
            'donation_tiers' => [],
        ]);
});

it('shows empty data for missing player', function () {
    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/069a79f444e94726a5befca90e38aaf5')
        ->assertJson([
            'account' => [],
            'ban' => [],
            'badges' => [],
            'donation_tiers' => [],
        ]);
});

it('shows empty data for unlinked account', function () {
    $player = MinecraftPlayer::factory()->create();

    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/'.$player->uuid)
        ->assertJson([
            'account' => [],
            'ban' => [],
            'badges' => [],
            'donation_tiers' => [],
        ]);
});

it('shows bans for unlinked account', function () {
    $player = MinecraftPlayer::factory()->create();

    $server = Server::factory()->create();
    $staffPlayer = MinecraftPlayer::factory()->create();
    $ban = GamePlayerBan::factory()
        ->bannedBy($staffPlayer)
        ->bannedPlayer($player)
        ->server($server)
        ->create();

    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/'.$player->uuid)
        ->assertJson([
            'account' => null,
            'ban' => [
                'id' => $ban->getKey(),
                'server_id' => $server->getKey(),
                'banned_player_id' => $player->getKey(),
                'banner_player_id' => $staffPlayer->getKey(),
                'reason' => $ban->reason,
                'expires_at' => $ban->expires_at,
                'created_at' => $ban->created_at,
                'updated_at' => $ban->updated_at,
                'unbanned_at' => null,
                'unbanner_player_id' => null,
                'unban_type' => null,
            ],
            'badges' => [],
            'donation_tiers' => [],
        ]);
});
