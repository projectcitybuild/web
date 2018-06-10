<?php
namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Register_Render_Test extends TestCase {
    use DatabaseMigrations, DatabaseTransactions;

    public function testDoesRender() {
        $response = $this->get('register');
        $response->assertStatus(200);
    }

}
