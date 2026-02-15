<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\DonationTier;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns 404 when player is not found', function () {
    $uuid = MinecraftUUID::random();

    $this->withServerToken()
        ->getJson('http://api.localhost/v3/players/'.$uuid.'/homes/limit')
        ->assertStatus(404)
        ->assertJson([
            'message' => 'Player not found',
        ]);
});

it('allows 1 home if no account', function () {
    $player = MinecraftPlayer::factory()->create();

    $this->withServerToken()
        ->getJson('http://api.localhost/v3/players/'.$player->uuid.'/homes/limit')
        ->assertOk()
        ->assertJson([
            'current' => 0,
            'max' => 1,
            'sources' => [],
        ]);
});

it('allows 1 home if roles do not grant additional homes', function () {
    $account = Account::factory()->create();
    $player = MinecraftPlayer::factory()->create(['account_id' => $account->getKey()]);

    $role1 = Role::factory()->create(['name' => 'trusted', 'additional_homes' => 0]);
    $account->roles()->attach($role1);

    $this->withServerToken()
        ->getJson('http://api.localhost/v3/players/'.$player->uuid.'/homes/limit')
        ->assertOk()
        ->assertJson([
            'current' => 0,
            'max' => 1,
            'sources' => [],
        ]);
});

it('composes home limit from roles', function () {
    $account = Account::factory()->create();
    $player = MinecraftPlayer::factory()->create(['account_id' => $account->getKey()]);

    $role1 = Role::factory()->create(['name' => 'trusted', 'additional_homes' => 5]);
    $role2 = Role::factory()->create(['name' => 'donor', 'additional_homes' => 6]);
    $account->roles()->attach([$role1, $role2]);

    for ($i = 0; $i < 3; $i++) {
        MinecraftHome::factory()->create(['player_id' => $player->getKey()]);
    }

    $this->withServerToken()
        ->getJson('http://api.localhost/v3/players/'.$player->uuid.'/homes/limit')
        ->assertOk()
        ->assertJson([
            'current' => 3,
            'max' => 11,
            'sources' => [
                'trusted' => 5,
                'donor' => 6,
            ],
        ]);
});

it('composes home limit from roles and donor tier roles', function () {
    $account = Account::factory()->create();
    $player = MinecraftPlayer::factory()->create(['account_id' => $account->getKey()]);

    $normalRole = Role::factory()->create(['name' => 'trusted', 'additional_homes' => 5]);
    $account->roles()->attach($normalRole);

    $donorRole = Role::factory()->create(['name' => 'donor', 'additional_homes' => 6]);
    $donorTier = DonationTier::factory()->create(['role_id' => $donorRole]);
    DonationPerk::factory()->notExpired()->create([
        'donation_id' => Donation::factory()->create()->getKey(),
        'account_id' => $account->getKey(),
        'donation_tier_id' => $donorTier->getKey(),
    ]);

    for ($i = 0; $i < 3; $i++) {
        MinecraftHome::factory()->create(['player_id' => $player->getKey()]);
    }

    $this->withServerToken()
        ->getJson('http://api.localhost/v3/players/'.$player->uuid.'/homes/limit')
        ->assertOk()
        ->assertJson([
            'current' => 3,
            'max' => 11,
            'sources' => [
                'trusted' => 5,
                'donor' => 6,
            ],
        ]);
});
