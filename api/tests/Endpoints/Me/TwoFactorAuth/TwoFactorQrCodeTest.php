<?php

namespace Tests\Endpoints\Me\TwoFactorAuth;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use RobThree\Auth\Providers\Qr\IQRCodeProvider;
use Tests\TestCase;

class TwoFactorQrCodeTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/me/2fa/qr';
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

    public function test_generates_qr_code()
    {
        $user = Account::factory()
            ->enabled2FA()
            ->create([
                'two_factor_secret' => encrypt('secret'), // Requires a hashed secret
            ]);

        $imageData = 'abcdefg';
        $this->mock(IQRCodeProvider::class, function (MockInterface $mock) use ($imageData) {
            $mock->shouldReceive('getQRCodeImage')
                ->andReturn($imageData);

            $mock->shouldReceive('getMimeType')
                ->andReturn('image/png');
        });

        $this->actingAs($user)
            ->json(method: self::METHOD, uri: self::ENDPOINT)
            ->assertStatus(200)
            ->assertJson([
              'qr' => 'data:image/png;base64,'.base64_encode($imageData),
            ]);
    }
}
