<?php

namespace Tests\Integration\API;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\Badge;
use Entities\Models\Eloquent\Donation;
use Entities\Models\Eloquent\DonationPerk;
use Entities\Models\Eloquent\DonationTier;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\Group;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIMinecraftAggregateTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private function endpoint(?MinecraftPlayer $player): string
    {
        $uuid = $player?->uuid ?? 'invalid';

        return 'api/v2/minecraft/'.$uuid.'/aggregate';
    }

    public function test_aggregates_all_data()
    {
        $account = Account::factory()->create();
        $player = MinecraftPlayer::factory()
            ->for($account)
            ->create();

        $group = Group::factory()->create();
        $account->groups()->attach($group);

        $server = Server::factory()->create();
        $staffPlayer = MinecraftPlayer::factory()->create();
        $ban = GameBan::factory()
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

        $this->getJson($this->endpoint($player))
            ->assertJson([
                'data' => [
                    'account' => [
                        'account_id' => $account->getKey(),
                        'username' => $account->username,
                        'last_login_at' => $account->last_login_at->timestamp,
                        'created_at' => $account->created_at->timestamp,
                        'updated_at' => $account->updated_at->timestamp,
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
                        'created_at' => $ban->created_at->timestamp,
                        'updated_at' => $ban->updated_at->timestamp,
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
                            'expires_at' => $perk->expires_at->timestamp,
                            'donation_tier' => [
                                'donation_tier_id' => $tier->getKey(),
                                'name' => $tier->name,
                            ],
                        ],
                    ],
                ],
            ]);
    }

    public function test_linked_player_with_no_data()
    {
        $account = Account::factory()->create();
        $player = MinecraftPlayer::factory()
            ->for($account)
            ->create();

        $this->getJson($this->endpoint($player))
            ->assertJson([
                'data' => [
                    'account' => [
                        'account_id' => $account->getKey(),
                        'username' => $account->username,
                        'last_login_at' => $account->last_login_at->timestamp,
                        'created_at' => $account->created_at->timestamp,
                        'updated_at' => $account->updated_at->timestamp,
                        'groups' => [],
                    ],
                    'ban' => [],
                    'badges' => [],
                    'donation_tiers' => [],
                ],
            ]);
    }

    public function test_missing_player()
    {
        $this->getJson($this->endpoint(null))
            ->assertJson([
                'data' => [
                    'account' => [],
                    'ban' => [],
                    'badges' => [],
                    'donation_tiers' => [],
                    'warnings' => [],
                ],
            ]);
    }

    public function test_unlinked_account()
    {
        $player = MinecraftPlayer::factory()->create();

        $this->getJson($this->endpoint($player))
            ->assertJson([
                'data' => [
                    'account' => [],
                    'ban' => [],
                    'badges' => [],
                    'donation_tiers' => [],
                ],
            ]);
    }

    public function test_banned_unlinked_account()
    {
        $player = MinecraftPlayer::factory()->create();

        $server = Server::factory()->create();
        $staffPlayer = MinecraftPlayer::factory()->create();
        $ban = GameBan::factory()
            ->bannedBy($staffPlayer)
            ->bannedPlayer($player)
            ->server($server)
            ->create();

        $this->getJson($this->endpoint($player))
            ->assertJson([
                'data' => [
                    'account' => null,
                    'ban' => [
                        'id' => $ban->getKey(),
                        'server_id' => $server->getKey(),
                        'banned_player_id' => $player->getKey(),
                        'banner_player_id' => $staffPlayer->getKey(),
                        'reason' => $ban->reason,
                        'expires_at' => $ban->expires_at,
                        'created_at' => $ban->created_at->timestamp,
                        'updated_at' => $ban->updated_at->timestamp,
                        'unbanned_at' => null,
                        'unbanner_player_id' => null,
                        'unban_type' => null,
                    ],
                    'badges' => [],
                    'donation_tiers' => [],
                ],
            ]);
    }
}
