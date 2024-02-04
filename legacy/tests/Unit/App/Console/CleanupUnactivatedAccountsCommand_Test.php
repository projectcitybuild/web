<?php

namespace Tests\Unit\App\Console;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\AccountEmailChange;
use Entities\Models\Eloquent\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CleanupUnactivatedAccountsCommand_Test extends TestCase
{
    use RefreshDatabase;

    public function test_deletes_unactivated_account()
    {
        $elapsedDaysToDelete = 14;

        $accountToBeDeleted = Account::factory()->unactivated()->create([
            'updated_at' => now()->subDays($elapsedDaysToDelete + 1),
        ]);
        $accountJustCreated = Account::factory()->unactivated()->create([
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('accounts', [
            'account_id' => $accountToBeDeleted->getKey(),
        ]);
        $this->assertDatabaseHas('accounts', [
            'account_id' => $accountJustCreated->getKey(),
        ]);

        $this->artisan('cleanup:unactivated-accounts --days='.$elapsedDaysToDelete)
            ->assertExitCode(0);

        $this->assertDatabaseMissing('accounts', [
            'account_id' => $accountToBeDeleted->getKey(),
        ]);
        $this->assertDatabaseHas('accounts', [
            'account_id' => $accountJustCreated->getKey(),
        ]);
    }

    public function test_cascade_deletes_account()
    {
        $account = Account::factory()
            ->unactivated()
            ->create(['updated_at' => now()->subDays(14)]);

        $emailChange = AccountEmailChange::factory()
            ->for($account)
            ->create();

        $group = Group::factory()->create();
        $account->groups()->attach($group);

        $this->artisan('cleanup:unactivated-accounts --days=7')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('accounts', [
            'account_id' => $account->getKey(),
        ]);
        $this->assertDatabaseMissing(AccountEmailChange::getTableName(), [
            'account_email_change_id' => $emailChange->getKey(),
        ]);
    }
}
