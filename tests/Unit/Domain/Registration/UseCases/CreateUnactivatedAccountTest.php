<?php

namespace Tests\Unit\Domain\SignUp\UseCases;

use App\Core\Domains\Groups\GroupsManager;
use App\Core\Support\Laravel\SignedURL\Adapters\StubSignedURLGenerator;
use App\Core\Support\Laravel\SignedURL\SignedURLGenerator;
use App\Domains\Registration\Notifications\AccountActivationNotification;
use App\Domains\Registration\UseCases\CreateUnactivatedAccount;
use App\Models\Account;
use Illuminate\Support\Facades\Notification;
use Repositories\AccountRepository;
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
