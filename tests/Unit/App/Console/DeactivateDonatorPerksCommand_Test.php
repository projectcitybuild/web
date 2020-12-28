<?php

namespace Tests;

use App\Console\Commands\DeactivateDonatorPerksCommand;
use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\Donation;
use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Donations\Notifications\DonationEndedNotification;
use App\Entities\Groups\Models\Group;
use App\Http\Actions\SyncUserToDiscourse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

final class DeactivateDonatorPerksCommand_Test extends TestCase
{
    use RefreshDatabase;

    private Group $donatorGroup;

    private Group $memberGroup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->donatorGroup = Group::factory()->donator()->create();
        $this->memberGroup = Group::factory()->member()->create();
    }

    private function makeCommand(): DeactivateDonatorPerksCommand
    {
        $mockSyncAction = $this->getMockBuilder(SyncUserToDiscourse::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new DeactivateDonatorPerksCommand($mockSyncAction);
    }

    public function testSendsNotificationWhenDonationExpires()
    {
        Notification::fake();

        $account = Account::factory()->create();

        DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->expired()
            ->create();

        $command = $this->makeCommand();
        $command->handle();

        Notification::assertSentTo($account, DonationEndedNotification::class);
    }

    public function testDeactivatesExpiredPerks()
    {
        $account = Account::factory()->create();

        $expiredPerk = DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->expired()
            ->create();

        $notExpiredPerk = DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->notExpired()
            ->create();

        $lifetimePerk = DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->expired()
            ->lifetime()
            ->create();

        $command = $this->makeCommand();
        $command->handle();

        $this->assertFalse(DonationPerk::find($expiredPerk->getKey())->is_active);
        $this->assertTrue(DonationPerk::find($notExpiredPerk->getKey())->is_active);
        $this->assertTrue(DonationPerk::find($lifetimePerk->getKey())->is_active);
    }

    public function testRemovesDonatorGroupWhenAllPerksHaveExpired()
    {
        $account = Account::factory()
            ->hasAttached($this->donatorGroup)
            ->create();

        DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->expired()
            ->count(2)
            ->create();

        $command = $this->makeCommand();
        $command->handle();

        $account = Account::find($account->getKey());
        $this->assertFalse($account->groups->contains($this->donatorGroup->getKey()));
    }

    public function testDoesNotRemoveDonatorGroupIfOtherUnexpiredPerk()
    {
        $account = Account::factory()
            ->hasAttached($this->donatorGroup)
            ->create();

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

        $command = $this->makeCommand();
        $command->handle();

        $account = Account::find($account->getKey());
        $this->assertTrue($account->groups->contains($this->donatorGroup->getKey()));
    }

    public function testDoesNotRemoveDonatorGroupIfOtherPerkIsLifetime()
    {
        $account = Account::factory()
            ->hasAttached($this->donatorGroup)
            ->create();

        DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->expired()
            ->create();

        DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->lifetime()
            ->create();

        $command = $this->makeCommand();
        $command->handle();

        $account = Account::find($account->getKey());
        $this->assertTrue($account->groups->contains($this->donatorGroup->getKey()));
    }

    public function testAssignsExpiredDonatorToMemberGroupIfNoGroup()
    {
        $account = Account::factory()
            ->hasAttached($this->donatorGroup)
            ->create();

        DonationPerk::factory()
            ->for($account)
            ->for(Donation::factory()->for($account))
            ->expired()
            ->create();

        $command = $this->makeCommand();
        $command->handle();

        $account = Account::find($account->getKey());
        $this->assertTrue($account->groups->contains($this->memberGroup->getKey()));
        $this->assertFalse($account->groups->contains($this->donatorGroup->getKey()));
        $this->assertEquals(1, count($account->groups));
    }
}
