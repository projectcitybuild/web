<?php

namespace Tests\E2E;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\DonationTier;
use App\Entities\Models\Eloquent\Group;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DonationWebhookTest extends TestCase
{
    private Account $account;
    private DonationTier $donationTier;
    private Carbon $now;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::factory()->create(['stripe_id' => 'cus_JyjQ8xLdu1UmFs']);
        $this->actingAs($this->account);

        Group::factory()->donor()->create();
        Group::factory()->member()->create();

        $this->donationTier = DonationTier::factory()->create(['donation_tier_id' => 1]);

        $this->now = Carbon::create(
            year: 2022,
            month: 4,
            day: 1,
            hour: 12,
            minute: 15,
            second: 30,
        );
        Carbon::setTestNow($this->now);

        // Prevent calls to Discourse
        putenv('IS_E2E_TEST=true');
    }

    private function loadJsonFromFile(string $path): array
    {
        $jsonFilePath = storage_path($path);
        $json = file_get_contents($jsonFilePath);

        return json_decode($json, true);
    }

    public function testSubscriptionPaymentIsProcessed()
    {
        $payload = $this->loadJsonFromFile('testing/stripe/webhook-invoice-paid.json');

        $this->postJson('/api/webhooks/stripe', $payload)
            ->assertStatus(200);

        $this->assertDatabaseHas('donations', [
            'account_id' => $this->account->getKey(),
            'amount' => 3.00,
        ]);

        $this->assertDatabaseHas('donation_perks', [
            'donation_tier_id' => 1,
            'donation_id' => 1,
            'account_id' => $this->account->getKey(),
            'is_active' => true,
            'expires_at' => $this->now->addMonth(),
        ]);

        $this->assertDatabaseHas('payments', [
            'account_id' => $this->account->getKey(),
            'stripe_price' => 'price_1JJL5mAtUyfM4v5ISwJrrVur',
            'stripe_product' => 'prod_JxFaAltmFPewxs',
            'amount_paid_in_cents' => 300,
            'quantity' => 1,
            'is_subscription_payment' => true,
        ]);

        $this->assertDatabaseCount('donation_perks', 1);
        $this->assertDatabaseCount('payments', 1);
    }

    public function testOneOffPaymentIsProcessed()
    {
        $payload = $this->loadJsonFromFile('testing/stripe/webhook-checkout-session-completed.json');

        $this->postJson('/api/webhooks/stripe', $payload)
            ->assertStatus(200);

        $this->assertDatabaseHas('donations', [
            'account_id' => $this->account->getKey(),
            'amount' => 3.00,
        ]);

        $this->assertDatabaseHas('donation_perks', [
            'donation_tier_id' => 1,
            'donation_id' => 1,
            'account_id' => $this->account->getKey(),
            'is_active' => true,
            'expires_at' => $this->now->addMonth(),
        ]);

        $this->assertDatabaseHas('payments', [
            'account_id' => $this->account->getKey(),
            'stripe_price' => 'price_1JJL5mAtUyfM4v5IJNHp1Tk2',
            'stripe_product' => 'prod_JxFaAltmFPewxs',
            'amount_paid_in_cents' => 300,
            'quantity' => 1,
            'is_subscription_payment' => false,
        ]);

        $this->assertDatabaseCount('donation_perks', 1);
        $this->assertDatabaseCount('payments', 1);
    }

    public function testOneOffPaymentWithMultipleMonthsIsProcessed()
    {
        $payload = $this->loadJsonFromFile('testing/stripe/webhook-checkout-session-completed-2-months.json');

        $this->postJson('/api/webhooks/stripe', $payload)
            ->assertStatus(200);

        $this->assertDatabaseHas('donations', [
            'account_id' => $this->account->getKey(),
            'amount' => 6.00,
        ]);

        $this->assertDatabaseHas('donation_perks', [
            'donation_tier_id' => 1,
            'donation_id' => 1,
            'account_id' => $this->account->getKey(),
            'is_active' => true,
            'expires_at' => $this->now->addMonths(2),
        ]);

        $this->assertDatabaseHas('payments', [
            'account_id' => $this->account->getKey(),
            'stripe_price' => 'price_1JJL5mAtUyfM4v5IJNHp1Tk2',
            'stripe_product' => 'prod_JxFaAltmFPewxs',
            'amount_paid_in_cents' => 600,
            'quantity' => 2,
            'is_subscription_payment' => false,
        ]);

        $this->assertDatabaseCount('donation_perks', 1);
        $this->assertDatabaseCount('payments', 1);
    }
}
