<?php

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Domains\Permissions\WebManagePermission;
use App\Domains\Permissions\WebReviewPermission;
use App\Http\Data\AdminBuilderRankApplicationData;
use App\Http\Data\AdminGameIPBanData;
use App\Models\Account;
use App\Models\Badge;
use App\Models\BuilderRankApplication;
use App\Models\Donation;
use App\Models\GameIPBan;
use App\Models\GamePlayerBan;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftWarp;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\PlayerWarning;
use App\Models\Role;
use App\Models\Server;
use App\Models\ServerToken;
use Carbon\CarbonInterface;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('serializes badge list and edit pages through the badge DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::BADGES_VIEW->value,
        WebManagePermission::BADGES_EDIT->value,
    ]);
    $badge = Badge::factory()->create(['display_name' => 'Contract Badge']);

    $this->actingAs($manager)
        ->get(route('manage.badges.edit', $badge))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Badges/BadgeEdit')
            ->where('badge.id', $badge->id)
            ->where('badge.display_name', 'Contract Badge')
            ->missing('badge.pivot')
        );

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.badges.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'Badges/BadgeList',
        'X-Inertia-Partial-Data' => 'badges',
    ])
        ->get(route('manage.badges.index'))
        ->assertOk()
        ->assertJsonPath('props.badges.data.0.id', $badge->id)
        ->assertJsonPath('props.badges.data.0.display_name', 'Contract Badge');
});

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

it('serializes warp list and edit pages through the warp DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::WARPS_VIEW->value,
        WebManagePermission::WARPS_EDIT->value,
    ]);
    $warp = MinecraftWarp::factory()->create(['name' => 'contract_warp']);

    $this->actingAs($manager)
        ->get(route('manage.minecraft.warps.edit', $warp))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Warps/WarpEdit')
            ->where('warp.id', $warp->id)
            ->where('warp.name', 'contract_warp')
        );

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.minecraft.warps.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'Warps/WarpList',
        'X-Inertia-Partial-Data' => 'warps',
    ])
        ->get(route('manage.minecraft.warps.index'))
        ->assertOk()
        ->assertJsonPath('props.warps.data.0.id', $warp->id)
        ->assertJsonPath('props.warps.data.0.name', 'contract_warp');
});

it('serializes server list and edit pages through the server DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::SERVERS_VIEW->value,
        WebManagePermission::SERVERS_EDIT->value,
    ]);
    $server = Server::factory()->create([
        'name' => 'Contract Server',
        'ip_alias' => 'play.example.test',
        'web_port' => null,
    ]);

    $this->actingAs($manager)
        ->get(route('manage.servers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Servers/ServerList')
            ->where('servers.0.id', $server->id)
            ->where('servers.0.ip_alias', 'play.example.test')
            ->where('servers.0.web_port', null)
        );

    $this->get(route('manage.servers.edit', $server))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Servers/ServerEdit')
            ->where('server.id', $server->id)
            ->where('server.name', 'Contract Server')
        );
});

it('serializes server token list and edit pages through the server token DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::SERVER_TOKENS_VIEW->value,
        WebManagePermission::SERVER_TOKENS_EDIT->value,
    ]);
    $token = ServerToken::factory()->create([
        'token' => 'contract-token',
        'description' => 'Contract credential',
        'allowed_ips' => '192.0.2.1',
    ]);

    $this->actingAs($manager)
        ->get(route('manage.server-tokens.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ServerTokens/ServerTokensList')
            ->where('tokens.0.id', $token->id)
            ->where('tokens.0.token', 'contract-token')
            ->where('tokens.0.allowed_ips', '192.0.2.1')
        );

    $this->get(route('manage.server-tokens.edit', $token))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ServerTokens/ServerTokenEdit')
            ->where('token.id', $token->id)
            ->where('token.description', 'Contract credential')
        );
});

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

it('serializes IP ban list and edit pages through the IP ban DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::IP_BANS_VIEW->value,
        WebManagePermission::IP_BANS_EDIT->value,
    ]);
    $banner = MinecraftPlayer::factory()->create(['alias' => 'ContractIPBanner']);
    $ban = GameIPBan::factory()
        ->bannedBy($banner)
        ->create([
            'ip_address' => '192.0.2.15',
            'reason' => 'Contract IP reason',
            'created_at' => '2025-01-01 00:00:00',
        ]);

    $this->actingAs($manager)
        ->get(route('manage.ip-bans.edit', $ban))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('IPBans/IPBanEdit')
            ->where('ban.id', $ban->id)
            ->where('ban.banner_player.id', $banner->id)
            ->where('ban.ip_address', '192.0.2.15')
        );

    $banWithNullLoadedBanner = GameIPBan::factory()
        ->bannedBy($banner)
        ->create([
            'created_at' => '2025-01-02 00:00:00',
            'unbanned_at' => now(),
            'unban_type' => 'manual',
        ]);
    $banWithNullLoadedBanner->setRelation('bannerPlayer', null);

    $ipBanData = AdminGameIPBanData::fromModel($banWithNullLoadedBanner);

    expect($ipBanData->banner_player)->toBeNull()
        ->and($ipBanData->unban_type?->value)->toBe('manual');

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.ip-bans.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'IPBans/IPBanList',
        'X-Inertia-Partial-Data' => 'bans',
    ])
        ->get(route('manage.ip-bans.index'))
        ->assertOk()
        ->assertJsonPath('props.bans.data.1.id', $ban->id)
        ->assertJsonPath('props.bans.data.1.banner_player.alias', 'ContractIPBanner');
});

it('serializes warning list and edit pages through the warning DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::WARNINGS_VIEW->value,
        WebManagePermission::WARNINGS_EDIT->value,
    ]);
    $warning = PlayerWarning::factory()
        ->withPlayers()
        ->create(['reason' => 'Contract warning']);
    $warning->load('warnedPlayer', 'warnerPlayer');

    $this->actingAs($manager)
        ->get(route('manage.warnings.edit', $warning))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Warnings/WarningEdit')
            ->where('warning.id', $warning->id)
            ->where('warning.warned_player.id', $warning->warned_player_id)
            ->where('warning.warner_player.id', $warning->warner_player_id)
        );

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.warnings.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'Warnings/WarningList',
        'X-Inertia-Partial-Data' => 'warnings',
    ])
        ->get(route('manage.warnings.index'))
        ->assertOk()
        ->assertJsonPath('props.warnings.data.0.id', $warning->id)
        ->assertJsonPath('props.warnings.data.0.warned_player.id', $warning->warned_player_id)
        ->assertJsonPath('props.warnings.data.0.warner_player.id', $warning->warner_player_id);
});

it('serializes role and permission pages through their DTOs', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::ROLES_VIEW->value,
        WebManagePermission::ROLES_EDIT->value,
        WebManagePermission::PERMISSIONS_ASSIGN->value,
    ]);
    $role = Role::factory()->create([
        'name' => 'contract-role',
        'role_type' => 'build',
        'display_priority' => 3,
    ]);
    $permission = Permission::create(['name' => 'web.manage.contract.view']);
    $role->permissions()->attach($permission->id);

    $this->actingAs($manager)
        ->get('/manage/roles/'.$role->id.'/edit')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Roles/RoleEdit')
            ->where('role.id', $role->id)
            ->where('role.name', 'contract-role')
        );

    $this->get('/manage/roles/'.$role->id.'/permissions')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Roles/RolePermissionSelect')
            ->where('permissions.0.id', $permission->id)
            ->where('permissions.0.name', 'web.manage.contract.view')
            ->where('role_permission_ids.0', $permission->id)
        );

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.roles.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'Roles/RoleList',
        'X-Inertia-Partial-Data' => 'roles',
    ])
        ->get(route('manage.roles.index'))
        ->assertOk()
        ->assertJsonFragment([
            'id' => $role->id,
            'name' => 'contract-role',
            'permissions_count' => 1,
        ]);
});

it('serializes role members through the account DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ROLES_VIEW->value]);
    $role = Role::factory()->create(['name' => 'contract-members']);
    $account = Account::factory()->create([
        'username' => 'contract-member',
        'email' => 'contract-member@example.test',
    ]);
    $role->accounts()->attach($account->id);

    $this->actingAs($manager)
        ->get('/manage/roles/'.$role->id.'/accounts')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Roles/RoleAccountList')
            ->where('role.id', $role->id)
            ->where('accounts.data.0.id', $account->id)
            ->where('accounts.data.0.username', 'contract-member')
            ->where('accounts.data.0.email', 'contract-member@example.test')
        );
});

it('serializes donation list and edit pages through the donation DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::DONATIONS_VIEW->value,
        WebManagePermission::DONATIONS_EDIT->value,
    ]);
    $account = Account::factory()->create([
        'username' => 'contract-donor',
        'email' => 'private-donor@example.test',
    ]);
    $payment = Payment::factory()->create([
        'original_currency' => 'aud',
        'original_unit_amount' => 2500,
        'unit_quantity' => 2,
    ]);
    $donation = Donation::factory()->create([
        'account_id' => $account->id,
        'payment_id' => $payment->id,
        'amount' => 50,
    ]);

    $this->actingAs($manager)
        ->get(route('manage.donations.edit', $donation))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Donations/DonationEdit')
            ->where('donation.id', $donation->id)
            ->where('donation.amount', 50)
        );

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.donations.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'Donations/DonationList',
        'X-Inertia-Partial-Data' => 'donations',
    ])
        ->get(route('manage.donations.index'))
        ->assertOk()
        ->assertJsonPath('props.donations.data.0.id', $donation->id)
        ->assertJsonPath('props.donations.data.0.account.username', 'contract-donor')
        ->assertJsonPath('props.donations.data.0.account.email', 'private-donor@example.test')
        ->assertJsonPath('props.donations.data.0.payment.original_unit_amount', 2500)
        ->assertJsonPath('props.donations.data.0.payment.unit_quantity', 2);
});

it('serializes permission list and edit pages through the permission DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::PERMISSIONS_VIEW->value,
        WebManagePermission::PERMISSIONS_EDIT->value,
    ]);
    $permission = Permission::create(['name' => 'web.manage.contract.permission']);

    $this->actingAs($manager)
        ->get(route('manage.permissions.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Permissions/PermissionList')
            ->where('permissions.0.id', $permission->id)
            ->where('permissions.0.name', 'web.manage.contract.permission')
        );

    $this->get('/manage/permissions/'.$permission->id.'/edit')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Permissions/PermissionEdit')
            ->where('permission.id', $permission->id)
            ->where('permission.name', 'web.manage.contract.permission')
        );
});

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

it('serializes builder rank review pages through application and role DTOs', function () {
    config(['inertia.ssr.enabled' => false]);

    $reviewer = $this->createStaffAccountWithPermissions([
        WebReviewPermission::BUILD_RANK_APPS_VIEW->value,
    ]);
    $account = Account::factory()->create(['username' => 'contract-builder']);
    $player = MinecraftPlayer::factory()->create([
        'alias' => 'ContractBuilder',
        'account_id' => $account->id,
    ]);
    $application = BuilderRankApplication::factory()->for($account)->create([
        'minecraft_alias' => 'ContractBuilder',
        'status' => ApplicationStatus::PENDING,
    ]);
    $buildRole = Role::factory()->architect()->create();

    $this->actingAs($reviewer)
        ->get(route('review.builder-ranks.show', $application))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BuilderRankApplications/BuilderRankApplicationShow')
            ->where('application.id', $application->id)
            ->where('application.status', ApplicationStatus::PENDING->value)
            ->where('application.account.minecraft_account.0.id', $player->id)
            ->where('buildRoles.0.id', $buildRole->id)
        );

    $version = $this->actingAs($reviewer)
        ->withHeader('X-Inertia', 'true')
        ->get('/review/builder-ranks')
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'BuilderRankApplications/BuilderRankApplicationList',
        'X-Inertia-Partial-Data' => 'applications',
    ])
        ->get('/review/builder-ranks')
        ->assertOk()
        ->assertJsonPath('props.applications.data.0.id', $application->id)
        ->assertJsonPath('props.applications.data.0.account.username', 'contract-builder')
        ->assertJsonPath('props.applications.data.0.account.minecraft_account.0.alias', 'ContractBuilder');
});

it('casts builder rank application closure dates before DTO construction', function () {
    $application = BuilderRankApplication::factory()
        ->for(Account::factory())
        ->approved()
        ->create()
        ->refresh();

    $data = AdminBuilderRankApplicationData::fromModel($application);

    expect($data->closed_at)->toBeInstanceOf(CarbonInterface::class);
});
