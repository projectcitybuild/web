<?php

namespace Unit\Domain\EmailChange\UseCases;

use App\Models\Account;
use App\Models\AccountEmailChange;
use Domain\EmailChange\UseCases\UpdateAccountEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateAccountEmailTest extends TestCase
{
    use RefreshDatabase;

    private UpdateAccountEmail $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useCase = new UpdateAccountEmail();
    }

    public function test_updates_email()
    {
        $oldEmail = 'old_email@pcbmc.co';
        $newEmail = 'new_email@pcbmc.co';
        $account = Account::factory()->create(['email' => $oldEmail]);
        $changeRequest = AccountEmailChange::factory()
            ->for($account)
            ->create(['email_new' => $newEmail]);

        $this->assertEquals(expected: $oldEmail, actual: $account->email);
        $this->assertDatabaseHas(
            table: $changeRequest->getTable(),
            data: ['account_email_change_id' => $changeRequest->getKey()]
        );

        $this->useCase->execute($account, $changeRequest);

        $this->assertEquals(expected: $newEmail, actual: $account->email);
        $this->assertDatabaseMissing(
            table: $changeRequest->getTable(),
            data: ['account_email_change_id' => $changeRequest->getKey()]
        );
    }
}
