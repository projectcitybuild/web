<?php

namespace Tests\Unit\Domain\SignUp\UseCases;

use App\Exceptions\Http\BadRequestException;
use App\Models\Account;
use Domain\SignUp\Exceptions\AccountAlreadyActivatedException;
use Domain\SignUp\UseCases\ActivateUnverifiedAccount;
use Repositories\AccountRepository;
use Tests\TestCase;

class ActivateUnverifiedAccountTest extends TestCase
{
    private AccountRepository $accountRepository;
    private ActivateUnverifiedAccount $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountRepository = \Mockery::mock(AccountRepository::class);

        $this->useCase = new ActivateUnverifiedAccount(
            accountRepository: $this->accountRepository,
        );
    }

    public function test_empty_email_throws_exception()
    {
        $this->expectException(BadRequestException::class);
        $this->useCase->execute(email: '');
    }

    public function test_no_matching_account_throws_exception()
    {
        $account = Account::factory()->create();

        $this->accountRepository
            ->shouldReceive('getByEmail')
            ->with($account->email)
            ->andReturnNull();

        $this->expectException(\Exception::class);

        $this->useCase->execute(email: $account->email);
    }

    public function test_already_activated_throws_exception()
    {
        $account = Account::factory()->create(['activated' => true]);

        $this->accountRepository
            ->shouldReceive('getByEmail')
            ->with($account->email)
            ->andReturn($account);

        $this->expectException(AccountAlreadyActivatedException::class);

        $this->useCase->execute(email: $account->email);
    }

    public function test_activates_account()
    {
        $account = Account::factory()->create(['activated' => false]);

        $this->accountRepository
            ->shouldReceive('getByEmail')
            ->with($account->email)
            ->andReturn($account);

        $this->accountRepository
            ->shouldReceive('activate')
            ->with($account);

        $this->useCase->execute(email: $account->email);
    }
}
