<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

final class ConsoleKernel_Test extends TestCase
{
    use RefreshDatabase;

    public function test_server_query_command()
    {
        $this->artisan('server:query --all')
            ->assertExitCode(0);

        $this->artisan('server:query --all --background')
            ->assertExitCode(0);
    }

    public function test_cleanup_password_resets_command()
    {
        $this->artisan('cleanup:password-resets')
            ->assertExitCode(0);
    }

    public function test_cleanup_unactivated_accounts_command()
    {
        $this->artisan('cleanup:unactivated-accounts')
            ->assertExitCode(0);
    }

    public function test_expire_donor_perks_command()
    {
        $this->artisan('donor-perks:expire')
            ->assertExitCode(0);
    }
}
