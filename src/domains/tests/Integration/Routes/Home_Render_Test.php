<?php
namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Home_Render_Test extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testDoesRender()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
