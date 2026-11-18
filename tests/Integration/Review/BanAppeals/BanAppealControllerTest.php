<?php

use App\Core\Utilities\Redact;
use App\Domains\Permissions\WebReviewPermission;
use App\Models\Account;
use App\Models\BanAppeal;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Testing\Fluent\AssertableJson as Assert;

it('renders loaded ban appeal data and a redacted linked account', function () {
    config(['inertia.ssr.enabled' => false]);

    $reviewer = Account::factory()->hasFinishedTotp()->create();
    $role = Role::factory()->staff()->create();
    $role->permissions()->attach(Permission::create([
        'name' => WebReviewPermission::BAN_APPEALS_VIEW->value,
    ])->id);
    $reviewer->roles()->attach($role->id);

    $account = Account::factory()->create(['email' => 'private@example.com']);
    $banAppeal = BanAppeal::factory()->for($account)->create([
        'created_at' => '2025-01-02 03:04:05',
        'decided_at' => null,
    ]);

    $this->actingAs($reviewer)
        ->get(route('review.ban-appeals.show', $banAppeal))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BanAppeals/BanAppealShow')
            ->where('banAppeal.id', $banAppeal->id)
            ->where('banAppeal.account.email', Redact::email('private@example.com'))
            ->where('banAppeal.game_player_ban', null)
            ->where('banAppeal.decider_player', null)
            ->where('banAppeal.created_at', '2025-01-02T03:04:05.000000Z')
            ->etc()
        );
});

it('renders a typed nested player ban and its loaded players', function () {
    config(['inertia.ssr.enabled' => false]);

    $reviewer = Account::factory()->hasFinishedTotp()->create();
    $role = Role::factory()->staff()->create();
    $role->permissions()->attach(Permission::create([
        'name' => WebReviewPermission::BAN_APPEALS_VIEW->value,
    ])->id);
    $reviewer->roles()->attach($role->id);

    $bannedPlayer = MinecraftPlayer::factory()->create();
    $bannerPlayer = MinecraftPlayer::factory()->create();
    $ban = GamePlayerBan::factory()
        ->bannedPlayer($bannedPlayer)
        ->bannedBy($bannerPlayer)
        ->create(['reason' => 'Griefing']);
    $appeal = BanAppeal::factory()->create(['game_ban_id' => $ban->id]);

    $this->actingAs($reviewer)
        ->get(route('review.ban-appeals.show', $appeal))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BanAppeals/BanAppealShow')
            ->where('banAppeal.game_player_ban.id', $ban->id)
            ->where('banAppeal.game_player_ban.reason', 'Griefing')
            ->where('banAppeal.game_player_ban.banned_player.id', $bannedPlayer->id)
            ->where('banAppeal.game_player_ban.banner_player.id', $bannerPlayer->id)
            ->where('banAppeal.status', 0)
        );
});

it('resolves deferred ban appeal data through the page DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $reviewer = Account::factory()->hasFinishedTotp()->create();
    $role = Role::factory()->staff()->create();
    $role->permissions()->attach(Permission::create([
        'name' => WebReviewPermission::BAN_APPEALS_VIEW->value,
    ])->id);
    $reviewer->roles()->attach($role->id);

    $player = MinecraftPlayer::factory()->create(['alias' => 'ContractPlayer']);
    $ban = GamePlayerBan::factory()->bannedPlayer($player)->create();
    $appeal = BanAppeal::factory()->create([
        'game_ban_id' => $ban->id,
        'unban_reason' => 'I have appealed this ban.',
    ]);
    $version = $this->actingAs($reviewer)
        ->withHeader('X-Inertia', 'true')
        ->get(route('review.ban-appeals.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'BanAppeals/BanAppealList',
        'X-Inertia-Partial-Data' => 'banAppeals',
    ])
        ->get(route('review.ban-appeals.index'))
        ->assertOk()
        ->assertJsonPath('component', 'BanAppeals/BanAppealList')
        ->assertJsonPath('props.banAppeals.data.0.id', $appeal->id)
        ->assertJsonPath('props.banAppeals.data.0.unban_reason', 'I have appealed this ban.')
        ->assertJsonPath('props.banAppeals.data.0.game_player_ban.banned_player.alias', 'ContractPlayer');
});
