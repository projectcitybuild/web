<?php

namespace Tests\Endpoints\Me\TwoFactorAuth;

use App\Models\Account;
use Database\Factories\AccountFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\HasRateLimitedRoute;
use Tests\TestCase;

class TwoFactorDisableTest extends TestCase
{
    use RefreshDatabase;
    use HasRateLimitedRoute;

    private const ENDPOINT = 'api/me/2fa';
    private const METHOD = 'DELETE';

    public function test_must_be_logged_in()
    {
        $response = $this->json(method: self::METHOD, uri: self::ENDPOINT);
        $response->assertStatus(401);
    }

    public function test_throws_if_2fa_not_enabled()
    {
        $user = Account::factory()
            ->passwordHashed()
            ->create();

        $this->assertNull($user->two_factor_secret);

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT, data: [
                'password' => 'password',
            ])
            ->assertJsonValidationErrorFor('2fa')
            ->assertStatus(422);
    }

    public function test_throws_if_incorrect_password()
    {
        $user = Account::factory()
            ->passwordHashed()
            ->verified2FA()
            ->create();

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT, data: [
                'password' => 'invalid',
            ])
            ->assertJsonValidationErrorFor('password')
            ->assertStatus(422);
    }

    public function test_is_rate_limited()
    {
        $user = Account::factory()
            ->passwordHashed()
            ->verified2FA()
            ->create();

        $this->hitRateLimit(name: 'password-confirm'.$user->getKey());

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT, data: [
                'password' => 'invalid',
            ])
            ->assertStatus(429);

        $this->resetRateLimit('password-confirm'.$user->getKey());
    }

    public function test_deletes_2fa_data()
    {
        $user = Account::factory()
            ->passwordHashed()
            ->verified2FA()
            ->create();

        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_recovery_codes);
        $this->assertNotNull($user->two_factor_confirmed_at);

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT, data: [
                'password' => AccountFactory::UNHASHED_PASSWORD,
            ])
            ->assertStatus(200);

        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_recovery_codes);
        $this->assertNull($user->two_factor_confirmed_at);
    }
}
