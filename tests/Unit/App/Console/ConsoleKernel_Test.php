<?php

namespace Tests;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class ConsoleKernel_Test extends TestCase
{
    use RefreshDatabase;
    
    public function testCommand_QueryAllServers()
    {
        $this->artisan('query:status --all')
            ->assertExitCode(0);
    }

    public function testCommand_CleanupPasswordReset()
    {
        $this->artisan('cleanup:password-resets')
            ->assertExitCode(0);
    }

    public function testCommand_CleanupUnactivatedAccounts()
    {
        $this->artisan('cleanup:unactivated-accounts')
            ->assertExitCode(0);
    }

    public function testCommand_DeactivateDonatorPerks()
    {
        $this->artisan('donator-perks:expire')
            ->assertExitCode(0);
    }
}
