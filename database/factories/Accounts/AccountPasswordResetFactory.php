<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Entities\Accounts\Models\Account;
use App\Entities\Accounts\Models\AccountPasswordReset;
use App\Helpers\TokenHelpers;
use Faker\Generator as Faker;

$factory->define(AccountPasswordReset::class, function (Faker $faker) {
    $account = factory(Account::class)->create();
    $token = TokenHelpers::generateToken();
    return [
        'email' => $account->email,
        'token' => $token
    ];
});
