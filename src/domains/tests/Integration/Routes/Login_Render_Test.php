<?php
namespace Tests\Integration;

use Infrastructure\Library\Discourse\Authentication\DiscourseAuthService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Login_Render_Test extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    private $payload   = "bm9uY2U9OTkwZjg3YmZkOGY3MDM4Mzk2ODYwOGRlZmY4NDVlY2EmcmV0dXJuX3Nzb191cmw9aHR0cCUzQSUyRiUyRmZvcnVtcy5wcm9qZWN0Y2l0eWJ1aWxkLmNvbSUyRnNlc3Npb24lMkZzc29fbG9naW4%3D";
    private $signature = "82d5b1fc0cd8ec026830683bf909e67b6043c661ab030fa45409fa8ffc06d962";


    protected function setUp()
    {
        parent::setUp();

        // disable checking the signed payload
        // in the url
        DiscourseAuthService::disablePayloadChecks();
    }

    public function testDoesRender()
    {
        $response = $this->get('login?sso='.$this->payload.'&sig='.$this->signature);
        $response->assertStatus(200);
    }

    public function testMissingPayload_Redirects()
    {
        $response = $this->get('login?sig='.$this->signature);
        $response->assertRedirect('/');
    }

    public function testMissingSignature_Redirects()
    {
        $response = $this->get('login?payload='.$this->payload);
        $response->assertRedirect('/');
    }
}
