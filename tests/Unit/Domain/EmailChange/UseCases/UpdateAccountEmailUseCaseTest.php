<?php

namespace Unit\Domain\EmailChange\UseCases;

use Domain\EmailChange\UseCases\UpdateAccountEmailUseCase;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\AccountEmailChange;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateAccountEmailUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private UpdateAccountEmailUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useCase = new UpdateAccountEmailUseCase();
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
