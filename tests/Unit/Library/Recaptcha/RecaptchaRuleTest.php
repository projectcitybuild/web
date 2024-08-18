<?php

namespace Tests\Unit\Library\Recaptcha;

use App\Core\Domains\Recaptcha\Rules\RecaptchaRule;
use App\Core\Domains\Recaptcha\Validator\Adapters\StubRecaptchaValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class RecaptchaRuleTest extends TestCase
{
    public function test_passes()
    {
        $rule = new RecaptchaRule(
            request: App::make(Request::class),
            recaptchaValidator: new StubRecaptchaValidator(passed: true),
        );
        $this->assertTrue(
            $rule->passes(attribute: null, value: 'token')
        );
    }

    public function testPasses_rejected_fails()
    {
        $rule = new RecaptchaRule(
            request: App::make(Request::class),
            recaptchaValidator: new StubRecaptchaValidator(passed: false),
        );
        $this->assertFalse(
            $rule->passes(attribute: null, value: 'token')
        );
    }
}
