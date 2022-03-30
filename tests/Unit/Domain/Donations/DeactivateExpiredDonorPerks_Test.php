<?php

namespace Tests\Unit\Domain\Donations;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\Donation;
use App\Entities\Models\Eloquent\DonationPerk;
use App\Entities\Notifications\DonationEndedNotification;
use Domain\Donations\DeactivateExpiredDonorPerks;
use Domain\Donations\DonationGroupSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class DeactivateExpiredDonorPerks_Test extends TestCase
{
    use RefreshDatabase;

    private $syncServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->syncServiceMock = $this->mock(DonationGroupSyncService::class);

        Notification::fake();
    }

    public function testDeactivatesExpiredPerk()
    {
        $account = Account::factory()->create();

        $expiredPerk = DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->expired()
            ->create();

        $this->assertTrue(DonationPerk::find($expiredPerk->getKey())->is_active);

        $this->syncServiceMock->shouldIgnoreMissing();
        $service = new DeactivateExpiredDonorPerks($this->syncServiceMock);
        $service->execute();

        $this->assertFalse(DonationPerk::find($expiredPerk->getKey())->is_active);
    }

    public function testDoesNotDeactivateUnexpiredPerk()
    {
        $account = Account::factory()->create();

        $expiredPerk = DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->notExpired()
            ->create();

        $this->assertTrue(DonationPerk::find($expiredPerk->getKey())->is_active);

        $this->syncServiceMock->shouldIgnoreMissing();
        $service = new DeactivateExpiredDonorPerks($this->syncServiceMock);
        $service->execute();

        $this->assertTrue(DonationPerk::find($expiredPerk->getKey())->is_active);
    }

    public function testSendsNotificationWhenPerkExpires()
    {
        $account = Account::factory()->create();

        DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->expired()
            ->create();

        $this->syncServiceMock->shouldIgnoreMissing();
        $service = new DeactivateExpiredDonorPerks($this->syncServiceMock);
        $service->execute();

        Notification::assertSentTo($account, DonationEndedNotification::class);
    }

    public function testRemovesDonorGroupIfPerksExpired()
    {
        $account = Account::factory()->create();

        DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->expired()
            ->create();

        $syncService = $this->spy(DonationGroupSyncService::class);
        $service = new DeactivateExpiredDonorPerks($syncService);
        $service->execute();

        $syncService->shouldHaveReceived('removeFromDonorGroup');
    }

    public function testDoesntRemoveDonorGroupIfOtherActivePerks()
    {
        $account = Account::factory()->create();

        DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->expired()
            ->create();

        DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->notExpired()
            ->create();

        $syncService = $this->spy(DonationGroupSyncService::class);
        $service = new DeactivateExpiredDonorPerks($syncService);
        $service->execute();

        $syncService->shouldNotHaveReceived('removeFromDonorGroup');
    }
}
