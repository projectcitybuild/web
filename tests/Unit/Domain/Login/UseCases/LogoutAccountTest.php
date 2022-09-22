<?php

namespace Tests\Unit\Domain\Login\UseCases;

use Domain\Login\UseCases\LogoutAccount;
use Entities\Models\Eloquent\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LogoutAccountTest extends TestCase
{
    use RefreshDatabase;

    private LogoutAccount $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useCase = new LogoutAccount();
    }

    public function test_returns_false_if_not_logged_in()
    {
        $this->assertFalse($this->useCase->execute());
    }

    public function test_logs_out_of_pcb()
    {
        $account = Account::factory()->create();
        Auth::setUser($account);

        $this->assertTrue(Auth::check());
        $this->assertTrue($this->useCase->execute());
        $this->assertFalse(Auth::check());
    }
}
