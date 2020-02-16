<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationPerk;

$factory->define(DonationPerk::class, function (Faker\Generator $faker) {
    $donation = factory(Donation::class)->create();

    return [
        'donation_id' => $donation->getKey(),
        'is_active' => true,
        'is_lifetime_perks' => true,
        'expires_at' => $faker->dateTime(),
        'created_at' => $faker->dateTime(),
        'updated_at' => $faker->dateTime(),
    ];
});