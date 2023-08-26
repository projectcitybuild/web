<?php

namespace Tests\Unit\Models\Eloquent;

use App\Models\Eloquent\Account;
use App\Models\Eloquent\MinecraftPlayer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_avatar_url_with_linked_minecraft_player(): void
    {
        $account = Account::factory()->create();

        MinecraftPlayer::factory()
            ->for($account)
            ->create(['uuid' => 'test-uuid']);

        $this->assertEquals(
            expected: "https://minotar.net/avatar/testuuid",
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
