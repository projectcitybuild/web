<?php

namespace Tests\Endpoints\Me\TwoFactorAuth;

use App\Models\Eloquent\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TwoFactorRecoveryCodesTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/me/2fa/recovery-codes';
    private const METHOD = 'GET';

    public function test_must_be_logged_in()
    {
        $response = $this->json(method: self::METHOD, uri: self::ENDPOINT);
        $response->assertStatus(401);
    }

    public function test_throws_if_not_enabled()
    {
        $user = Account::factory()->create();

        $this->assertNull($user->two_factor_secret);

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT)
            ->assertStatus(400);
    }

    public function test_generates_recovery_codes()
    {
        $user = Account::factory()
            ->enabled2FA()
            ->create();

        $this->assertNull($user->two_factor_recovery_codes);

        Str::createRandomStringsUsingSequence([
            '0', // Something internal is calling Str::random once...
            '1', 'a',
            '2', 'b',
            '3', 'c',
            '4', 'd',
            '5', 'e',
            '6', 'f',
            '7', 'g',
            '8', 'h',
        ]);

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT)
            ->dump()
            ->assertStatus(200)
            ->assertJson([
              'recovery_codes' => [
                  '1-a',
                  '2-b',
                  '3-c',
                  '4-d',
                  '5-e',
                  '6-f',
                  '7-g',
                  '8-h',
              ],
            ]);

        $this->assertNotNull($user->two_factor_recovery_codes);

        Str::createRandomStringsNormally();
    }
}
