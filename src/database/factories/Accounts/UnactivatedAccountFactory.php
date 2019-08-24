<?php

use App\Entities\Accounts\Models\UnactivatedAccount;
use Illuminate\Support\Facades\Hash;

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(UnactivatedAccount::class, function (Faker\Generator $faker) {
    return [
        'email' => $faker->email,
        'username' => $faker->userName,
        'password' => Hash::make("password")
    ];
});

$factory->state(UnactivatedAccount::class, 'unhashed', [
    'password' => 'password'
]);

$factory->state(UnactivatedAccount::class, 'with-confirm', [
    'password_confirm' => 'password'
]);
