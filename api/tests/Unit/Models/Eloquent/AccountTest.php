<?php

namespace Tests\Unit\Models\Eloquent;

use App\Models\Account;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_avatar_url_with_linked_minecraft_player(): void
    {
        $account = Account::factory()->create();
        $uuid = 'bee2c0bb-2f5b-47ce-93f9-734b3d7fef5f';

        Player::factory()
            ->for($account)
            ->create(['uuid' => $uuid]);

        $this->assertEquals(
            expected: "https://minotar.net/avatar/bee2c0bb2f5b47ce93f9734b3d7fef5f",
            actual: $account->avatarUrl,
        );
    }

    public function test_avatar_url_with_only_username(): void
    {
        $account = Account::factory()
            ->create(['username' => 'test_username']);

        $this->assertEquals(
            expected: "https://ui-avatars.com/api/?name=test_username",
            actual: $account->avatarUrl,
        );
    }

    public function test_avatar_url_with_no_username(): void
    {
        $account = Account::factory()
            ->create(['username' => null]);

        $this->assertNull($account->avatarUrl);
    }
}
