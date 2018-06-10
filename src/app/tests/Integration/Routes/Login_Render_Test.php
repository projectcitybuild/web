<?php
namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Login_Render_Test extends TestCase {
    use DatabaseMigrations, DatabaseTransactions;

    public function testDoesRender() {
        $payload   = "bm9uY2U9OTkwZjg3YmZkOGY3MDM4Mzk2ODYwOGRlZmY4NDVlY2EmcmV0dXJuX3Nzb191cmw9aHR0cCUzQSUyRiUyRmZvcnVtcy5wcm9qZWN0Y2l0eWJ1aWxkLmNvbSUyRnNlc3Npb24lMkZzc29fbG9naW4%3D";
        $signature = "82d5b1fc0cd8ec026830683bf909e67b6043c661ab030fa45409fa8ffc06d962";

        $response = $this->get('login?sso='.$payload.'&sig='.$signature);
        $response->assertStatus(200);
    }

}