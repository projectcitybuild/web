<?php

use App\Entities\Accounts\Models\Account;
use Illuminate\Support\Facades\Hash;

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(Account::class, function (Faker\Generator $faker) {
    return [
        'email' => $faker->email,
        'username' => $faker->userName,
        'password' => Hash::make("secret"),
        'last_login_ip' => $faker->ipv4,
        'last_login_at' => $faker->dateTimeBetween('-180days', '-1hours'),
    ];
});

$factory->state(Account::class, 'unhashed', [
    'password' => 'password'
]);

$factory->state(Account::class, 'with-confirm', [
    'password_confirm' => 'password'
]);

