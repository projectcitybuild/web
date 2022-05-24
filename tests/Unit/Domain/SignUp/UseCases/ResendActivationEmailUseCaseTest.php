<?php

namespace Tests\Unit\Domain\SignUp\UseCases;

use Domain\SignUp\Exceptions\AccountAlreadyActivatedException;
use Domain\SignUp\UseCases\ResendActivationEmailUseCase;
use Entities\Models\Eloquent\Account;
use Entities\Notifications\AccountActivationNotification;
use Entities\Repositories\AccountRepository;
use Illuminate\Support\Facades\Notification;
use Library\SignedURL\Adapters\StubSignedURLGenerator;
use Library\SignedURL\SignedURLGenerator;
use Tests\TestCase;

class ResendActivationEmailUseCaseTest extends TestCase
{
    private AccountRepository $accountRepository;
    private SignedURLGenerator $signedURLGenerator;
    private ResendActivationEmailUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountRepository = \Mockery::mock(AccountRepository::class);
        $this->signedURLGenerator = new StubSignedURLGenerator(outputURL: 'url');

        $this->useCase = new ResendActivationEmailUseCase(
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
