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

    protected function setUp(): void
    {
        parent::setUp();

        factory(Group::class)->create(['name' => 'donator']);
    }

    private function makeCommand() : DeactivateDonatorPerksCommand
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
}
