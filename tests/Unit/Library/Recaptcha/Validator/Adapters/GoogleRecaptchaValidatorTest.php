<?php

namespace Tests\Unit\Library\Recaptcha\Validator\Adapters;

use App\Core\Domains\Recaptcha\Validator\Adapters\GoogleRecaptchaValidator;
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
        $validator = new GoogleRecaptchaValidator();

        $this->assertTrue($validator->passed('token', 'ip'));
    }

    public function test_fails()
    {
        Http::fake([
            '*' => Http::response(
                body: '{"success":false}'
            ),
        ]);
        $validator = new GoogleRecaptchaValidator();

        $this->assertFalse($validator->passed('token', 'ip'));
    }
}
