<?php

namespace Tests\Endpoints\Me\TwoFactorAuth;

use App\Models\Eloquent\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoFactorEnableTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/me/2fa';
    private const METHOD = 'POST';

    public function test_must_be_logged_in()
    {
        $response = $this->json(method: self::METHOD, uri: self::ENDPOINT);
        $response->assertStatus(401);
    }

    public function test_throws_if_already_enabled()
    {
        $user = Account::factory()->create([
            'two_factor_secret' => 'secret',
        ]);
        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT)
            ->assertStatus(400);
    }

    public function test_generates_2fa_secret()
    {
        $user = Account::factory()->create();

        $this->assertNull($user->two_factor_secret);

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT)
            ->assertStatus(200);

        $this->assertNotNull($user->two_factor_secret);
    }
}
