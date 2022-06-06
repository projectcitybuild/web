<?php

namespace Test\Feature;

use Entities\Models\PanelGroupScope;
use Illuminate\Support\Facades\Config;
use Tests\E2ETestCase;
use Tests\TestCase;

class TelescopeAccessTest extends E2ETestCase
{
    protected string $originalEnv;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalEnv = config('app.env');
        Config::set('app.env', 'production');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Config::set('app.env', $this->originalEnv);
    }


    public function test_telescope_access_forbidden()
    {
        $this->get('/telescope')
            ->assertForbidden();
    }

    public function test_telescope_admin_allowed()
    {
        $this->actingAs($this->adminAccount(scopes: [PanelGroupScope::ACCESS_PANEL]))
            ->get('/telescope')
            ->assertOk();
    }
}
