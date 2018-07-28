<?php
namespace Tests\Library\OAuth;

use Tests\TestCase;
use Domains\Library\OAuth\Adapters\Discord\DiscordOAuthAdapter;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Log\Logger;

class OAuthDiscordAdapter_Test extends TestCase
{
    private $loggerStub;
    private $clientStub;
    
    public function setUp() 
    {
        parent::setUp();
        
        $this->loggerStub = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->clientStub = $this->getMockBuilder(Client::class)
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
        $adapter = new DiscordOAuthAdapter($this->clientStub, $this->loggerStub);
        $redirectUri = 'https://projectcitybuild.com/test_uri';

        // when...
        $result = $adapter->requestProviderLoginUrl($redirectUri);

        // expect...
        $expectedClientId = config('services.discord.client_id');
        $expectedRedirectUri = rawurlencode($redirectUri);
        $expectedUri = 'https://discordapp.com/api/oauth2/authorize?client_id='.$expectedClientId.'&redirect_uri='.$expectedRedirectUri.'&response_type=code&scope=identify%20email';
        
        $this->assertEquals($expectedUri, $result);
    }

    public function testGetProviderAccount_succeeds()
    {
        // given...
        $client = $this->getClientWithResponses([
            new Response(200, [], '{"access_token": "vdoIfMJNaZBBnGyE2461ra5HxTnW4X", "token_type": "Bearer", "expires_in": 604800, "refresh_token": "y4iaxIlfSj4cHh5YOKi3uk4lsXSykG", "scope": "identify email"}'),
            new Response(200, [], '{"username": "test_user", "verified": true, "locale": "en-US", "mfa_enabled": false, "id": "12345678901234567890", "avatar": "887d4e404fe4d54731790635a6627fea", "discriminator": "1086", "email": "testuser@pcbmc.co"}'),
        ]);
        $redirectUri = 'https://projectcitybuild.com/test_uri';
        $adapter = new DiscordOAuthAdapter($client, $this->loggerStub);

        // when...
        $user = $adapter->requestProviderAccount($redirectUri, 'auth_code');

        // expect...
        $this->assertEquals('testuser@pcbmc.co', $user->getEmail());
        $this->assertEquals('test_user', $user->getName());
        $this->assertEquals('12345678901234567890', $user->getId());
    }
}
