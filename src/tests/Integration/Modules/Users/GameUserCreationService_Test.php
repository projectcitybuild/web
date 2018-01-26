<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use App\Modules\Users\Services\GameUserCreationService;
use App\Modules\Users\UserAliasTypeEnum;

class GameUserCreationService_Test extends TestCase {
    use RefreshDatabase;

    /**
     * Tests that when one alias is given, a game user is successfully created
     *
     * @return void
     */
    public function testCreateUser_whenAliasGiven_createsUser() {
        $test = resolve(GameUserCreationService::class);
        $result = $test->createUser([UserAliasTypeEnum::MINECRAFT_NAME => 'test123']);

        $this->assertDatabaseHas('user_aliases', [
            'user_alias_type_id' => UserAliasTypeEnum::MINECRAFT_NAME,
            'alias' => 'test123',
            'game_user_id' => $result->game_user_id,
        ]);

        $this->assertDatabaseHas('game_users', [
            'game_user_id' => $result->game_user_id,
        ]);
    }

    /**
     * Tests that when multiple aliases are given, a game user is successfully created
     *
     * @return void
     */
    public function testCreateUser_whenMultipleAliasGiven_createsUser() {
        $test = resolve(GameUserCreationService::class);
        $result = $test->createUser([
            UserAliasTypeEnum::MINECRAFT_NAME => 'first_alias',
            UserAliasTypeEnum::MINECRAFT_UUID => 'second_alias',
        ]);

        $this->assertDatabaseHas('user_aliases', [
            'user_alias_type_id' => UserAliasTypeEnum::MINECRAFT_NAME,
            'alias' => 'first_alias',
            'game_user_id' => $result->game_user_id,
        ]);
        $this->assertDatabaseHas('user_aliases', [
            'user_alias_type_id' => UserAliasTypeEnum::MINECRAFT_UUID,
            'alias' => 'second_alias',
            'game_user_id' => $result->game_user_id,
        ]);
        $this->assertDatabaseHas('game_users', [
            'game_user_id' => $result->game_user_id,
        ]);
    }
}
