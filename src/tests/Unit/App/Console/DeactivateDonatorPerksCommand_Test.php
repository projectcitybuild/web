<?php

namespace Tests;

use App\Console\Commands\DeactivateDonatorPerksCommand;
use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\DonationPerk;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class DeactivateDonatorPerksCommand_Test extends TestCase
{
    use RefreshDatabase;
    
    public function testDeactivatesExpiredPerk()
    {
        $expectedPerk = factory(DonationPerk::class)->create([
            'is_active' => true,
            'is_lifetime_perks' => false,
            'expires_at' => now()->subDay(),
            'account_id' => factory(Account::class)->create()->getKey(),
        ]);

        $command = resolve(DeactivateDonatorPerksCommand::class);
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

        $command = resolve(DeactivateDonatorPerksCommand::class);
        $command->handle();

        $perk = DonationPerk::find($expectedPerk->getKey());

        $this->assertTrue($perk->is_active);
    }
}
