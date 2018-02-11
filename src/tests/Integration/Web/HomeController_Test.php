<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HomeController_Test extends TestCase {
    use DatabaseMigrations, DatabaseTransactions;

    /**
     * Asserts that the getView() controller route renders
     *
     * @return void
     */
    public function testGetView_doesRender() {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

}
