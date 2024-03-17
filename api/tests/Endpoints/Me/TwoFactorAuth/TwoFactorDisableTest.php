<?php

namespace Tests\Endpoints\Me\TwoFactorAuth;

use App\Models\Eloquent\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TwoFactorDisableTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/me/2fa';
    private const METHOD = 'DELETE';

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
            ->assertStatus(400);
    }

    public function test_throws_if_incorrect_password()
    {
        $user = Account::factory()->create([
            'password' => Hash::make('password'),
            'two_factor_secret' => 'secret',
            'two_factor_recovery_codes' => 'codes',
            'two_factor_confirmed_at' => now(),
        ]);

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT, data: [
                'password' => 'invalid',
            ])
            ->assertStatus(401);
    }

    public function test_deletes_2fa_data()
    {
        $user = Account::factory()->create([
            'password' => Hash::make('password'),
            'two_factor_secret' => 'secret',
            'two_factor_recovery_codes' => 'codes',
            'two_factor_confirmed_at' => now(),
        ]);

        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_recovery_codes);
        $this->assertNotNull($user->two_factor_confirmed_at);

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT, data: [
                'password' => 'password',
            ])
            ->assertStatus(200);

        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_recovery_codes);
        $this->assertNull($user->two_factor_confirmed_at);
    }
}
