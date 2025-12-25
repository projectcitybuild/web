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
use Illuminate\Support\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;

it('requires server token', function () {
    $this->postJson('http://api.localhost/v3/server/connection/authorize')
        ->assertUnauthorized();

    $status = $this
        ->withServerToken()
        ->postJson('http://api.localhost/v3/server/connection/authorize', [
            'uuid' => MinecraftUUID::random()->trimmed(),
        ])
        ->status();

    expect($status)->not->toEqual(401);
});

it('throws exception for invalid Minecraft UUID', function () {
    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/connection/authorize', [
            'uuid' => 'invalid',
        ])
        ->assertInvalid(['uuid']);
});

it('creates new player', function () {
    $this->freezeTime(function ($now) {
        $now->setMicroseconds(0);
        $uuid = MinecraftUUID::random()->trimmed();

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/connection/authorize', [
                'uuid' => $uuid,
                'alias' => 'foo',
            ])
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('bans', null)
                ->whereContains('player.player', [
                    'account_id' => null,
                    'uuid' => $uuid,
                    'alias' => 'foo',
                    'nickname' => null,
                    'last_seen_at' => $now->toISOString(),
                    'last_connected_at' => $now->toISOString(),
                    'created_at' => $now->toISOString(),
                    'updated_at' => $now->toISOString(),
                ])
                ->where('player.account', null)
                ->where('player.groups', [])
                ->where('player.badges', [])
                ->etc()
            );
    });
});

describe('existing player', function () {
    it('returns data for member', function () {
        $this->freezeTime(function ($now) {
            $now->setMicroseconds(0);
            $group = Group::factory()->member()->create();
            $account = Account::factory()->create();
            $player = MinecraftPlayer::factory()->for($account)->create();

            $this->withServerToken()
                ->postJson('http://api.localhost/v3/server/connection/authorize', [
                    'uuid' => $player->uuid,
                    'alias' => $player->alias,
                ])
                ->assertJson(fn (AssertableJson $json) => $json
                    ->where('bans', null)
                    ->whereContains('player.player', [
                        'account_id' => $account->getKey(),
                        'uuid' => $player->uuid,
                        'alias' => $player->alias,
                        'nickname' => $player->nickname,
                        'last_seen_at' => $now->toISOString(),
                        'last_connected_at' => $now->toISOString(),
                        'created_at' => $now->toISOString(),
                        'updated_at' => $now->toISOString(),
                    ])
                    ->whereContains('player.account', [
                        'account_id' => $account->getKey(),
                    ])
                    ->whereContains('player.groups.0', [
                        'group_id' => $group->getKey(),
                    ])
                    ->etc()
                );
        });
    });

    it('updates alias', function () {
        $this->freezeTime(function ($now) {
            $now->setMicroseconds(0);
            $account = Account::factory()->create();
            $player = MinecraftPlayer::factory()->for($account)->create();

            $this->withServerToken()
                ->postJson('http://api.localhost/v3/server/connection/authorize', [
                    'uuid' => $player->uuid,
                    'alias' => 'updated',
                ])
                ->assertJson(fn (AssertableJson $json) => $json
                    ->where('player.player.alias', 'updated')
                    ->etc()
                );
        });
    });

    it('updates last_connected_at', function () {
        $this->freezeTime(function ($now) {
            $now->setMicroseconds(0);

            $prev = now()->subWeek()->setMicroseconds(0);
            $player = MinecraftPlayer::factory()->create(['last_connected_at' => $prev]);
            expect($player->last_connected_at)->toEqual($prev);

            $this->withServerToken()
                ->postJson('http://api.localhost/v3/server/connection/authorize', [
                    'uuid' => $player->uuid,
                ]);

            $player->refresh();
            expect($player->last_connected_at)->toEqual($now);
        });
    });

    it('updates last_seen_at if no ban', function () {
        $this->freezeTime(function (Carbon $now) {
            $now->setMicroseconds(0);

            $prev = now()->subWeek()->setMicroseconds(0);
            $player = MinecraftPlayer::factory()->create(['last_seen_at' => $prev]);
            expect($player->last_seen_at)->toEqual($prev);

            $this->withServerToken()
                ->postJson('http://api.localhost/v3/server/connection/authorize', [
                    'uuid' => $player->uuid,
                ]);

            $player->refresh();
            expect($player->last_seen_at)->toEqual($now);

            // Player cannot join if banned, so last_seen_at should remain untouched
            $prev = $now->copy();
            $now->addWeek();
            GamePlayerBan::factory()->bannedPlayer($player)->create();
            $this->withServerToken()
                ->postJson('http://api.localhost/v3/server/connection/authorize', [
                    'uuid' => $player->uuid,
                ]);

            $player->refresh();
            expect($player->last_seen_at)->toEqual($prev);
        });
    });

    it('contains non-member groups', function () {
        $group = Group::factory()->create();

        $account = Account::factory()->create();
        $account->groups()->sync($group);

        $player = MinecraftPlayer::factory()->create([
            'account_id' => $account->getKey(),
        ]);

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/connection/authorize', [
                'uuid' => $player->uuid,
            ])
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('player.groups', 1)
                ->has('player.groups', 1, fn (AssertableJson $json) => $json
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

    it('contains group + donor tiers', function () {
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
            ->postJson('http://api.localhost/v3/server/connection/authorize', [
                'uuid' => $player->uuid,
            ])
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('player.groups', 2)
                ->has('player.groups.0', fn (AssertableJson $json) => $json
                    ->where('group_id', $group->getKey())
                    ->where('group_type', $group->group_type)
                    ->where('minecraft_name', $group->minecraft_name)
                    ->where('minecraft_display_name', $group->minecraft_display_name)
                    ->where('minecraft_hover_text', $group->minecraft_hover_text)
                    ->etc()
                )
                ->has('player.groups.1', fn (AssertableJson $json) => $json
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

    it('contains default group + donor tiers', function () {
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
            ->postJson('http://api.localhost/v3/server/connection/authorize', [
                'uuid' => $player->uuid,
            ])
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('player.groups', 2)
                ->has('player.groups.0', fn (AssertableJson $json) => $json
                    ->where('group_id', $group->getKey())
                    ->where('group_type', $group->group_type)
                    ->where('minecraft_name', $group->minecraft_name)
                    ->where('minecraft_display_name', $group->minecraft_display_name)
                    ->where('minecraft_hover_text', $group->minecraft_hover_text)
                    ->etc()
                )
                ->has('player.groups.1', fn (AssertableJson $json) => $json
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
            ->postJson('http://api.localhost/v3/server/connection/authorize', [
                'uuid' => $player->uuid,
            ])
            ->assertJson(fn (AssertableJson $json) => $json
                ->count('player.badges', 2)
                ->has('player.badges.0', fn (AssertableJson $json) => $json
                    ->where('id', $badge->getKey())
                    ->where('unicode_icon', $badge->unicode_icon)
                    ->where('display_name', $badge->display_name)
                    ->etc()
                )
                ->has('player.badges.1', fn (AssertableJson $json) => $json
                    ->where('unicode_icon', '⌚')
                    ->where('display_name', '0.00 years on PCB')
                    ->etc()
                )
                ->etc()
            );
    });
});

describe('banned', function () {
    it('contains uuid ban', function () {
        $player = MinecraftPlayer::factory()->create();
        $ban = GamePlayerBan::factory()
            ->bannedPlayer($player)
            ->temporary()
            ->create();

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/connection/authorize', [
                'uuid' => $player->uuid,
            ])
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('bans.uuid', fn (AssertableJson $json) => $json
                    ->where('reason', $ban->reason)
                    ->where('expires_at', $ban->expires_at->toISOString())
                    ->where('created_at', $ban->created_at->toISOString())
                    ->where('updated_at', $ban->updated_at->toISOString())
                    ->etc()
                )
                ->where('player', null)
                ->etc()
            );
    });

    it('contains ip ban', function () {
        $player = MinecraftPlayer::factory()->create();

        $ban = GameIPBan::factory()
            ->bannedBy(MinecraftPlayer::factory())
            ->create(['ip_address' => '192.168.0.1']);

        $this->withServerToken()
            ->postJson('http://api.localhost/v3/server/connection/authorize', [
                'uuid' => $player->uuid,
                'ip' => '192.168.0.1',
            ])
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('bans.ip', fn (AssertableJson $json) => $json
                    ->where('ip_address', $ban->ip_address)
                    ->where('reason', $ban->reason)
                    ->where('created_at', $ban->created_at->toISOString())
                    ->where('updated_at', $ban->updated_at->toISOString())
                    ->etc()
                )
                ->where('player', null)
                ->etc()
            );
    });
});
