<?php

namespace Unit\Domain\EmailChange\UseCases;

use App\Models\Account;
use App\Models\AccountEmailChange;
use Domain\EmailChange\Notifications\VerifyNewEmailAddressNotification;
use Domain\EmailChange\Notifications\VerifyOldEmailAddressNotification;
use Domain\EmailChange\UseCases\SendVerificationEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Library\SignedURL\Adapters\StubSignedURLGenerator;
use Library\SignedURL\SignedURLGenerator;
use Library\Tokens\Adapters\StubTokenGenerator;
use Library\Tokens\TokenGenerator;
use Repositories\AccountEmailChangeRepository;
use Tests\TestCase;

class SendVerificationEmailTest extends TestCase
{
    use RefreshDatabase;

    private AccountEmailChangeRepository $emailChangeRepository;
    private TokenGenerator $tokenGenerator;
    private SignedURLGenerator $signedURLGenerator;
    private SendVerificationEmail $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->emailChangeRepository = \Mockery::mock(AccountEmailChangeRepository::class);
        $this->tokenGenerator = new StubTokenGenerator(token: 'token');
        $this->signedURLGenerator = new StubSignedURLGenerator(outputURL: 'url');

        $this->useCase = new SendVerificationEmail(
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
            callback: fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === $account->email
        );

        Notification::assertSentOnDemand(
            notification: VerifyNewEmailAddressNotification::class,
            callback: fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === $newEmail
        );
    }
}
