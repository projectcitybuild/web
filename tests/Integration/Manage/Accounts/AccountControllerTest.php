<?php

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
