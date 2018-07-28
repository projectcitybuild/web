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


    public function setUp()
    {
        parent::setUp();

        $this->memoryCache = new OAuthMemoryStorage();
        $this->adapterFactory = new OAuthAdapterFactory();
        $this->request = resolve(Request::class);
    }

    public function testPreservesRedirectUri()
    {
        // given...
        $loginHandler = new OAuthLoginHandler($this->memoryCache, $this->adapterFactory, $this->request);
        $loginHandler->setProvider('discord');
        $redirectUri = 'test_redirect_uri';
        
        // when...
        $loginHandler->redirectToLogin($redirectUri);
        
        // expect...
        $this->assertEquals($redirectUri, $this->memoryCache->get());
    }

    public function testReturnsRedirectUri()
    {
        // given...
        $loginHandler = new OAuthLoginHandler($this->memoryCache, $this->adapterFactory, $this->request);
        $loginHandler->setProvider('discord');
        
        // when...
        $result = $loginHandler->redirectToLogin('test_redirect_uri');
        
        // expect...
        $this->assertInstanceOf(RedirectResponse::class, $result);
    }
}
