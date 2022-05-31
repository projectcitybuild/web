<?php

namespace Tests\E2E\API;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\DonationTier;
use Entities\Models\Eloquent\Group;
use Entities\Models\Eloquent\StripeProduct;
use Entities\Notifications\DonationPerkStartedNotification;
use Illuminate\Support\Facades\Notification;
use Tests\E2ETestCase;

class DonationCheckoutTest extends E2ETestCase
{
    private const ENDPOINT = 'donate/checkout';

    public function test_one_off_donation_checkout()
    {
        $product = StripeProduct::create([
            'price_id' => 'price_1JJL5mAtUyfM4v5IJNHp1Tk2',
            'product_id' => 'prod_JxFaAltmFPewxs',
            'donation_tier_id' => null,
        ]);

        $payload = [
            'price_id' => $product->price_id,
        ];

        $this->actingAs(Account::factory()->create())
            ->post(self::ENDPOINT, $payload)
            ->assertStatus(302);
    }

    public function test_subscription_donation_checkout()
    {
        $product = StripeProduct::create([
            'price_id' => 'price_1JJL5mAtUyfM4v5ISwJrrVur',
            'product_id' => 'prod_JxFaAltmFPewxs',
            'donation_tier_id' => null,
        ]);

        $payload = [
            'price_id' => $product->price_id,
        ];

        $this->actingAs(Account::factory()->create())
            ->post(self::ENDPOINT, $payload)
            ->assertStatus(302);
    }
}
