<?php

namespace Tests;

use App\Console\Commands\CleanupUnactivatedAccountsCommand;
use App\Entities\Accounts\Models\Account;
use App\Entities\Donations\Models\DonationPerk;
use App\Entities\Groups\Models\Group;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class CleanupUnactivatedAccountsCommand_Test extends TestCase
{
    use RefreshDatabase;

    public function testCascadeDeletesUnactivatedAccount()
    {
        $elapsedDaysToDelete = 14;

        $associatedGroup = factory(Group::class)->states('as-default')->create();

        $unactivatedAccount = factory(Account::class)->states('unactivated')->create();
        $unactivatedAccount->groups()->attach($unactivatedAccount->getKey());
        $unactivatedAccount->updated_at = now()->subDays($elapsedDaysToDelete + 1);
        $unactivatedAccount->save();

        $this->assertDatabaseHas('accounts', [
            'account_id' => $unactivatedAccount->getKey(),
        ]);
        $this->assertDatabaseHas('groups_accounts', [
            'account_id' => $unactivatedAccount->getKey(),
            'group_id' => $associatedGroup->getKey(),
        ]);

        $this->artisan('cleanup:unactivated-accounts --days='.$elapsedDaysToDelete)
            ->assertExitCode(0);

        $this->assertDatabaseMissing('accounts', [
            'account_id' => $unactivatedAccount->getKey(),
        ]);
        $this->assertDatabaseMissing('groups_accounts', [
            'account_id' => $unactivatedAccount->getKey(),
            'group_id' => $associatedGroup->getKey(),
        ]);
    }
}
