<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Payments\Models\AccountPaymentSession;
use Tests\TestCase;

class DonationTest extends TestCase
{
    public function testDonationCreateAuthenticated()
    {
        $account = factory(Account::class)->create();
        $this->actingAs($account);
        $url = action('Api\DonationController@create', ['amount' => '3.00', 'account_id' => $account->getKey()]);

        $resp = $this->getJson($url)
            ->assertOk();

        $this->assertDatabaseHas('account_payment_sessions', [
            'account_id' => $account->getKey(),
            'is_processed' => 0
        ]);
    }

    public function testDonationCreateUnauthenticated()
    {
        $url = action('Api\DonationController@create', ['amount' => '3.00']);

        $resp = $this->getJson($url)
            ->assertOk();

        $this->assertDatabaseHas('account_payment_sessions', [
            'account_id' => null,
            'is_processed' => 0
        ]);
    }
}
