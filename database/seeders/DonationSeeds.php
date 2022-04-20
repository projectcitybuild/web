<?php

namespace Database\Seeders;

use Entities\Models\Eloquent\DonationTier;
use Entities\Models\Eloquent\StripeProduct;
use Illuminate\Database\Seeder;

class DonationSeeds extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $copperTier = DonationTier::create([
            'name' => 'copper',
            'currency_reward' => 10,
        ]);
        $ironTier = DonationTier::create([
            'name' => 'iron',
            'currency_reward' => 25,
        ]);
        $diamondTier = DonationTier::create([
            'name' => 'diamond',
            'currency_reward' => 50,
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
    }
}
