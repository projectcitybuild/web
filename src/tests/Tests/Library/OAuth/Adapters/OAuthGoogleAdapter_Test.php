<?php
namespace Tests\Library\OAuth;

use Tests\TestCase;
use Domains\Library\OAuth\Adapters\Google\GoogleOAuthAdapter;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Log\Logger;

class OAuthGoogleAdapter_Test extends TestCase
{
    private $loggerStub;
    private $clientMock;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->loggerStub = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->clientMock = $this->getMockBuilder(Client::class)
            ->getMock();
    }

    private function getClientWithResponses(array $responses)
    {
        $mock        = new MockHandler($responses);
        $mockHandler = HandlerStack::create($mock);
        $client      = new Client(['handler' => $mockHandler]);

        return $client;
    }

    public function testGetLoginUrl_succeeds()
    {
        // given...
        $adapter = new GoogleOAuthAdapter($this->clientMock, $this->loggerStub);
        $redirectUri = 'https://projectcitybuild.com/test_uri';

        // when...
        $result = $adapter->requestProviderLoginUrl($redirectUri);

        // expect...
        $expectedClientId = config('services.google.client_id');
        $expectedRedirectUri = rawurlencode($redirectUri);
        $expectedUri = 'https://accounts.google.com/o/oauth2/auth?client_id='.$expectedClientId.'&redirect_uri='.$expectedRedirectUri.'&response_type=code&scope=openid%20profile%20email';
        
        $this->assertEquals($expectedUri, $result);
    }

    public function testGetProviderAccount_succeeds()
    {
        // given...
        $client = $this->getClientWithResponses([
            new Response(200, [], '{ "access_token" : "test_access_token", "expires_in" : 3598, "id_token" : "test_id_token", "scope" : "https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/userinfo.email", "token_type" : "Bearer" }'),
            new Response(200, [], '{ "kind": "plus#person", "etag": "\"hCnRu-GwRzXLXPdAHHyDrK_S150/8W1tW_V6D9lKAFGPjSqIN2mhbWo\"", "emails": [ { "value": "testuser@pcbmc.co", "type": "account" } ], "objectType": "person", "id": "12345678901234567890", "displayName": "Test Account", "name": { "familyName": "Account", "givenName": "Test" }, "image": { "url": "https://lh4.googleusercontent.com/-C59v9FQor10/AAAAAAAAAAI/AAAAAAAAAAA/AAnnY7pY7bV4m6hkG7NzLE2DvvP5x2j9Mw/mo/photo.jpg?sz=50", "isDefault": true }, "isPlusUser": false, "language": "en", "verified": true }'),
        ]);
        $redirectUri = 'https://projectcitybuild.com/test_uri';
        $adapter = new GoogleOAuthAdapter($client, $this->loggerStub);

        // when...
        $user = $adapter->requestProviderAccount($redirectUri, 'auth_code');

        // expect...
        $this->assertEquals('testuser@pcbmc.co', $user->getEmail());
        $this->assertEquals('Test Account', $user->getName());
        $this->assertEquals('12345678901234567890', $user->getId());
    }
}
