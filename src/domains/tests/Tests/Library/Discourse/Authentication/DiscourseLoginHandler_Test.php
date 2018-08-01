<?php
namespace Tests\Library\RateLimit;

use Tests\TestCase;
use Domains\Library\Discourse\Authentication\DiscourseLoginHandler;
use Illuminate\Log\Logger;
use Domains\Library\Discourse\Authentication\DiscourseNonceStorage;
use Domains\Library\Discourse\Authentication\DiscoursePayloadValidator;
use Domains\Library\Discourse\Exceptions\BadSSOPayloadException;

class DiscourseLoginHandler_Test extends TestCase
{
    private $logStub;
    private $storageMock;

    public function setUp()
    {
        parent::setUp();

        $this->logStub = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->storageMock = $this->getMockBuilder(DiscourseNonceStorage::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testVerifyPayload_validPayload()
    {
        // given...
        $payloadValidator = new DiscoursePayloadValidator('test_key');
        $handler = new DiscourseLoginHandler($this->storageMock, $payloadValidator, $this->logStub);
        
        $payload = [
            'nonce' => 'test_nonce',
            'return_sso_url' => 'return_url',   
        ];
        $validSSO = $payloadValidator->makePayload($payload);
        $validSignature = $payloadValidator->getSignedPayload($validSSO);
        
        // expect...
        $this->storageMock
            ->expects($this->once())
            ->method('store')
            ->with(
                $this->equalTo($payload['nonce']), 
                $this->equalTo($payload['return_sso_url'])
            );

        // when...
        $handler->verifyAndStorePayload($validSSO, $validSignature);
    }

    public function testVerifyPayload_badPayload()
    {
        // given...
        $payloadValidator = new DiscoursePayloadValidator('test_key');
        $handler = new DiscourseLoginHandler($this->storageMock, $payloadValidator, $this->logStub);
        
        // expect...
        $this->expectException(BadSSOPayloadException::class);

        // when...
        $handler->verifyAndStorePayload('bad_payload', 'valid_payload');
    }

    public function testGetLoginRedirectUrl()
    {
        // given...
        $this->storageMock->method('get')
            ->willReturn([
                'nonce' => 'test_nonce',
                'return_uri' => 'test_return_uri',
            ]);

        $payloadValidator = new DiscoursePayloadValidator('test_key');
        $handler = new DiscourseLoginHandler($this->storageMock, $payloadValidator, $this->logStub);

        // when...
        $url = $handler->getLoginRedirectUrl(111, 'test-user@pcbmc.co');

        // expect...
        $this->assertEquals('test_return_uri?sso=ZW1haWw9dGVzdC11c2VyJTQwcGNibWMuY28mZXh0ZXJuYWxfaWQ9MTExJm5vbmNlPXRlc3Rfbm9uY2UmcmVxdWlyZV9hY3RpdmF0aW9uPWZhbHNl&sig=64c5e8ca6b44598509ed67fbdda42d5a719e3a51926ebc7bed2be84e3ab72ed5', $url);
    }
}