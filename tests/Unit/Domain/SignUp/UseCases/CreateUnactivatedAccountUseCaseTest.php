<?php

namespace Tests\Unit\Domain\SignUp\UseCases;

use Domain\SignUp\UseCases\CreateUnactivatedAccountUseCase;
use Entities\Models\Eloquent\Account;
use Entities\Notifications\AccountActivationNotification;
use Entities\Repositories\AccountRepository;
use Illuminate\Support\Facades\Notification;
use Library\SignedURL\Adapters\StubSignedURLGenerator;
use Library\SignedURL\SignedURLGenerator;
use Shared\Groups\GroupsManager;
use Tests\TestCase;

class CreateUnactivatedAccountUseCaseTest extends TestCase
{
    private AccountRepository $accountRepository;
    private GroupsManager $groupsManager;
    private SignedURLGenerator $signedURLGenerator;
    private CreateUnactivatedAccountUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountRepository = \Mockery::mock(AccountRepository::class);
        $this->groupsManager = \Mockery::mock(GroupsManager::class);
        $this->signedURLGenerator = new StubSignedURLGenerator(outputURL: 'url');

        $this->useCase = new CreateUnactivatedAccountUseCase(
            accountRepository: $this->accountRepository,
            groupsManager: $this->groupsManager,
            signedURLGenerator: $this->signedURLGenerator,
        );

        Notification::fake();
    }

    public function test_creates_unactivated_account()
    {
        $email = 'email';
        $username = 'username';
        $password = 'password';
        $ip = 'ip';

        $this->accountRepository
            ->shouldReceive('create')
            ->with($email, $username, $password, $ip);

        $this->groupsManager
            ->shouldReceive('addToDefaultGroup');

        $this->useCase->execute($email, $username, $password, $ip);
    }

    public function test_sends_notification()
    {
        $account = Account::factory()->create();

        $this->accountRepository
            ->shouldReceive('create')
            ->andReturn($account);

        $this->groupsManager
            ->shouldReceive('addToDefaultGroup');

        $this->useCase->execute('email', 'username', 'password', 'ip');

        Notification::assertSentTo($account, AccountActivationNotification::class);
    }
}