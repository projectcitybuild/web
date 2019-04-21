<?php
namespace Tests\Library\OAuth;

use Tests\TestCase;
use Domains\Library\OAuth\OAuthLoginHandler;
use Domains\Library\OAuth\OAuthAdapterFactory;
use Domains\Library\OAuth\Storage\OAuthMemoryStorage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class OAuthLoginHandler_Test extends TestCase
{
    /**
     * @var OAuthSessionStorage
     */
    private $memoryCache;
    
    /**
     * @var OAuthAdapterFactory
     */
    private $adapterFactory;

    /**
     * @var Request
     */
    private $request;


    protected function setUp(): void
    {
        parent::setUp();

        $this->memoryCache = new OAuthMemoryStorage();
        $this->adapterFactory = new OAuthAdapterFactory();
        $this->request = resolve(Request::class);
    }

    public function testPreservesRedirectUri()
    {
        $loginHandler = new OAuthLoginHandler($this->memoryCache, $this->adapterFactory, $this->request);
        $redirectUri = 'test_redirect_uri';
        
        $loginHandler->redirectToLogin('discord', $redirectUri);
        
        $this->assertEquals($redirectUri, $this->memoryCache->get());
    }

    public function testReturnsRedirectUri()
    {
        $loginHandler = new OAuthLoginHandler($this->memoryCache, $this->adapterFactory, $this->request);

        $result = $loginHandler->redirectToLogin('discord', 'test_redirect_uri');
        
        $this->assertInstanceOf(RedirectResponse::class, $result);
    }
}
