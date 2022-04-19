<?php

namespace Tests\Services;

use App\Http\Middleware\MfaGate;
use Database\Factories\AccountFactory;
use Domain\Login\Entities\LoginCredentials;
use Domain\Login\Exceptions\AccountNotActivatedException;
use Domain\Login\Exceptions\InvalidLoginCredentialsException;
use Domain\Login\UseCases\LoginUseCase;
use Entities\Models\Eloquent\Account;
use Entities\Repositories\AccountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Shared\ExternalAccounts\Sync\ExternalAccountSync;
use Tests\TestCase;

class LoginUseCaseTests extends TestCase
{
    use RefreshDatabase;

    private ExternalAccountSync $externalAccountSync;
    private AccountRepository $accountRepository;
    private LoginUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->externalAccountSync = \Mockery::mock(ExternalAccountSync::class);
        $this->accountRepository = \Mockery::mock(AccountRepository::class);

        $this->useCase = new LoginUseCase(
            externalAccountSync: $this->externalAccountSync,
            accountRepository: $this->accountRepository,
        );
    }

    public function test_throws_error_if_incorrect_credentials()
    {
        Account::factory()->create();

        $this->expectException(InvalidLoginCredentialsException::class);

        $this->useCase->execute(
            credentials: new LoginCredentials(
                email: 'invalid',
                password: 'invalid',
            ),
            shouldRemember: false,
            ip: 'ip',
        );
    }

    public function test_throws_error_if_account_not_activated()
    {
        $account = Account::factory()->create(['activated' => false]);

        $this->accountRepository
            ->shouldReceive('getByEmail')
            ->andReturn($account);

        $this->expectException(AccountNotActivatedException::class);

        $this->useCase->execute(
            credentials: new LoginCredentials(
                email: $account->email,
                password: AccountFactory::UNHASHED_PASSWORD,
            ),
            shouldRemember: false,
            ip: 'ip',
        );
    }

    public function test_logs_in()
    {
        $account = Account::factory()->create();

        $this->accountRepository
            ->shouldReceive('getByEmail')
            ->andReturn($account);

        $this->accountRepository
            ->shouldReceive('touchLastLogin');

        $this->assertFalse(Auth::check());

        $this->useCase->execute(
            credentials: new LoginCredentials(
                email: $account->email,
                password: AccountFactory::UNHASHED_PASSWORD,
            ),
            shouldRemember: false,
            ip: 'ip',
        );

        $this->assertTrue(Auth::check());
    }

    public function test_syncs_username_with_external_service()
    {
        $account = Account::factory()->create(['username' => null]);

        $this->accountRepository
            ->shouldReceive('getByEmail')
            ->andReturn($account);

        $this->accountRepository
            ->shouldReceive('touchLastLogin');

        $this->externalAccountSync
            ->shouldReceive('matchExternalUsername')
            ->with($account);

        $this->useCase->execute(
            credentials: new LoginCredentials(
                email: $account->email,
                password: AccountFactory::UNHASHED_PASSWORD,
            ),
            shouldRemember: false,
            ip: 'ip',
        );
    }

    public function test_puts_session_if_2fa_enabled()
    {
        $account = Account::factory()->create(['is_totp_enabled' => true]);

        $this->accountRepository
            ->shouldReceive('getByEmail')
            ->andReturn($account);

        $this->accountRepository
            ->shouldReceive('touchLastLogin');

        $this->useCase->execute(
            credentials: new LoginCredentials(
                email: $account->email,
                password: AccountFactory::UNHASHED_PASSWORD,
            ),
            shouldRemember: false,
            ip: 'ip',
        );

        $this->assertEquals(
            expected: 'true',
            actual: Session::get(MfaGate::NEEDS_MFA_KEY),
        );
    }
}
