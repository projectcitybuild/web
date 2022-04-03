<?php

namespace Tests\Unit\Domain\Donations\UseCases;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\Donation;
use App\Entities\Models\Eloquent\DonationPerk;
use App\Entities\Models\Eloquent\DonationTier;
use App\Entities\Models\Eloquent\Group;
use Carbon\Carbon;
use Domain\Donations\Entities\PaidAmount;
use Domain\Donations\Entities\PaymentType;
use Domain\Donations\Repositories\DonationPerkRepository;
use Domain\Donations\Repositories\DonationRepository;
use Domain\Donations\Repositories\PaymentRepository;
use Domain\Donations\UseCases\ProcessPaymentUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Shared\Groups\GroupsManager;
use Tests\TestCase;

final class ProcessPaymentUseCaseTests extends TestCase
{
    use RefreshDatabase;

    private GroupsManager $groupsManager;
    private PaymentRepository $paymentRepository;
    private DonationPerkRepository $donationPerkRepository;
    private DonationRepository $donationRepository;
    private Group $donorGroup;
    private DonationTier $donationTier;
    private Account $account;
    private Carbon $now;

    private ProcessPaymentUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->groupsManager = \Mockery::mock(GroupsManager::class);
        $this->paymentRepository = \Mockery::mock(PaymentRepository::class)->makePartial();
        $this->donationPerkRepository = \Mockery::mock(DonationPerkRepository::class)->makePartial();
        $this->donationRepository = \Mockery::mock(DonationRepository::class)->makePartial();
        $this->donorGroup = Group::factory()->create();
        $this->donationTier = DonationTier::factory()->create();
        $this->account = Account::factory()->create();

        $this->useCase = new ProcessPaymentUseCase(
            groupsManager: $this->groupsManager,
            paymentRepository: $this->paymentRepository,
            donationPerkRepository: $this->donationPerkRepository,
            donationRepository: $this->donationRepository,
            donorGroup: $this->donorGroup,
        );

        $this->now = Carbon::create(year: 2022, month: 4, day: 3, hour: 18, minute: 2, second: 1);
        Carbon::setTestNow($this->now);

        Notification::fake();
    }

    public function test_first_time_successful_one_off_payment()
    {
        $productId = 'product_id';
        $priceId = 'price_id';
        $numberOfMonths = 3;
        $paidAmount = PaidAmount::fromCents(12345);

        $this->groupsManager
            ->shouldReceive('addMember')
            ->withArgs([$this->donorGroup, $this->account]);

        $this->useCase->execute(
            account: $this->account,
            productId: $productId,
            priceId: $priceId,
            donationTierId: $this->donationTier->getKey(),
            paidAmount: $paidAmount,
            quantity: $numberOfMonths,
            donationType: PaymentType::ONE_OFF,
        );

        $this->assertDatabaseHas(
            table: 'payments',
            data: [
                'account_id' => $this->account->getKey(),
                'stripe_product' => $productId,
                'stripe_price' => $priceId,
                'amount_paid_in_cents' => 12345,
                'quantity' => $numberOfMonths,
                'is_subscription_payment' => false,
            ]
        );
        $this->assertDatabaseHas(
            table: 'donations',
            data: [
                'account_id' => $this->account->getKey(),
                'amount' => 123.45,
            ]
        );
        $this->assertDatabaseHas(
            table: 'donation_perks',
            data: [
                'donation_id' => 1,
                'donation_tier_id' => $this->donationTier->getKey(),
                'account_id' => $this->account->getKey(),
                'is_active' => true,
                'expires_at' => $this->now->copy()->addMonths($numberOfMonths),
            ]
        );
    }

    public function test_successful_payment_extends_expiry_time()
    {
        $donation = Donation::factory()
            ->for($this->account)
            ->create();

        DonationPerk::factory()
            ->for($this->account)
            ->for($this->donationTier)
            ->for($donation)
            ->create(['expires_at' => $this->now->copy()->addDays(15)]);

        $this->groupsManager
            ->shouldReceive('addMember');

        $this->useCase->execute(
            account: $this->account,
            productId: 'product_id',
            priceId: 'price_id',
            donationTierId: $this->donationTier->getKey(),
            paidAmount: PaidAmount::fromCents(12345),
            quantity: 1,
            donationType: PaymentType::ONE_OFF,
        );

        $newPerk = DonationPerk::where('donation_id', 2)->first();
        $this->assertEquals(
            expected: $this->now->copy()->addDays(15)->addMonths(1),
            actual: $newPerk->expires_at,
        );
    }

    public function test_successful_payment_doesnt_extend_expiry_time_of_different_tier_id()
    {
        $otherTier = DonationTier::factory()->create();

        $donation = Donation::factory()
            ->for($this->account)
            ->create();

        DonationPerk::factory()
            ->for($this->account)
            ->for($this->donationTier)
            ->for($donation)
            ->create(['expires_at' => $this->now->copy()->addDays(15)]);

        $this->groupsManager
            ->shouldReceive('addMember');

        $this->useCase->execute(
            account: $this->account,
            productId: 'product_id',
            priceId: 'price_id',
            donationTierId: $otherTier->getKey(),
            paidAmount: PaidAmount::fromCents(12345),
            quantity: 1,
            donationType: PaymentType::ONE_OFF,
        );

        $newPerk = DonationPerk::where('donation_id', 2)->first();
        $this->assertEquals(
            expected: $this->now->copy()->addMonth(),
            actual: $newPerk->expires_at,
        );
    }
}
