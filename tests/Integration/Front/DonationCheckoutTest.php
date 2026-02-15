<?php

namespace Tests\Integration\Front;

use App\Models\Account;
use App\Models\DonationTier;
use App\Models\Role;
use App\Models\StripeProduct;
use Tests\IntegrationTestCase;

class DonationCheckoutTest extends IntegrationTestCase
{
    private const ENDPOINT = 'donate/checkout';

    public function test_one_off_donation_checkout()
    {
        $product = StripeProduct::factory()
            ->for(DonationTier::factory()->for(Role::factory()))
            ->create([
                'price_id' => 'price_1JJL5mAtUyfM4v5IJNHp1Tk2',
                'product_id' => 'prod_JxFaAltmFPewxs',
            ]);

        $payload = [
            'price_id' => $product->price_id,
        ];

        $this->actingAs(Account::factory()->create())
            ->post(self::ENDPOINT, $payload)
            ->assertStatus(303);
    }

    public function test_subscription_donation_checkout()
    {
        $product = StripeProduct::factory()
            ->for(DonationTier::factory()->for(Role::factory()))
            ->create([
                'price_id' => 'price_1JJL5mAtUyfM4v5ISwJrrVur',
                'product_id' => 'prod_JxFaAltmFPewxs',
            ]);

        $payload = [
            'price_id' => $product->price_id,
        ];

        $this->actingAs(Account::factory()->create())
            ->post(self::ENDPOINT, $payload)
            ->assertStatus(303);
    }
}
