<?php

namespace App\Domains\Captcha\Validator\Adapters;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleRecaptchaValidatorTest extends TestCase
{
    public function test_passes()
    {
        Http::fake([
            '*' => Http::response(
                body: '{"success":true,"challenge_ts":"2018-08-01T11:17:32Z","hostname":"projectcitybuild.com"}'
            ),
        ]);
        $validator = new TurntileCaptchaValidator();

        $this->assertTrue($validator->passed('token', 'ip'));
    }

    public function test_fails()
    {
        Http::fake([
            '*' => Http::response(
                body: '{"success":false}'
            ),
        ]);
        $validator = new TurntileCaptchaValidator();

        $this->assertFalse($validator->passed('token', 'ip'));
    }
}
