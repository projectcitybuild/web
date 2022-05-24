<?php

namespace Unit\Domain\EmailChange\UseCases;

use Domain\EmailChange\Notifications\VerifyNewEmailAddressNotification;
use Domain\EmailChange\Notifications\VerifyOldEmailAddressNotification;
use Domain\EmailChange\UseCases\SendVerificationEmailUseCase;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\AccountEmailChange;
use Entities\Repositories\AccountEmailChangeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Library\SignedURL\Adapters\StubSignedURLGenerator;
use Library\SignedURL\SignedURLGenerator;
use Library\Tokens\Adapters\StubTokenGenerator;
use Library\Tokens\TokenGenerator;
use Tests\TestCase;

class SendVerificationEmailUseCaseTests extends TestCase
{
    use RefreshDatabase;

    private AccountEmailChangeRepository $emailChangeRepository;
    private TokenGenerator $tokenGenerator;
    private SignedURLGenerator $signedURLGenerator;
    private SendVerificationEmailUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->emailChangeRepository = \Mockery::mock(AccountEmailChangeRepository::class);
        $this->tokenGenerator = new StubTokenGenerator(token: 'token');
        $this->signedURLGenerator = new StubSignedURLGenerator(outputURL: 'url');

        $this->useCase = new SendVerificationEmailUseCase(
            emailChangeRepository: $this->emailChangeRepository,
            tokenGenerator: $this->tokenGenerator,
            signedURLGenerator: $this->signedURLGenerator,
        );
    }

    public function test_sends_emails()
    {
        Notification::fake();

        $account = Account::factory()->create();
        $newEmail = 'new_email@pcbmc.co';

        $this->emailChangeRepository
            ->shouldReceive('create')
            ->with($account->getKey(), 'token', $account->email, $newEmail)
            ->andReturn(AccountEmailChange::make());

        $this->useCase->execute(
            accountId: $account->getKey(),
            oldEmailAddress: $account->email,
            newEmailAddress: $newEmail,
        );

        Notification::assertSentOnDemand(
            notification: VerifyOldEmailAddressNotification::class,
            callback: fn ($notification, $channels, $notifiable)
                => $notifiable->routes['mail'] === $account->email
        );

        Notification::assertSentOnDemand(
            notification: VerifyNewEmailAddressNotification::class,
            callback: fn ($notification, $channels, $notifiable)
            => $notifiable->routes['mail'] === $newEmail
        );
    }
}
