<?php

namespace Tests\Endpoints\Auth;

use App\Models\Eloquent\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use RobThree\Auth\TwoFactorAuth;
use Tests\HasRateLimitedRoute;
use Tests\TestCase;

class TwoFactorChallengeTest extends TestCase
{
    use RefreshDatabase;
    use HasRateLimitedRoute;

    private const ENDPOINT = 'api/2fa/challenge';
    private const METHOD = 'POST';

    public function test_throws_if_2fa_not_enabled()
    {
        $user = Account::factory()->create();

        $this->assertNull($user->two_factor_secret);

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT)
            ->assertJsonValidationErrorFor('2fa')
            ->assertStatus(422);
    }

    public function test_throws_if_no_code()
    {
        $user = Account::factory()
            ->enabled2FA()
            ->create();

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT, data: [])
            ->assertJsonValidationErrorFor('code')
            ->assertStatus(422);
    }

    public function test_throws_if_incorrect_code()
    {
        $user = Account::factory()
            ->enabled2FA()
            ->create(['two_factor_secret' => encrypt('secret')]);

        $this->mock(TwoFactorAuth::class, function (MockInterface $mock) {
           $mock->shouldReceive('verifyCode')
               ->andReturn(false);
        });

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT, data: [
                'code' => 'invalid',
            ])
            ->assertJsonValidationErrorFor('code')
            ->assertStatus(422);
    }

    public function test_is_rate_limited()
    {
        $user = Account::factory()
            ->passwordHashed()
            ->verified2FA()
            ->create();

        $this->hitRateLimit(name: 'two-factor'.$user->getKey());

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT, data: [
                'code' => 'invalid',
            ])
            ->assertStatus(429);

        $this->resetRateLimit('two-factor'.$user->getKey());
    }

    public function test_valid_code()
    {
        $user = Account::factory()
            ->enabled2FA()
            ->create(['two_factor_secret' => encrypt('secret')]);

        $this->mock(TwoFactorAuth::class, function (MockInterface $mock) {
            $mock->shouldReceive('verifyCode')
                ->andReturn(true);
        });

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT, data: [
                'code' => '123456',
            ])
            ->assertStatus(200);
    }
}
