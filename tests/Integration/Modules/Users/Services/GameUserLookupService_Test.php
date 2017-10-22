<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Modules\Users\Services\GameUserLookupService;
use App\Modules\Users\Repositories\GameUserRepository;
use App\Modules\Users\Repositories\UserAliasRepository;
use App\Modules\Users\Models\UserAlias;
use App\Modules\Users\UserAliasTypeEnum;
use App\Modules\Users\Models\GameUser;

class GameUserLookupService_Test extends TestCase {
    use DatabaseMigrations, DatabaseTransactions;

    private $userRepository;
    private $aliasRepository;

    public function setUp() {
        parent::setUp();

        $this->userRepository  = app()->make(GameUserRepository::class);
        $this->aliasRepository = app()->make(UserAliasRepository::class);
    }

    /**
     * Creates a fake game user for the current test
     *
     * @return void
     */
    private function createFakeGameUser() {
        $gameUser = GameUser::forceCreate([
            'game_user_id' => 150,
            'user_id' => null,
        ]);

        $alias = UserAlias::create([
            'user_alias_type_id' => UserAliasTypeEnum::MINECRAFT_UUID,
            'game_user_id' => $gameUser->game_user_id,
            'alias' => 'fake_uuid',
        ]);
    }

    /**
     * Asserts that when the alias of an existing player is given
     * that player is returned.
     *
     * @return void
     */
    public function test_whenExistingUser_returnsUser() {
        $this->createFakeGameUser();

        $service = new GameUserLookupService($this->userRepository, $this->aliasRepository);
        $result = $service->getOrCreateGameUserId(UserAliasTypeEnum::MINECRAFT_UUID, 'fake_uuid');

        $this->assertEquals(150, $result);
    }

    /**
     * Asserts that when the alias of a non-existing player is given
     * a new user alias and game user is created
     *
     * @return void
     */
    public function test_whenNonExistentUser_createsUserAlias() {
        $service = new GameUserLookupService($this->userRepository, $this->aliasRepository);
        $service->getOrCreateGameUserId(UserAliasTypeEnum::MINECRAFT_UUID, 'new_user_uuid');

        $this->assertDatabaseHas('user_aliases', [
            'user_alias_type_id' => UserAliasTypeEnum::MINECRAFT_UUID,
            'alias' => 'new_user_uuid',
        ]);
        
        $this->assertDatabaseHas('game_users', [
            'user_id' => null,
        ]);
    }

}
