<?php

namespace Unit\Domain\EmailChange\UseCases;

use Domain\EmailChange\Exceptions\TokenNotFoundException;
use Domain\EmailChange\Notifications\VerifyNewEmailAddressNotification;
use Domain\EmailChange\Notifications\VerifyOldEmailAddressNotification;
use Domain\EmailChange\UseCases\SendVerificationEmailUseCase;
use Domain\EmailChange\UseCases\UpdateAccountEmailUseCase;
use Domain\EmailChange\UseCases\VerifyEmailUseCase;
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

class VerifyEmailUseCaseTests extends TestCase
{
    use RefreshDatabase;

    private AccountEmailChangeRepository $emailChangeRepository;
    private VerifyEmailUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->emailChangeRepository = \Mockery::mock(AccountEmailChangeRepository::class);

        $this->useCase = new VerifyEmailUseCase(
            emailChangeRepository: $this->emailChangeRepository,
        );
    }

    public function test_throws_exception_if_missing()
    {
        $this->emailChangeRepository
            ->shouldReceive('firstByToken')
            ->andReturnNull();

        $this->expectException(TokenNotFoundException::class);

        $this->useCase->execute(
            token: 'token',
            email: 'email',
            onHalfComplete: function () {},
            onBothComplete: function () {},
        );
    }

    public function test_flow()
    {
        $verified = false;

        $oldEmail = 'old_email@pcbmc.co';
        $newEmail = 'new_email@pcbmc.co';

        $changeRequest = AccountEmailChange::factory()
            ->for(Account::factory()->create())
            ->create([
                'email_previous' => $oldEmail,
                'email_new' => $newEmail,
            ]);

        $this->emailChangeRepository
            ->shouldReceive('firstByToken')
            ->with('token')
            ->andReturn($changeRequest);

        $this->useCase->execute(
            token: 'token',
            email: $oldEmail,
            onHalfComplete: function () use (&$verified) {
                $verified = true;
            },
            onBothComplete: fn ($_) => $this->fail('Expected onHalfComplete call, but onBothComplete was called'),
        );

        $this->assertTrue($verified);
        $this->assertTrue($changeRequest->is_previous_confirmed);
        $this->assertFalse($changeRequest->is_new_confirmed);

        $this->useCase->execute(
            token: 'token',
            email: $newEmail,
            onHalfComplete: fn ($_) => $this->fail('Expected onBothComplete call, but onHalfComplete was called'),
            onBothComplete: function () use (&$verified) {
                $verified = true;
            },
        );

        $this->assertTrue($verified);
        $this->assertTrue($changeRequest->is_previous_confirmed);
        $this->assertTrue($changeRequest->is_new_confirmed);
    }
}
