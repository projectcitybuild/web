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
            'role_id' => $copperRole->id,
        ]);

        $ironRole = Role::factory()->create([
            'name' => 'iron tier',
            'minecraft_name' => 'iron_tier',
        ]);
        $ironTier = DonationTier::create([
            'name' => 'iron',
            'role_id' => $ironRole->id,
        ]);

        $diamondRole = Role::factory()->create([
            'name' => 'diamond tier',
            'minecraft_name' => 'diamond_tier',
        ]);
        $diamondTier = DonationTier::create([
            'name' => 'diamond',
            'role_id' => $diamondRole->id,
        ]);

        // Copper subscription
        StripeProduct::create([
            'product_id' => 'prod_JxFaAltmFPewxs',
            'price_id' => 'price_1JJL5mAtUyfM4v5ISwJrrVur',
            'donation_tier_id' => $copperTier->id,
        ]);

        // Copper one-off
        StripeProduct::create([
            'product_id' => 'prod_JxFaAltmFPewxs',
            'price_id' => 'price_1JJL5mAtUyfM4v5IJNHp1Tk2',
            'donation_tier_id' => $copperTier->id,
        ]);

        // Iron subscription
        StripeProduct::create([
            'product_id' => 'prod_JxFbEy7JGmfJog',
            'price_id' => 'price_1JJL63AtUyfM4v5IoVomtPRZ',
            'donation_tier_id' => $ironTier->id,
        ]);

        // Iron one-off
        StripeProduct::create([
            'product_id' => 'prod_JxFbEy7JGmfJog',
            'price_id' => 'price_1JJL63AtUyfM4v5ILyrs2uxw',
            'donation_tier_id' => $ironTier->id,
        ]);

        // Diamond subscription
        StripeProduct::create([
            'price_id' => 'price_1JJL6RAtUyfM4v5IP77eRPER',
            'product_id' => 'prod_JxFbZQxVmr2SCu',
            'donation_tier_id' => $diamondTier->id,
        ]);

        // Diamond one-off
        StripeProduct::create([
            'price_id' => 'price_1JJL6RAtUyfM4v5Ih3kg7UDM',
            'product_id' => 'prod_JxFbZQxVmr2SCu',
            'donation_tier_id' => $diamondTier->id,
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
            'donation_id' => $donationWithPerk->id,
            'donation_tier_id' => $copperTier->id,
            'account_id' => $account->id,
            'expires_at' => now()->addDays(14),
        ]);
    }
}
