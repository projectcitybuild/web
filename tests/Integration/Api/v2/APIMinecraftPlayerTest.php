<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\Account;
use App\Models\Badge;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\DonationTier;
use App\Models\GameIPBan;
use App\Models\GamePlayerBan;
use App\Models\Group;
use App\Models\MinecraftPlayer;
use Illuminate\Testing\Fluent\AssertableJson;

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

it('returns empty data set for unknown player', function () {
    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/'.MinecraftUUID::random())
        ->assertJson([
            'account' => null,
            'player' => null,
            'groups' => [],
            'ban' => null,
            'badges' => [],
            'ip_ban' => null,
        ]);
});

it('contains player data', function () {
    $this->freezeTime(function ($now) {
        $now->setMicroseconds(0);

        $player = MinecraftPlayer::factory()->create();

        $this->withServerToken()
            ->getJson('api/v2/minecraft/player/'.$player->uuid)
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('player', [
                    'account_id' => null,
                    'player_minecraft_id' => $player->getKey(),
                    'uuid' => $player->uuid,
                    'alias' => $player->alias,
                    'nickname' => $player->nickname,
                    'last_seen_at' => $player->last_seen_at->toISOString(),
                    'last_connected_at' => null,
                    'last_synced_at' => $now->toISOString(),
                    'created_at' => $now->toISOString(),
                    'updated_at' => $now->toISOString(),
                ])
                ->etc()
            );
    });
});

it('contains account', function () {
    $account = Account::factory()->create();
    $player = MinecraftPlayer::factory()->create([
        'account_id' => $account->getKey(),
    ]);

    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/'.$player->uuid)
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('account', fn (AssertableJson $json) => $json
                ->where('account_id', $account->getKey())
                ->where('username', $account->username)
                ->where('activated', $account->activated)
                ->where('email', $account->email)
                ->etc()
            )
            ->etc()
        );
});

it('contains groups', function () {
    $group = Group::factory()->create();

    $account = Account::factory()->create();
    $account->groups()->sync($group);

    $player = MinecraftPlayer::factory()->create([
        'account_id' => $account->getKey(),
    ]);

    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/'.$player->uuid)
        ->assertJson(fn (AssertableJson $json) => $json
            ->count('groups', 1)
            ->has('groups', 1, fn (AssertableJson $json) => $json
                ->where('group_id', $group->getKey())
                ->where('group_type', $group->group_type)
                ->where('minecraft_name', $group->minecraft_name)
                ->where('minecraft_display_name', $group->minecraft_display_name)
                ->where('minecraft_hover_text', $group->minecraft_hover_text)
                ->etc()
            )
            ->etc()
        );
});

it('contains default group if no groups', function () {
    $group = Group::factory()->member()->create();
    $account = Account::factory()->create();

    $player = MinecraftPlayer::factory()->create([
        'account_id' => $account->getKey(),
    ]);

    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/'.$player->uuid)
        ->assertJson(fn (AssertableJson $json) => $json
            ->count('groups', 1)
            ->has('groups', 1, fn (AssertableJson $json) => $json
                ->where('group_id', $group->getKey())
                ->where('group_type', $group->group_type)
                ->where('minecraft_name', $group->minecraft_name)
                ->where('minecraft_display_name', $group->minecraft_display_name)
                ->where('minecraft_hover_text', $group->minecraft_hover_text)
                ->etc()
            )
            ->etc()
        );
});

it('contains donor tiers as groups', function () {
    $group = Group::factory()->create();
    $account = Account::factory()->create();
    $account->groups()->sync($group);
    $player = MinecraftPlayer::factory()->for($account)->create();

    $donorTierGroup = Group::factory()->create();
    $donorTier = DonationTier::factory()->for($donorTierGroup)->create();
    $donation = Donation::factory()->create();

    DonationPerk::factory()
        ->notExpired()
        ->for($donation)
        ->for($donorTier)
        ->for($account)
        ->create();

    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/'.$player->uuid)
        ->assertJson(fn (AssertableJson $json) => $json
            ->count('groups', 2)
            ->has('groups.0', fn (AssertableJson $json) => $json
                ->where('group_id', $group->getKey())
                ->where('group_type', $group->group_type)
                ->where('minecraft_name', $group->minecraft_name)
                ->where('minecraft_display_name', $group->minecraft_display_name)
                ->where('minecraft_hover_text', $group->minecraft_hover_text)
                ->etc()
            )
            ->has('groups.1', fn (AssertableJson $json) => $json
                ->where('group_id', $donorTierGroup->getKey())
                ->where('group_type', $donorTierGroup->group_type)
                ->where('minecraft_name', $donorTierGroup->minecraft_name)
                ->where('minecraft_display_name', $donorTierGroup->minecraft_display_name)
                ->where('minecraft_hover_text', $donorTierGroup->minecraft_hover_text)
                ->etc()
            )
            ->etc()
        );
});

it('contains donor tiers and default group', function () {
    $group = Group::factory()->member()->create();
    $account = Account::factory()->create();
    $player = MinecraftPlayer::factory()->for($account)->create();

    $donorTierGroup = Group::factory()->create();
    $donorTier = DonationTier::factory()->for($donorTierGroup)->create();
    $donation = Donation::factory()->create();

    DonationPerk::factory()
        ->notExpired()
        ->for($donation)
        ->for($donorTier)
        ->for($account)
        ->create();

    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/'.$player->uuid)
        ->assertJson(fn (AssertableJson $json) => $json
            ->count('groups', 2)
            ->has('groups.0', fn (AssertableJson $json) => $json
                ->where('group_id', $group->getKey())
                ->where('group_type', $group->group_type)
                ->where('minecraft_name', $group->minecraft_name)
                ->where('minecraft_display_name', $group->minecraft_display_name)
                ->where('minecraft_hover_text', $group->minecraft_hover_text)
                ->etc()
            )
            ->has('groups.1', fn (AssertableJson $json) => $json
                ->where('group_id', $donorTierGroup->getKey())
                ->where('group_type', $donorTierGroup->group_type)
                ->where('minecraft_name', $donorTierGroup->minecraft_name)
                ->where('minecraft_display_name', $donorTierGroup->minecraft_display_name)
                ->where('minecraft_hover_text', $donorTierGroup->minecraft_hover_text)
                ->etc()
            )
            ->etc()
        );
});

it('contains badges', function () {
    $badge = Badge::factory()->create();
    $account = Account::factory()->create();
    $account->badges()->sync($badge);

    $player = MinecraftPlayer::factory()->create([
        'account_id' => $account->getKey(),
    ]);

    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/'.$player->uuid)
        ->assertJson(fn (AssertableJson $json) => $json
            ->count('badges', 2)
            ->has('badges.0', fn (AssertableJson $json) => $json
                ->where('id', $badge->getKey())
                ->where('unicode_icon', $badge->unicode_icon)
                ->where('display_name', $badge->display_name)
                ->etc()
            )
            ->has('badges.1', fn (AssertableJson $json) => $json
                ->where('unicode_icon', '⌚')
                ->where('display_name', '0.00 years on PCB')
                ->etc()
            )
            ->etc()
        );
});

it('contains uuid ban', function () {
    $player = MinecraftPlayer::factory()->create();
    $ban = GamePlayerBan::factory()
        ->bannedPlayer($player)
        ->temporary()
        ->create();

    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/'.$player->uuid)
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('ban', fn (AssertableJson $json) => $json
                ->where('reason', $ban->reason)
                ->where('expires_at', $ban->expires_at->toISOString())
                ->where('created_at', $ban->created_at->toISOString())
                ->where('updated_at', $ban->updated_at->toISOString())
                ->etc()
            )
            ->etc()
        );
});

it('contains ip ban', function () {
    $player = MinecraftPlayer::factory()->create();

    $ban = GameIPBan::factory()
        ->bannedBy(MinecraftPlayer::factory())
        ->create(['ip_address' => '192.168.0.1']);

    $this->withServerToken()
        ->getJson('api/v2/minecraft/player/'.$player->uuid.'?ip=192.168.0.1')
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('ip_ban', fn (AssertableJson $json) => $json
                ->where('ip_address', $ban->ip_address)
                ->where('reason', $ban->reason)
                ->where('created_at', $ban->created_at->toISOString())
                ->where('updated_at', $ban->updated_at->toISOString())
                ->etc()
            )
            ->etc()
        );
});
