<?php

namespace Tests\Services;

use App\Entities\Models\Eloquent\Account;
use Domain\Login\UseCases\LogoutUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Shared\ExternalAccounts\Session\ExternalAccountsSession;
use Tests\TestCase;

class LogoutUseCaseTests extends TestCase
{
    use RefreshDatabase;

    private ExternalAccountsSession $externalAccountsSession;
    private LogoutUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->externalAccountsSession = \Mockery::mock(ExternalAccountsSession::class);

        $this->useCase = new LogoutUseCase(
            externalAccountsSession: $this->externalAccountsSession,
        );
    }

    public function test_returns_false_if_not_logged_in()
    {
        $this->assertFalse($this->useCase->logoutOfPCB());
        $this->assertFalse($this->useCase->logoutOfDiscourseAndPCB());
    }

    public function test_logs_out_of_pcb()
    {
        $account = Account::factory()->create();
        Auth::setUser($account);

        $this->assertTrue(Auth::check());
        $this->assertTrue($this->useCase->logoutOfPCB());
        $this->assertFalse(Auth::check());
    }

    public function test_logs_out_of_pcb_and_discourse()
    {
        $account = Account::factory()->create();
        Auth::setUser($account);

        $this->externalAccountsSession
            ->shouldReceive('logout')
            ->with($account->getKey());

        $this->assertTrue(Auth::check());
        $this->assertTrue($this->useCase->logoutOfDiscourseAndPCB());
        $this->assertFalse(Auth::check());
    }
}
