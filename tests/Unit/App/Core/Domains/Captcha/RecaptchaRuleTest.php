<?php

namespace App\Core\Domains\Captcha;

use App\Core\Domains\Captcha\Rules\CaptchaRule;
use App\Core\Domains\Captcha\Validator\Adapters\StubCaptchaValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class RecaptchaRuleTest extends TestCase
{
    public function test_passes()
    {
        $rule = new CaptchaRule(
            request: App::make(Request::class),
            recaptchaValidator: new StubCaptchaValidator(passed: true),
        );
        $this->assertTrue(
            $rule->passes(attribute: null, value: 'token')
        );
    }

    public function testPasses_rejected_fails()
    {
        $rule = new CaptchaRule(
            request: App::make(Request::class),
            recaptchaValidator: new StubCaptchaValidator(passed: false),
        );
        $this->assertFalse(
            $rule->passes(attribute: null, value: 'token')
        );
    }
}
