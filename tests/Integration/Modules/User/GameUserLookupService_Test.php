<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use App\Modules\Users\Services\GameUserLookupService;
use App\Modules\Users\Models\UserAlias;
use App\Modules\Users\Models\GameUser;
use App\Modules\Users\UserAliasTypeEnum;

class GameUserLookupService_Test extends TestCase {
    use RefreshDatabase;

    /**
     * Tests that getGameUser() can find the correct existing user
     *
     * @return void
     */
    public function testGetGameUser_whenUserExists_returnsUser() {
        $gameUser = GameUser::create();
        $alias = UserAlias::create([
            'user_alias_type_id' => UserAliasTypeEnum::MINECRAFT_NAME,
            'alias' => 'test123',
            'game_user_id' => $gameUser->game_user_id,
        ]);

        $test = resolve(GameUserLookupService::class);
        $result = $test->getGameUser(UserAliasTypeEnum::MINECRAFT_NAME, 'test123');

        $this->assertEquals($gameUser->game_user_id, $result->game_user_id);
    }

    /**
     * Tests that getGameUser() returns null when the user does not exist
     *
     * @return void
     */
    public function testGetGameUser_whenUserDoesNotExists_returnsNull() {
        $test = resolve(GameUserLookupService::class);
        $result = $test->getGameUser(UserAliasTypeEnum::MINECRAFT_NAME, 'ghost_user');

        $this->assertNull($result);
    }

    /**
     * Tests that getOrCreateGameUser() returns a user when it exists
     *
     * @return void
     */
    public function testGetOrCreateGameUser_whenUserExists_returnsUser() {
        $gameUser = GameUser::create();
        $alias = UserAlias::create([
            'user_alias_type_id' => UserAliasTypeEnum::MINECRAFT_NAME,
            'alias' => 'test123',
            'game_user_id' => $gameUser->game_user_id,
        ]);

        $test = resolve(GameUserLookupService::class);
        $result = $test->getOrCreateGameUser(UserAliasTypeEnum::MINECRAFT_NAME, 'test123');

        $this->assertEquals($gameUser->game_user_id, $result->game_user_id);
    }

    /**
     * Tests that getOrCreateGameUser() creates a new game user when it does not exist
     *
     * @return void
     */
    public function testGetOrCreateGameUser_whenUserDoesNotExists_createsUser() {
        $test = resolve(GameUserLookupService::class);
        $result = $test->getOrCreateGameUser(UserAliasTypeEnum::MINECRAFT_NAME, 'test123');
        
        $this->assertDatabaseHas('user_aliases', [
            'user_alias_type_id' => UserAliasTypeEnum::MINECRAFT_NAME,
            'alias' => 'test123',
            'game_user_id' => $result->game_user_id,
        ]);

        $this->assertDatabaseHas('game_users', [
            'game_user_id' => $result->game_user_id,
        ]);
    }

}
