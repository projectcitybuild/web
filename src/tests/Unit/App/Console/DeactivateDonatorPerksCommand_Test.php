<?php

namespace Tests;

use App\Console\Commands\DeactivateDonatorPerksCommand;
use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Groups\Models\Group;
use App\Http\Actions\SyncUserToDiscourse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class DeactivateDonatorPerksCommand_Test extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Group
     */
    private $donatorGroup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->donatorGroup = factory(Group::class)->create(['name' => 'donator']);
    }

    private function makeCommand(): DeactivateDonatorPerksCommand
    {
        $mockSyncAction = $this->getMockBuilder(SyncUserToDiscourse::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new DeactivateDonatorPerksCommand($mockSyncAction);
    }
    
    public function testDeactivatesExpiredPerk()
    {
        $expectedPerk = factory(DonationPerk::class)->create([
            'is_active' => true,
            'is_lifetime_perks' => false,
            'expires_at' => now()->subDay(),
            'account_id' => factory(Account::class)->create()->getKey(),
        ]);

        $command = $this->makeCommand();
        $command->handle();

        $perk = DonationPerk::find($expectedPerk->getKey());

        $this->assertFalse($perk->is_active);
    }

    public function testDoesNotDeactivateUnexpiredPerk()
    {
        $expectedPerk = factory(DonationPerk::class)->create([
            'is_active' => true,
            'is_lifetime_perks' => false,
            'expires_at' => now()->addDay(),
            'account_id' => factory(Account::class)->create()->getKey(),
        ]);

        $command = $this->makeCommand();
        $command->handle();

        $perk = DonationPerk::find($expectedPerk->getKey());

        $this->assertTrue($perk->is_active);
    }

    public function testRemovesDonatorGroupWhenExpired()
    {
        $expectedAccount = factory(Account::class)->create();
        $expectedAccount->groups()->attach($this->donatorGroup->getKey());

        $expectedPerk = factory(DonationPerk::class)->create([
            'account_id' => $expectedAccount->getKey(),
            'is_active' => true,
            'is_lifetime_perks' => false,
            'expires_at' => now()->subDay(),
        ]);

        $this->assertTrue($expectedPerk->account->groups->contains($this->donatorGroup->getKey()));

        $command = $this->makeCommand();
        $command->handle();

        $account = Account::find($expectedAccount->getKey());
        $this->assertFalse($account->groups->contains($this->donatorGroup->getKey()));
    }
}
