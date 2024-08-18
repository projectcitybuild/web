<?php

namespace Tests\Unit\Domain\SignUp\UseCases;

use App\Core\Domains\SignedURL\Adapters\StubSignedURLGenerator;
use App\Core\Domains\SignedURL\SignedURLGenerator;
use App\Domains\SignUp\Exceptions\AccountAlreadyActivatedException;
use App\Domains\SignUp\Notifications\AccountActivationNotification;
use App\Domains\SignUp\UseCases\ResendActivationEmail;
use App\Models\Account;
use Illuminate\Support\Facades\Notification;
use Repositories\AccountRepository;
use Tests\TestCase;

class ResendActivationEmailTest extends TestCase
{
    private AccountRepository $accountRepository;
    private SignedURLGenerator $signedURLGenerator;
    private ResendActivationEmail $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountRepository = \Mockery::mock(AccountRepository::class);
        $this->signedURLGenerator = new StubSignedURLGenerator(outputURL: 'url');

        $this->useCase = new ResendActivationEmail(
            accountRepository: $this->accountRepository,
            signedURLGenerator: $this->signedURLGenerator,
        );

        Notification::fake();
    }

    public function test_throws_exception_if_account_already_activated()
    {
        $account = Account::factory()->create(['activated' => true]);

        $this->accountRepository
            ->shouldReceive('getByEmail')
            ->with($account->email)
            ->andReturn($account);

        $this->expectException(AccountAlreadyActivatedException::class);

        $this->useCase->execute(email: $account->email);
    }

    public function test_sends_notification()
    {
        $account = Account::factory()->create(['activated' => false]);

        $this->accountRepository
            ->shouldReceive('getByEmail')
            ->andReturn($account);

        $this->useCase->execute('email');

        Notification::assertSentTo($account, AccountActivationNotification::class);
    }
}
