<?php
namespace Tests\Library\OAuth;

use Tests\TestCase;
use Domains\Library\OAuth\Adapters\Facebook\FacebookOAuthAdapter;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Log\Logger;

class OAuthFacebookAdapter_Test extends TestCase
{
    private $loggerStub;
    private $clientMock;
    
    public function setUp() 
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
        $adapter = new FacebookOAuthAdapter($this->clientMock, $this->loggerStub);
        $redirectUri = 'https://projectcitybuild.com/test_uri';

        // when...
        $result = $adapter->requestProviderLoginUrl($redirectUri);

        // expect...
        $expectedClientId = config('services.facebook.client_id');
        $expectedRedirectUri = rawurlencode($redirectUri);
        $expectedUri = 'https://www.facebook.com/v3.1/dialog/oauth?client_id='.$expectedClientId.'&redirect_uri='.$expectedRedirectUri.'&scope=email';
        
        $this->assertEquals($expectedUri, $result);
    }

    public function testGetProviderAccount_succeeds()
    {
        // given...
        $client = $this->getClientWithResponses([
            new Response(200, [], '{ "access_token": "test_access_token", "token_type": "bearer", "expires_in": 5178516 }'),
            new Response(200, [], '{ "name": "Test User", "email": "testuser@pcbmc.co","id":"12345678901234567890"}'),
        ]);
        $redirectUri = 'https://projectcitybuild.com/test_uri';
        $adapter = new FacebookOAuthAdapter($client, $this->loggerStub);

        // when...
        $user = $adapter->requestProviderAccount($redirectUri, 'auth_code');

        // expect...
        $this->assertEquals('testuser@pcbmc.co', $user->getEmail());
        $this->assertEquals('Test User', $user->getName());
        $this->assertEquals('12345678901234567890', $user->getId());
    }
}
