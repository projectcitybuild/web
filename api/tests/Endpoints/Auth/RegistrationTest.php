<?php

namespace Tests\Endpoints\Auth;

use App\Models\Eloquent\Account;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/register';
    private const METHOD = 'POST';

    public function test_new_users_can_register(): void
    {
        Notification::fake();

        $this
            ->json(method: self::METHOD, uri: self::ENDPOINT, data: [
                'username' => 'user',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertStatus(200);

        $account = Account::where('email', 'test@example.com')->first();
        $this->assertNotNull($account);
        $this->assertEquals('user', $account->username);
        $this->assertNull($account->verified_at);

        Notification::assertSentTo($account, VerifyEmail::class);
    }

    public function test_email_must_be_unique(): void
    {
        Account::factory()->create(['email' => 'test@example.com']);

        $this
            ->json(method: self::METHOD, uri: self::ENDPOINT, data: [
                'username' => 'user',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertJsonValidationErrorFor('email')
            ->assertStatus(422);
    }

    public function test_username_must_be_unique(): void
    {
        Account::factory()->create(['username' => 'user']);

        $this
            ->json(method: self::METHOD, uri: self::ENDPOINT, data: [
                'username' => 'user',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertJsonValidationErrorFor('username')
            ->assertStatus(422);
    }
}
