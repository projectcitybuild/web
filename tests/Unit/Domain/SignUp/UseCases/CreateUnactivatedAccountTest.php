<?php

namespace Tests\Unit\Domain\SignUp\UseCases;

use Domain\SignUp\UseCases\CreateUnactivatedAccount;
use Entities\Models\Eloquent\Account;
use Entities\Notifications\AccountActivationNotification;
use Illuminate\Support\Facades\Notification;
use Library\SignedURL\Adapters\StubSignedURLGenerator;
use Library\SignedURL\SignedURLGenerator;
use Repositories\AccountRepository;
use Shared\Groups\GroupsManager;
use Tests\TestCase;

class CreateUnactivatedAccountTest extends TestCase
{
    private AccountRepository $accountRepository;
    private GroupsManager $groupsManager;
    private SignedURLGenerator $signedURLGenerator;
    private CreateUnactivatedAccount $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountRepository = \Mockery::mock(AccountRepository::class);
        $this->groupsManager = \Mockery::mock(GroupsManager::class);
        $this->signedURLGenerator = new StubSignedURLGenerator(outputURL: 'url');

        $this->useCase = new CreateUnactivatedAccount(
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
            ->with($email, $username, $password, $ip)
            ->andReturn(new Account());

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
