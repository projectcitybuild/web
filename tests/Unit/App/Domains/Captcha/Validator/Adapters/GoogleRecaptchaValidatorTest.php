<?php

namespace Tests\Unit\App\Domains\Captcha\Validator\Adapters;

use App\Domains\Captcha\Validator\Adapters\TurntileCaptchaValidator;
use Illuminate\Support\Facades\Http;

it('passes when the captcha provider accepts the token', function () {
    Http::fake([
        '*' => Http::response(
            body: '{"success":true,"challenge_ts":"2018-08-01T11:17:32Z","hostname":"projectcitybuild.com"}'
        ),
    ]);
    $validator = new TurntileCaptchaValidator;

    $this->assertTrue($validator->passed('token', 'ip'));
});

it('fails when the captcha provider rejects the token', function () {
    Http::fake([
        '*' => Http::response(
            body: '{"success":false}'
        ),
    ]);
    $validator = new TurntileCaptchaValidator;

    $this->assertFalse($validator->passed('token', 'ip'));
});
