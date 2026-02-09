<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\DonationTier;
use App\Models\Group;
use App\Models\MinecraftHome;
use App\Models\MinecraftPlayer;
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

it('allows 1 home if groups do not grant additional homes', function () {
    $account = Account::factory()->create();
    $player = MinecraftPlayer::factory()->create(['account_id' => $account->getKey()]);

    $group1 = Group::factory()->create(['name' => 'trusted', 'additional_homes' => 0]);
    $account->groups()->attach($group1);

    $this->withServerToken()
        ->getJson('http://api.localhost/v3/players/'.$player->uuid.'/homes/limit')
        ->assertOk()
        ->assertJson([
            'current' => 0,
            'max' => 1,
            'sources' => [],
        ]);
});

it('composes home limit from groups', function () {
    $account = Account::factory()->create();
    $player = MinecraftPlayer::factory()->create(['account_id' => $account->getKey()]);

    $group1 = Group::factory()->create(['name' => 'trusted', 'additional_homes' => 5]);
    $group2 = Group::factory()->create(['name' => 'donor', 'additional_homes' => 6]);
    $account->groups()->attach([$group1, $group2]);

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

it('composes home limit from groups and donor tier groups', function () {
    $account = Account::factory()->create();
    $player = MinecraftPlayer::factory()->create(['account_id' => $account->getKey()]);

    $normalGroup = Group::factory()->create(['name' => 'trusted', 'additional_homes' => 5]);
    $account->groups()->attach($normalGroup);

    $donorGroup = Group::factory()->create(['name' => 'donor', 'additional_homes' => 6]);
    $donorTier = DonationTier::factory()->create(['group_id' => $donorGroup]);
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
