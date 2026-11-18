<?php

namespace Tests\Integration\Front;

use App\Models\Account;
use App\Models\DonationTier;
use App\Models\Role;
use App\Models\StripeProduct;

it('starts one-off donation checkout', function () {
    $product = StripeProduct::factory()
        ->for(DonationTier::factory()->for(Role::factory()))
        ->create([
            'price_id' => 'price_1JJL5mAtUyfM4v5IJNHp1Tk2',
            'product_id' => 'prod_JxFaAltmFPewxs',
        ]);

    $endpoint = 'donate/checkout';
    $payload = [
        'price_id' => $product->price_id,
    ];

    $this->actingAs(Account::factory()->create())
        ->post($endpoint, $payload)
        ->assertStatus(303);
});

it('starts subscription donation checkout', function () {
    $product = StripeProduct::factory()
        ->for(DonationTier::factory()->for(Role::factory()))
        ->create([
            'price_id' => 'price_1JJL5mAtUyfM4v5ISwJrrVur',
            'product_id' => 'prod_JxFaAltmFPewxs',
        ]);

    $endpoint = 'donate/checkout';
    $payload = [
        'price_id' => $product->price_id,
    ];

    $this->actingAs(Account::factory()->create())
        ->post($endpoint, $payload)
        ->assertStatus(303);
});
