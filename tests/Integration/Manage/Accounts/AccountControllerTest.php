<?php

use App\Core\Utilities\Redact;
use App\Domains\Permissions\WebManagePermission;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use App\Models\Permission;
use App\Models\Role;

function accountManagerForFilters(): Account
{
    $account = Account::factory()
        ->hasFinishedTotp()
        ->create();
    $role = Role::factory()->staff()->create();
    $account->roles()->attach($role->id);
    $role->permissions()->attach(Permission::create([
        'name' => WebManagePermission::ACCOUNTS_VIEW->value,
    ])->id);

    return $account;
}

it('filters accounts by whether they have a player', function () {
    $manager = accountManagerForFilters();
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

it('redacts account email for managers without email permission', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = accountManagerForFilters();
    $account = Account::factory()->create(['email' => 'private@example.com']);

    $this->actingAs($manager)
        ->get(route('manage.accounts.show', $account))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Accounts/AccountShow')
            ->where('account.email', Redact::email($account->email))
        );
});
