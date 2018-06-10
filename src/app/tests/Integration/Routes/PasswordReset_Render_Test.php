<?php
namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PasswordReset_Render_Test extends TestCase {
    use DatabaseMigrations, DatabaseTransactions;

    public function testDoesRender() {
        $response = $this->get('password-reset');
        $response->assertStatus(200);
    }

}
