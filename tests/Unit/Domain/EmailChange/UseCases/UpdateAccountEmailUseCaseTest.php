<?php

namespace Unit\Domain\EmailChange\UseCases;

use Domain\EmailChange\Notifications\VerifyNewEmailAddressNotification;
use Domain\EmailChange\Notifications\VerifyOldEmailAddressNotification;
use Domain\EmailChange\UseCases\SendVerificationEmailUseCase;
use Domain\EmailChange\UseCases\UpdateAccountEmailUseCase;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\AccountEmailChange;
use Entities\Repositories\AccountEmailChangeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Library\SignedURL\Adapters\StubSignedURLGenerator;
use Library\SignedURL\SignedURLGenerator;
use Library\Tokens\Adapters\StubTokenGenerator;
use Library\Tokens\TokenGenerator;
use Shared\ExternalAccounts\Sync\ExternalAccountSync;
use Tests\TestCase;

class UpdateAccountEmailUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private ExternalAccountSync $externalAccountSync;
    private UpdateAccountEmailUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->externalAccountSync = \Mockery::mock(ExternalAccountSync::class);

        $this->useCase = new UpdateAccountEmailUseCase(
            externalAccountSync: $this->externalAccountSync
        );
    }

    public function test_updates_email()
    {
        $oldEmail = 'old_email@pcbmc.co';
        $newEmail = 'new_email@pcbmc.co';
        $account = Account::factory()->create(['email' => $oldEmail]);
        $changeRequest = AccountEmailChange::factory()
            ->for($account)
            ->create(['email_new' => $newEmail]);

        $this->externalAccountSync
            ->shouldReceive('sync')
            ->with($account);

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
