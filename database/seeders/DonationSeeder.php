<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\DonationTier;
use App\Models\Payment;
use App\Models\Role;
use App\Models\StripeProduct;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    public function run()
    {
        $copperRole = Role::factory()->create([
            'name' => 'copper tier',
            'minecraft_name' => 'copper_tier',
        ]);
        $copperTier = DonationTier::create([
            'name' => 'copper',
            'role_id' => $copperRole->getKey(),
        ]);

        $ironRole = Role::factory()->create([
            'name' => 'iron tier',
            'minecraft_name' => 'iron_tier',
        ]);
        $ironTier = DonationTier::create([
            'name' => 'iron',
            'role_id' => $ironRole->getKey(),
        ]);

        $diamondRole = Role::factory()->create([
            'name' => 'diamond tier',
            'minecraft_name' => 'diamond_tier',
        ]);
        $diamondTier = DonationTier::create([
            'name' => 'diamond',
            'role_id' => $diamondRole->getKey(),
        ]);

        // Copper subscription
        StripeProduct::create([
            'product_id' => 'prod_JxFaAltmFPewxs',
            'price_id' => 'price_1JJL5mAtUyfM4v5ISwJrrVur',
            'donation_tier_id' => $copperTier->getKey(),
        ]);

        // Copper one-off
        StripeProduct::create([
            'product_id' => 'prod_JxFaAltmFPewxs',
            'price_id' => 'price_1JJL5mAtUyfM4v5IJNHp1Tk2',
            'donation_tier_id' => $copperTier->getKey(),
        ]);

        // Iron subscription
        StripeProduct::create([
            'product_id' => 'prod_JxFbEy7JGmfJog',
            'price_id' => 'price_1JJL63AtUyfM4v5IoVomtPRZ',
            'donation_tier_id' => $ironTier->getKey(),
        ]);

        // Iron one-off
        StripeProduct::create([
            'product_id' => 'prod_JxFbEy7JGmfJog',
            'price_id' => 'price_1JJL63AtUyfM4v5ILyrs2uxw',
            'donation_tier_id' => $ironTier->getKey(),
        ]);

        // Diamond subscription
        StripeProduct::create([
            'price_id' => 'price_1JJL6RAtUyfM4v5IP77eRPER',
            'product_id' => 'prod_JxFbZQxVmr2SCu',
            'donation_tier_id' => $diamondTier->getKey(),
        ]);

        // Diamond one-off
        StripeProduct::create([
            'price_id' => 'price_1JJL6RAtUyfM4v5Ih3kg7UDM',
            'product_id' => 'prod_JxFbZQxVmr2SCu',
            'donation_tier_id' => $diamondTier->getKey(),
        ]);

        $account = Account::where('email', 'admin@pcbmc.co')->first();

        Donation::factory()
            ->for(Payment::factory()->for($account)->create())
            ->for($account)
            ->create(['created_at' => now()]);

        Donation::factory()
            ->for(Payment::factory()->for($account)->create())
            ->for($account)
            ->create();

        $donationWithPerk = Donation::factory()
            ->for(Payment::factory()->for($account)->create())
            ->for($account)
            ->create();

        DonationPerk::factory()->create([
            'donation_id' => $donationWithPerk->getKey(),
            'donation_tier_id' => $copperTier->getKey(),
            'account_id' => $account->getKey(),
            'expires_at' => now()->addDays(14),
        ]);
    }
}
