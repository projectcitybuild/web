<?php

use App\Core\Utilities\Redact;
use App\Domains\Permissions\WebManagePermission;
use App\Models\Account;
use App\Models\ExternalAccount;
use App\Models\MinecraftPlayer;
use App\Models\Role;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('filters accounts by whether they have a player', function () {
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ACCOUNTS_VIEW->value]);
    $accountWithPlayer = Account::factory()->create();
    $accountWithoutPlayer = Account::factory()->create();
    MinecraftPlayer::factory()->create(['account_id' => $manager->id]);
    MinecraftPlayer::factory()->create(['account_id' => $accountWithPlayer->id]);

    $this->actingAs($manager)
        ->getJson(route('manage.accounts.index', ['has_player' => '1']))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['id' => $accountWithPlayer->id]);

    $this->getJson(route('manage.accounts.index', ['has_player' => '0']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $accountWithoutPlayer->id);
});

it('sorts the account list by an allowed column and direction', function () {
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ACCOUNTS_VIEW->value]);
    $alpha = Account::factory()->create(['username' => 'Sort Alpha']);
    $zulu = Account::factory()->create(['username' => 'Sort Zulu']);

    $this->actingAs($manager)
        ->getJson(route('manage.accounts.index', ['username' => 'Sort', 'sort' => 'username', 'direction' => 'asc']))
        ->assertOk()
        ->assertJsonPath('data.0.id', $alpha->id)
        ->assertJsonPath('data.1.id', $zulu->id);

    $this->getJson(route('manage.accounts.index', ['username' => 'Sort', 'sort' => 'username', 'direction' => 'desc']))
        ->assertOk()
        ->assertJsonPath('data.0.id', $zulu->id)
        ->assertJsonPath('data.1.id', $alpha->id);
});

it('preserves filters and sorting in account pagination URLs', function () {
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ACCOUNTS_VIEW->value]);
    foreach (range(0, 50) as $index) {
        Account::factory()->create(['username' => sprintf('Paginate %03d', $index)]);
    }

    $query = ['username' => 'Paginate', 'sort' => 'username', 'direction' => 'asc'];
    $this->actingAs($manager)
        ->getJson(route('manage.accounts.index', $query))
        ->assertOk()
        ->assertJsonPath('total', 51)
        ->assertJsonPath('next_page_url', route('manage.accounts.index', [...$query, 'page' => 2]));

    $this->getJson(route('manage.accounts.index', [...$query, 'page' => 2]))
        ->assertOk()
        ->assertJsonPath('data.0.username', 'Paginate 050');
});

it('uses default sorting for missing or invalid sort directions', function () {
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ACCOUNTS_VIEW->value]);
    Account::factory()->create([
        'username' => 'Sort Alpha',
        'created_at' => now()->subDay(),
    ]);
    $zulu = Account::factory()->create([
        'username' => 'Sort Zulu',
        'created_at' => now(),
    ]);

    $this->actingAs($manager)
        ->getJson(route('manage.accounts.index', ['username' => 'Sort', 'sort' => 'username', 'direction' => 'sideways']))
        ->assertOk()
        ->assertJsonPath('data.0.id', $zulu->id);

    $this->getJson(route('manage.accounts.index', ['username' => 'Sort', 'sort' => 'username']))
        ->assertOk()
        ->assertJsonPath('data.0.id', $zulu->id);

    $this->getJson(route('manage.accounts.index', ['username' => 'Sort', 'sort' => 'unknown', 'direction' => 'asc']))
        ->assertOk()
        ->assertJsonPath('data.0.id', $zulu->id);
});

it('does not sort accounts by redacted email addresses', function () {
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ACCOUNTS_VIEW->value]);
    $latest = Account::factory()->create([
        'username' => 'Sort Latest',
        'email' => 'zulu@example.test',
        'created_at' => now(),
    ]);
    Account::factory()->create([
        'username' => 'Sort Earlier',
        'email' => 'alpha@example.test',
        'created_at' => now()->subDay(),
    ]);

    $this->actingAs($manager)
        ->getJson(route('manage.accounts.index', ['username' => 'Sort', 'sort' => 'email', 'direction' => 'asc']))
        ->assertOk()
        ->assertJsonPath('data.0.id', $latest->id);
});

it('redacts account email for managers without email permission', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ACCOUNTS_VIEW->value]);
    $account = Account::factory()->create(['email' => 'private@example.com']);

    $this->actingAs($manager)
        ->get(route('manage.accounts.show', $account))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Accounts/AccountShow')
            ->where('account.email', Redact::email($account->email))
            ->where('account.username', $account->username)
            ->missing('account.name')
            ->where('is_admin', false)
            ->where('permissions', [WebManagePermission::ACCOUNTS_VIEW->value])
            ->where('pending_appeals', 0)
            ->where('pending_build_rank_apps', 0)
            ->where('success', null)
        );
});

it('shows account email to managers with email permission', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::ACCOUNTS_VIEW->value,
        WebManagePermission::ACCOUNTS_VIEW_EMAIL->value,
    ]);
    $account = Account::factory()->create(['email' => 'private@example.com']);

    $this->actingAs($manager)
        ->get(route('manage.accounts.show', $account))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Accounts/AccountShow')
            ->where('account.email', 'private@example.com')
            ->where('permissions', [
                WebManagePermission::ACCOUNTS_VIEW->value,
                WebManagePermission::ACCOUNTS_VIEW_EMAIL->value,
            ])
        );
});

it('shows a linked Discord account in account details', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ACCOUNTS_VIEW->value]);
    $account = Account::factory()->create();
    $discordAccount = ExternalAccount::factory()->create([
        'account_id' => $account->id,
        'provider_user_id' => '123456789012345678',
        'username' => 'builder',
    ]);

    $this->actingAs($manager)
        ->get(route('manage.accounts.show', $account))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Accounts/AccountShow')
            ->where('account.discord_account.provider_user_id', $discordAccount->provider_user_id)
            ->where('account.discord_account.username', $discordAccount->username)
        );
});

it('serializes loaded account relations as typed objects', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ACCOUNTS_VIEW->value]);
    $account = Account::factory()->create();
    $role = Role::factory()->staff()->create();
    $account->roles()->attach($role->id);
    $player = MinecraftPlayer::factory()->create(['account_id' => $account->id]);

    $this->actingAs($manager)
        ->get(route('manage.accounts.show', $account))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Accounts/AccountShow')
            ->where('account.roles.0.id', $role->id)
            ->where('account.roles.0.name', $role->name)
            ->where('account.minecraft_account.0.id', $player->id)
            ->where('account.minecraft_account.0.uuid', $player->uuid)
        );
});

it('renders account edit data through the account DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ACCOUNTS_EDIT->value]);
    $account = Account::factory()->create(['email' => 'edit@example.com']);

    $this->actingAs($manager)
        ->get(route('manage.accounts.edit', $account))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Accounts/AccountEdit')
            ->where('account.id', $account->id)
            ->where('account.email', 'edit@example.com')
            ->where('account.username', $account->username)
        );
});
