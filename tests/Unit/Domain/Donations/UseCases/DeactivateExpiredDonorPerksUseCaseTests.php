<?php

namespace Tests\Unit\Domain\Donations\UseCases;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\Donation;
use App\Entities\Models\Eloquent\DonationPerk;
use App\Entities\Models\Eloquent\Group;
use App\Entities\Notifications\DonationEndedNotification;
use Domain\Donations\Repositories\DonationPerkRepository;
use Domain\Donations\UseCases\DeactivateExpiredDonorPerksUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Shared\Groups\GroupsManager;
use Tests\TestCase;

final class DeactivateExpiredDonorPerksUseCaseTests extends TestCase
{
    use RefreshDatabase;

    private GroupsManager $groupsManager;
    private DonationPerkRepository $donationPerkRepository;
    private Group $donorGroup;
    private Account $account;
    private DeactivateExpiredDonorPerksUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->groupsManager = \Mockery::mock(GroupsManager::class)->makePartial();
        $this->donationPerkRepository = \Mockery::mock(DonationPerkRepository::class)->makePartial();
        $this->donorGroup = Group::factory()->create();
        $this->account = Account::factory()->create();

        $this->useCase = new DeactivateExpiredDonorPerksUseCase(
            groupsManager: $this->groupsManager,
            donationPerkRepository: $this->donationPerkRepository,
            donorGroup: $this->donorGroup,
        );

        Notification::fake();
    }

    private function find(DonationPerk $donationPerk): ?DonationPerk
    {
        return DonationPerk::find($donationPerk->getKey());
    }

    public function test_deactivates_expired_perk()
    {
        $expiredPerk = DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->expired()
            ->create();

        $this->assertTrue($this->find($expiredPerk)->is_active);

        $this->useCase->execute();

        $this->assertFalse($this->find($expiredPerk)->is_active);
    }

    public function test_doesnt_deactivate_unexpired_perk()
    {
        $expiredPerk = DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->notExpired()
            ->create();

        $this->assertTrue($this->find($expiredPerk)->is_active);

        $this->useCase->execute();

        $this->assertTrue(DonationPerk::find($expiredPerk->getKey())->is_active);
    }

    public function test_sends_notification_when_perk_expires()
    {
        DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->expired()
            ->create();

        $this->useCase->execute();

        Notification::assertSentTo($this->account, DonationEndedNotification::class);
    }

    public function test_doesnt_send_notification_if_has_other_active_perk_after_perk_expires()
    {
        DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->expired()
            ->create();

        DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->notExpired()
            ->create();

        $this->useCase->execute();

        Notification::assertNothingSentTo($this->account);
    }

    public function test_requests_donor_group_removal_if_perks_expired()
    {
        DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->expired()
            ->create();

        $this->groupsManager
            ->shouldReceive('removeMember')
            ->with([$this->donorGroup->getKey(), $this->account->getKey()]);

        $this->useCase->execute();
    }

    public function test_doesnt_request_donor_group_removal_if_other_active_perks()
    {
        DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->expired()
            ->create();

        DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->notExpired()
            ->create();

        $this->groupsManager->shouldNotHaveBeenCalled(['removeMember']);

        $this->useCase->execute();
    }
}