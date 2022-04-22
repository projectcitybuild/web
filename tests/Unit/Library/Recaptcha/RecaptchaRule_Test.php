<?php

namespace Tests\Library\Recaptcha;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Library\Recaptcha\Rules\RecaptchaRule;
use Tests\TestCase;

class RecaptchaRule_Test extends TestCase
{
    private $logStub;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logStub = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        RecaptchaRule::disable();
    }

    private function getClientWithResponses(array $responses)
    {
        $mock = new MockHandler($responses);
        $mockHandler = HandlerStack::create($mock);
        $client = new Client(['handler' => $mockHandler]);

        return $client;
    }

    public function testPasses_success_passes()
    {
        // given...
        $client = $this->getClientWithResponses([
            new Response(200, [], '{"success":true,"challenge_ts":"2018-08-01T11:17:32Z","hostname":"projectcitybuild.com"}'),
        ]);
        $rule = new RecaptchaRule($client, new Request, $this->logStub);

        // when...
        $passes = $rule->passes(null, null);

        // expect...
        $this->assertTrue($passes);
    }

    public function testPasses_rejected_fails()
    {
        // given...
        $client = $this->getClientWithResponses([
            new Response(200, [], '{"success":false}'),
        ]);
        $rule = new RecaptchaRule($client, new Request, $this->logStub);

        // when...
        $passes = $rule->passes(null, null);

        // expect...
        $this->assertFalse($passes);
    }
}
