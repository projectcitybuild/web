<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Donations\Models\DonationTier;
use App\Entities\Donations\Models\MinecraftLootBox;
use App\Entities\Donations\Models\MinecraftRedeemedLootBox;
use App\Entities\Players\Models\MinecraftPlayer;
use Carbon\Carbon;
use Tests\TestCase;

class APIRedeemLootBoxTest extends TestCase
{
    public function testReturnsErrorIfPlayerDoesntExist()
    {
        $this->post('api/minecraft/xxxx-xxxx-xxxx/boxes/redeem')
            ->assertStatus(404);
    }

    public function testReturnsErrorIfAccountNotLinkedToMinecraftPlayer()
    {
        $player = MinecraftPlayer::factory()->create();

        $this->post('api/minecraft/'.$player->uuid.'/boxes/redeem')
            ->assertStatus(404);
    }

    public function testReturnsErrorIfNoPerks()
    {
        $account = Account::factory()->create();
        $player = MinecraftPlayer::factory()->create([
            'account_id' => $account->getKey(),
        ]);

        $this->post('api/minecraft/'.$player->uuid.'/boxes/redeem')
            ->assertStatus(404);
    }

    public function testReturnsNothingIfTierHasNoBoxes()
    {
        $tier = DonationTier::factory()->create();
        $account = Account::factory()->create();
        $player = MinecraftPlayer::factory()->create(['account_id' => $account->getKey()]);
        $donation = Donation::factory()->create(['account_id' => $account->getKey()]);

        DonationPerk::factory()->create([
            'account_id' => $account->getKey(),
            'donation_id' => $donation->getKey(),
            'donation_tier_id' => $tier->getKey(),
            'is_active' => true,
            'expires_at' => now()->addMonth(),
        ]);

        $this->post('api/minecraft/'.$player->uuid.'/boxes/redeem')
            ->assertStatus(404);
    }

    public function testStoresRedemption()
    {
        $tier = DonationTier::factory()->create();
        $account = Account::factory()->create();
        $player = MinecraftPlayer::factory()->create(['account_id' => $account->getKey()]);
        $donation = Donation::factory()->create(['account_id' => $account->getKey()]);
        $lootBox = MinecraftLootBox::factory()->create(['donation_tier_id' => $tier->getKey()]);

        DonationPerk::factory()->create([
            'account_id' => $account->getKey(),
            'donation_id' => $donation->getKey(),
            'donation_tier_id' => $tier->getKey(),
            'is_active' => true,
            'expires_at' => now()->addMonth(),
        ]);

        $this->post('api/minecraft/'.$player->uuid.'/boxes/redeem')
            ->assertStatus(200);

        $this->assertDatabaseHas('minecraft_redeemed_loot_boxes', [
            'account_id' => $account->getKey(),
            'minecraft_loot_box_id' => $lootBox->getKey(),
        ]);
    }

    public function testReturnsUnredeemedBoxes()
    {
        $tier = DonationTier::factory()->create();
        $account = Account::factory()->create();
        $player = MinecraftPlayer::factory()->create(['account_id' => $account->getKey()]);
        $donation = Donation::factory()->create(['account_id' => $account->getKey()]);
        $lootBox = MinecraftLootBox::factory()->create(['donation_tier_id' => $tier->getKey()]);

        DonationPerk::factory()->create([
            'account_id' => $account->getKey(),
            'donation_id' => $donation->getKey(),
            'donation_tier_id' => $tier->getKey(),
            'is_active' => true,
            'expires_at' => now()->addMonth(),
        ]);

        $this->post('api/minecraft/'.$player->uuid.'/boxes/redeem')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'redeemed_boxes' => [
                        [
                            'minecraft_loot_box_id' => $lootBox->getKey(),
                            'donation_tier_id' => $tier->getKey(),
                            'loot_box_name' => $lootBox->loot_box_name,
                            'quantity' => $lootBox->quantity,
                            'is_active' => true,
                        ],
                    ],
                ],
            ]);
    }

    public function testReturnsSecondsUntilTomorrowIfAllBoxesRedeemed()
    {
        $tier = DonationTier::factory()->create();
        $account = Account::factory()->create();
        $player = MinecraftPlayer::factory()->create(['account_id' => $account->getKey()]);
        $donation = Donation::factory()->create(['account_id' => $account->getKey()]);
        $lootBox = MinecraftLootBox::factory()->create(['donation_tier_id' => $tier->getKey()]);

        MinecraftRedeemedLootBox::create([
            'account_id' => $account->getKey(),
            'minecraft_loot_box_id' => $lootBox->getKey(),
            'created_at' => now(),
        ]);

        DonationPerk::factory()->create([
            'account_id' => $account->getKey(),
            'donation_id' => $donation->getKey(),
            'donation_tier_id' => $tier->getKey(),
            'is_active' => true,
            'expires_at' => now()->addMonth(),
        ]);

        $secsUntilTomorrow = Carbon::tomorrow()->diffInSeconds();

        $this->post('api/minecraft/'.$player->uuid.'/boxes/redeem')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'seconds_until_redeemable' => $secsUntilTomorrow,
                ],
            ]);
    }

    public function testCannotRedeemTwiceImmediately()
    {
        $tier = DonationTier::factory()->create();
        $account = Account::factory()->create();
        $player = MinecraftPlayer::factory()->create(['account_id' => $account->getKey()]);
        $donation = Donation::factory()->create(['account_id' => $account->getKey()]);
        $lootBox = MinecraftLootBox::factory()->create(['donation_tier_id' => $tier->getKey()]);

        DonationPerk::factory()->create([
            'account_id' => $account->getKey(),
            'donation_id' => $donation->getKey(),
            'donation_tier_id' => $tier->getKey(),
            'is_active' => true,
            'expires_at' => now()->addMonth(),
        ]);

        $this->post('api/minecraft/'.$player->uuid.'/boxes/redeem')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'redeemed_boxes' => [
                        [
                            'minecraft_loot_box_id' => $lootBox->getKey(),
                            'donation_tier_id' => $tier->getKey(),
                            'loot_box_name' => $lootBox->loot_box_name,
                            'quantity' => $lootBox->quantity,
                            'is_active' => true,
                        ],
                    ],
                ],
            ]);

        $secsUntilTomorrow = Carbon::tomorrow()->diffInSeconds();

        $this->post('api/minecraft/'.$player->uuid.'/boxes/redeem')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'seconds_until_redeemable' => $secsUntilTomorrow,
                ],
            ]);
    }
}
