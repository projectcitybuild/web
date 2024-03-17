<?php

namespace Tests\Endpoints\Me\TwoFactorAuth;

use App\Models\Eloquent\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use RobThree\Auth\TwoFactorAuth;
use Tests\TestCase;

class TwoFactorConfirmTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/me/2fa/confirm';
    private const METHOD = 'POST';

    public function test_must_be_logged_in()
    {
        $response = $this->json(method: self::METHOD, uri: self::ENDPOINT);
        $response->assertStatus(401);
    }

    public function test_throws_if_2fa_not_enabled()
    {
        $user = Account::factory()->create();

        $this->assertNull($user->two_factor_secret);

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT)
            ->assertJsonValidationErrorFor('2fa')
            ->assertStatus(422);
    }

    public function test_throws_if_2fa_already_confirmed()
    {
        $user = Account::factory()
            ->verified2FA()
            ->create();

        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_confirmed_at);

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

    public function test_confirms_valid_code()
    {
        $user = Account::factory()
            ->enabled2FA()
            ->create(['two_factor_secret' => encrypt('secret')]);

        $this->assertNull($user->two_factor_confirmed_at);

        $this->mock(TwoFactorAuth::class, function (MockInterface $mock) {
            $mock->shouldReceive('verifyCode')
                ->andReturn(true);
        });

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT, data: [
                'code' => '123456',
            ])
            ->assertStatus(200);

        $this->assertNotNull($user->two_factor_confirmed_at);
    }
}
