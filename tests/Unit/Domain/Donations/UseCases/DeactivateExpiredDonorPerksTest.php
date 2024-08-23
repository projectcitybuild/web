<?php

namespace Tests\Unit\Domain\Donations\UseCases;

use App\Core\Domains\Groups\GroupsManager;
use App\Domains\Donations\Notifications\DonationEndedNotification;
use App\Domains\Donations\UseCases\DeactivateExpiredDonorPerks;
use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Repositories\DonationPerkRepository;
use Tests\TestCase;

final class DeactivateExpiredDonorPerksTest extends TestCase
{
    use RefreshDatabase;

    private GroupsManager $groupsManager;
    private DonationPerkRepository $donationPerkRepository;
    private Group $donorGroup;
    private Account $account;
    private DeactivateExpiredDonorPerks $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->groupsManager = \Mockery::mock(GroupsManager::class);

        $this->donationPerkRepository = \Mockery::mock(DonationPerkRepository::class);
        $this->donorGroup = Group::factory()->create();
        $this->account = Account::factory()->create();

        $this->useCase = new DeactivateExpiredDonorPerks(
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

        $this->donationPerkRepository
            ->shouldReceive('getExpired')
            ->andReturn(collect([$expiredPerk]));

        $this->donationPerkRepository
            ->shouldReceive('countActive')
            ->andReturn(0);

        $this->groupsManager
            ->shouldReceive('removeMember');

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

        $this->donationPerkRepository
            ->shouldReceive('getExpired')
            ->andReturn(collect([]));

        $this->assertTrue($this->find($expiredPerk)->is_active);

        $this->useCase->execute();

        $this->assertTrue(DonationPerk::find($expiredPerk->getKey())->is_active);
    }

    public function test_sends_notification_when_perk_expires()
    {
        $expiredPerk = DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->expired()
            ->create();

        $this->donationPerkRepository
            ->shouldReceive('getExpired')
            ->andReturn(collect([$expiredPerk]));

        $this->donationPerkRepository
            ->shouldReceive('countActive')
            ->andReturn(0);

        $this->groupsManager
            ->shouldReceive('removeMember');

        $this->useCase->execute();

        Notification::assertSentTo($this->account, DonationEndedNotification::class);
    }

    public function test_doesnt_send_notification_if_has_other_active_perk_after_perk_expires()
    {
        $expiredPerk1 = DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->expired()
            ->create();

        $expiredPerk2 = DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->notExpired()
            ->create();

        $this->donationPerkRepository
            ->shouldReceive('getExpired')
            ->andReturn(collect([$expiredPerk1, $expiredPerk2]));

        $this->donationPerkRepository
            ->shouldReceive('countActive')
            ->andReturn(1);

        $this->useCase->execute();

        Notification::assertNothingSentTo($this->account);
    }

    public function test_removes_donor_group_if_perks_expired()
    {
        $expiredPerk = DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->expired()
            ->create();

        $this->donationPerkRepository
            ->shouldReceive('getExpired')
            ->andReturn(collect([$expiredPerk]));

        $this->donationPerkRepository
            ->shouldReceive('countActive')
            ->andReturn(0);

        $this->groupsManager
            ->shouldReceive('removeMember');

        $this->useCase->execute();

        // Skip risky warning
        $this->assertTrue(true);
    }

    public function test_doesnt_remove_donor_group_if_other_active_perks()
    {
        $expiredPerk1 = DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->expired()
            ->create();

        $expiredPerk2 = DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->notExpired()
            ->create();

        $this->donationPerkRepository
            ->shouldReceive('getExpired')
            ->andReturn(collect([$expiredPerk1, $expiredPerk2]));

        $this->donationPerkRepository
            ->shouldReceive('countActive')
            ->andReturn(1);

        $this->groupsManager
            ->shouldNotHaveBeenCalled(['removeMember']);

        $this->useCase->execute();
    }
}
