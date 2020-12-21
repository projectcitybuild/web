<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Library\Stripe\StripeHandler;
use Mockery\MockInterface;
use Tests\TestCase;

class DonationTest extends TestCase
{
    public function testDonationCreateAuthenticated()
    {
        $account = factory(Account::class)->create();
        $this->actingAs($account);

        $this->mock(StripeHandler::class, function (MockInterface $mock) {
            $mock->shouldReceive('createCheckoutSession')->once()->andReturn('abc123');
        });

        $url = action('Api\DonationController@create', ['amount' => '3.00', 'account_id' => $account->getKey()]);

        $resp = $this->getJson($url)
            ->assertOk();

        $this->assertDatabaseHas('account_payment_sessions', [
            'account_id' => $account->getKey(),
            'is_processed' => 0
        ]);

        $resp->assertJson(['data' => ['session_id' => 'abc123']]);
    }

    public function testDonationCreateUnauthenticated()
    {
        $url = action('Api\DonationController@create', ['amount' => '3.00']);

        $this->mock(StripeHandler::class, function (MockInterface $mock) {
            $mock->shouldReceive('createCheckoutSession')->once()->andReturn('abc123');
        });

        $resp = $this->getJson($url)
            ->assertOk();

        $this->assertDatabaseHas('account_payment_sessions', [
            'account_id' => null,
            'is_processed' => 0
        ]);

        $resp->assertJson(['data' => ['session_id' => 'abc123']]);
    }
}
