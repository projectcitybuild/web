<?php

namespace Tests\Integration\Console;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\Donation;
use Entities\Models\Eloquent\DonationPerk;
use Entities\Models\Eloquent\DonationTier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RewardCurrencyCommandTest extends TestCase
{
    use RefreshDatabase;

    private Carbon $now;

    protected function setUp(): void
    {
        parent::setUp();

        $this->now = Carbon::create(year: 2022, month: 4, day: 19, hour: 10, minute: 9, second: 8);
        Carbon::setTestNow($this->now);
    }

    public function test_rewards_currency()
    {
        $tier = DonationTier::factory()->create(['currency_reward' => 200]);
        $account = Account::factory()->create(['balance' => 100]);
        $donation = Donation::factory()->create();
        $perk = DonationPerk::factory()
            ->for($tier)
            ->for($account)
            ->for($donation)
            ->create();

        $this->assertDatabaseHas(
            table: 'donation_perks',
            data: [
                'donation_perks_id' => $perk->getKey(),
                'last_currency_reward_at' => null,
            ],
        );
        $this->assertDatabaseHas(
            table: 'accounts',
            data: [
                'account_id' => $account->getKey(),
                'balance' => 100,
            ],
        );

        $this->artisan('donor-perks:reward-currency')
            ->assertExitCode(0);

        $this->assertDatabaseHas(
            table: 'accounts',
            data: [
                'account_id' => $account->getKey(),
                'balance' => 300,
            ],
        );
        $this->assertDatabaseHas(
            table: 'donation_perks',
            data: [
                'donation_perks_id' => $perk->getKey(),
                'last_currency_reward_at' => $this->now,
            ],
        );
    }

    public function test_rewards_multiple_perks_currency()
    {
        $tier = DonationTier::factory()->create(['currency_reward' => 200]);
        $account = Account::factory()->create(['balance' => 100]);
        $donation = Donation::factory()->create();

        $perk1 = DonationPerk::factory()
            ->for($tier)
            ->for($account)
            ->for($donation)
            ->create();

        $perk2 = DonationPerk::factory()
            ->for($tier)
            ->for($account)
            ->for($donation)
            ->create();

        $this->assertDatabaseHas(
            table: 'donation_perks',
            data: [
                'donation_perks_id' => $perk1->getKey(),
                'last_currency_reward_at' => null,
            ],
        );
        $this->assertDatabaseHas(
            table: 'donation_perks',
            data: [
                'donation_perks_id' => $perk2->getKey(),
                'last_currency_reward_at' => null,
            ],
        );
        $this->assertDatabaseHas(
            table: 'accounts',
            data: [
                'account_id' => $account->getKey(),
                'balance' => 100,
            ],
        );

        $this->artisan('donor-perks:reward-currency')
            ->assertExitCode(0);

        $this->assertDatabaseHas(
            table: 'accounts',
            data: [
                'account_id' => $account->getKey(),
                'balance' => 500,
            ],
        );
        $this->assertDatabaseHas(
            table: 'donation_perks',
            data: [
                'donation_perks_id' => $perk1->getKey(),
                'last_currency_reward_at' => $this->now,
            ],
        );
        $this->assertDatabaseHas(
            table: 'donation_perks',
            data: [
                'donation_perks_id' => $perk2->getKey(),
                'last_currency_reward_at' => $this->now,
            ],
        );
    }

    public function test_doesnt_reward_currency()
    {
        $date = $this->now->copy()->addDays(-1);

        $tier = DonationTier::factory()->create(['currency_reward' => 200]);
        $account = Account::factory()->create(['balance' => 100]);
        $donation = Donation::factory()->create();
        $perk = DonationPerk::factory()
            ->for($tier)
            ->for($account)
            ->for($donation)
            ->create(['last_currency_reward_at' => $date]);

        $this->assertDatabaseHas(
            table: 'donation_perks',
            data: [
                'donation_perks_id' => $perk->getKey(),
                'last_currency_reward_at' => $date,
            ],
        );
        $this->assertDatabaseHas(
            table: 'accounts',
            data: [
                'account_id' => $account->getKey(),
                'balance' => 100,
            ],
        );

        $this->artisan('donor-perks:reward-currency')
            ->assertExitCode(0);

        $this->assertDatabaseHas(
            table: 'accounts',
            data: [
                'account_id' => $account->getKey(),
                'balance' => 100,
            ],
        );
        $this->assertDatabaseHas(
            table: 'donation_perks',
            data: [
                'donation_perks_id' => $perk->getKey(),
                'last_currency_reward_at' => $date,
            ],
        );
    }
}
