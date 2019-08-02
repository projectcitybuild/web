<?php
namespace Tests\Library\Discourse\Authentication;

use Tests\TestCase;
use App\Library\Discourse\Authentication\DiscoursePayloadValidator;

class DiscoursePayloadValidator_Test extends TestCase
{
    public function testPayloadSigning()
    {
        // given...
        $payload = 'test_payload';
        $expectedSignedPayload = '043514f10ccc49602dad6b3f430dacd3911c412476e1331d9967e01f66f91d9a';
        $validator = new DiscoursePayloadValidator('test_key');

        // when...
        $signedPayload = $validator->getSignedPayload($payload);

        // expect...
        $this->assertEquals($expectedSignedPayload, $signedPayload);
    }

    public function testPayloadValidation_withValidPayload()
    {
        // given...
        $payload = 'test_payload';
        $expectedSignedPayload = '043514f10ccc49602dad6b3f430dacd3911c412476e1331d9967e01f66f91d9a';
        $validator = new DiscoursePayloadValidator('test_key');

        // when...
        $isValidPayload = $validator->isValidPayload($payload, $expectedSignedPayload);

        // expect...
        $this->assertTrue($isValidPayload);
    }

    public function testPayloadValidation_withBadPayload()
    {
        // given...
        $payload = 'test_payload';
        $expectedSignedPayload = '043514f10ccc49602dad6b3f430dacd3911c412476e1331d9967e01f66f91d9a';
        $validator = new DiscoursePayloadValidator('valid_key');

        // when...
        $isValidPayload = $validator->isValidPayload($payload, $expectedSignedPayload);

        // expect...
        $this->assertFalse($isValidPayload);
    }

    public function testPayloadUnpacking()
    {
        // given...
        $payload = 'nonce=test_nonce&return_sso_url=test_url';
        $payload = urlencode($payload);
        $payload = base64_encode($payload);

        $validator = new DiscoursePayloadValidator('');

        // when...
        $unpacked = $validator->unpackPayload($payload);

        // expect...
        $this->assertEquals([
            'nonce' => 'test_nonce',
            'return_sso_url' => 'test_url',
        ], $unpacked);
    }

    public function testMakePayload()
    {
        // given...
        $validator = new DiscoursePayloadValidator('test_key');
        $expectedSignedPayload = 'bm9uY2U9dGVzdF9ub25jZSZyZXR1cm5fc3NvX3VybD10ZXN0X3VybA==';

        // when...
        $signedPayload = $validator->makePayload([
            'nonce' => 'test_nonce',
            'return_sso_url' => 'test_url',
        ]);

        // expect...
        $this->assertEquals($expectedSignedPayload, $signedPayload);
    }

    public function testMakeRedirectUrl()
    {
        // given...
        $expectedUrl = 'test_url?sso=test_sso&sig=test_sig';
        $validator = new DiscoursePayloadValidator('test_key');
        
        // when...
        $redirectUrl = $validator->getRedirectUrl('test_url', 'test_sso', 'test_sig');

        // expect...
        $this->assertEquals($expectedUrl, $redirectUrl);
    }
}
