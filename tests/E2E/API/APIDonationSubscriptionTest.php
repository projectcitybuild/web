<?php

namespace Tests\E2E\API;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\Donation;
use Entities\Models\Eloquent\DonationTier;
use Entities\Models\Eloquent\Group;
use Entities\Models\Eloquent\StripeProduct;
use Entities\Notifications\DonationPerkStartedNotification;
use Illuminate\Support\Facades\Notification;
use Tests\E2ETestCase;

class APIDonationSubscriptionTest extends E2ETestCase
{
    private Account $account;
    private DonationTier $donationTier;
    private Group $donorGroup;
    private array $webhookPayload;

    private const ENDPOINT = '/api/webhooks/stripe';
    private const PRICE_ID = 'price_1JJL5mAtUyfM4v5ISwJrrVur';
    private const PRODUCT_ID = 'prod_JxFaAltmFPewxs';
    private const QUANTITY = 1;
    private const AMOUNT_PAID = 300;

    protected function setUp(): void
    {
        parent::setUp();

        $this->webhookPayload = $this->loadJsonFromFile('stripe/webhook-invoice-paid.json');

        $this->account = Account::factory()->create(['stripe_id' => 'cus_JyjQ8xLdu1UmFs']);
        $this->donationTier = DonationTier::factory()->create(['donation_tier_id' => 1]);

        $this->donorGroup = Group::factory()->donor()->create();
        Group::factory()->member()->create();

        Notification::fake();
    }

    private function createValidStripeProduct(bool $withDonationTier)
    {
        StripeProduct::create([
            'price_id' => self::PRICE_ID,
            'product_id' => self::PRODUCT_ID,
            'donation_tier_id' => $withDonationTier ? $this->donationTier->getKey() : null,
        ]);
    }

    public function test_payment_processed_without_perks()
    {
        $this->createValidStripeProduct(withDonationTier: false);

        $this->postJson(self::ENDPOINT, $this->webhookPayload)
            ->assertStatus(200);

        $this->assertDatabaseHas('donations', [
            'account_id' => $this->account->getKey(),
            'amount' => self::AMOUNT_PAID / 100,
        ]);

        $this->assertDatabaseHas('payments', [
            'account_id' => $this->account->getKey(),
            'stripe_price' => self::PRICE_ID,
            'stripe_product' => self::PRODUCT_ID,
            'amount_paid_in_cents' => self::AMOUNT_PAID,
            'quantity' => self::QUANTITY,
            'is_subscription_payment' => true,
        ]);

        $this->assertDatabaseCount(table: 'donation_perks', count: 0);

        $this->assertEquals(expected: 0, actual: $this->account->groups->count());

        Notification::assertNothingSent();
    }

    public function test_payment_processed_with_perks()
    {
        $this->createValidStripeProduct(withDonationTier: true);

        $this->postJson(self::ENDPOINT, $this->webhookPayload)
            ->assertStatus(200);

        $this->assertDatabaseHas('donations', [
            'account_id' => $this->account->getKey(),
            'amount' => 3.00,
        ]);

        $donationId = Donation::first()->getKey();

        $this->assertDatabaseHas('payments', [
            'account_id' => $this->account->getKey(),
            'stripe_price' => self::PRICE_ID,
            'stripe_product' => self::PRODUCT_ID,
            'amount_paid_in_cents' => self::AMOUNT_PAID,
            'quantity' => self::QUANTITY,
            'is_subscription_payment' => true,
        ]);

        $this->assertDatabaseHas('donation_perks', [
            'donation_tier_id' => $this->donationTier->getKey(),
            'donation_id' => $donationId,
            'account_id' => $this->account->getKey(),
            'is_active' => true,
            'expires_at' => $this->now->addMonth(),
        ]);

        $this->assertTrue($this->account->groups->contains($this->donorGroup));

        Notification::assertSentTo($this->account, DonationPerkStartedNotification::class);
    }
}
