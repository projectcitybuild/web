<?php

namespace Tests\Integration\Feature;

use Entities\Models\PanelGroupScope;
use Tests\E2ETestCase;
use Tests\Support\TemporaryConfig;

class TelescopeAccessTest extends E2ETestCase
{
    use TemporaryConfig;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setTemporaryConfig('app.env', 'production');
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
