<?php
namespace Tests\Library\Discourse\Authentication;

use Tests\TestCase;
use Domains\Library\Discourse\Authentication\DiscourseLoginHandler;
use Domains\Library\Discourse\Authentication\DiscoursePayloadValidator;
use Domains\Library\Discourse\Exceptions\BadSSOPayloadException;
use Domains\Library\Discourse\Api\DiscourseSSOApi;
use Illuminate\Log\Logger;

class DiscourseLoginHandler_Test extends TestCase
{
    private $logStub;
    private $apiMock;

    public function setUp()
    {
        parent::setUp();

        $this->logStub = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->apiMock = $this->getMockBuilder(DiscourseSSOApi::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetLoginRedirectUrl()
    {
        // given...
        $payloadValidator = new DiscoursePayloadValidator('test_key');

        $payload = [
            'nonce' => 'test_nonce',
            'return_sso_url' => 'return_url',   
        ];
        $validSSO = $payloadValidator->makePayload($payload);
        $validSignature = $payloadValidator->getSignedPayload($validSSO);

        $this->apiMock
            ->expects($this->once())
            ->method('requestNonce')
            ->willReturn([
                'sso' => $validSSO,
                'sig' => $validSignature,
            ]);

        $handler = new DiscourseLoginHandler($this->apiMock, $payloadValidator, $this->logStub);

        // when...
        $url = $handler->getRedirectUrl(111, 'test-user@pcbmc.co');

        // expect...
        $this->assertEquals('return_url?sso=ZW1haWw9dGVzdC11c2VyJTQwcGNibWMuY28mZXh0ZXJuYWxfaWQ9MTExJm5vbmNlPXRlc3Rfbm9uY2UmcmVxdWlyZV9hY3RpdmF0aW9uPWZhbHNl&sig=64c5e8ca6b44598509ed67fbdda42d5a719e3a51926ebc7bed2be84e3ab72ed5', $url);
    }

    public function testVerifyPayload_badPayload()
    {
        // given...
        $this->apiMock
            ->expects($this->once())
            ->method('requestNonce')
            ->willReturn([
                'sso' => 'bad_sso',
                'sig' => 'bad_sig',
            ]);

        $payloadValidator = new DiscoursePayloadValidator('test_key');
        $handler = new DiscourseLoginHandler($this->apiMock, $payloadValidator, $this->logStub);
        
        // expect...
        $this->expectException(BadSSOPayloadException::class);

        // when...
        $handler->getRedirectUrl(111, 'test-user@pcbmc.co');
    }
}