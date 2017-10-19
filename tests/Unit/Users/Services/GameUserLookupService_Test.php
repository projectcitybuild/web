<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Modules\Users\Services\GameUserLookupService;
use App\Modules\Users\Repositories\GameUserRepository;
use App\Modules\Users\Repositories\UserAliasRepository;
use App\Modules\Users\Models\UserAliasType;
use App\Modules\Users\Exceptions\InvalidAliasTypeException;

class GameUserLookupService_Test extends TestCase {

    private $gameUserRepositoryMock;
    private $userAliasRepositoryMock;

    public function setUp() {
        parent::setUp();

        $this->gameUserRepositoryMock = $this->getMockBuilder(GameUserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->userAliasRepositoryMock = $this->getMockBuilder(UserAliasRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Tests that when given a valid alias type name, returns the expected
     * alias type id
     *
     * @return void
     */
    public function testGetAliasTypeId_whenValidType_returnsId() {
        $userAliasType = new UserAliasType();
        $userAliasType->forceFill(['user_alias_type_id' => 1]);

        $this->userAliasRepositoryMock
            ->method('getAliasType')
            ->will($this->returnValue($userAliasType));

        $lookupService = new GameUserLookupService($this->gameUserRepositoryMock, $this->userAliasRepositoryMock);
        $test = $lookupService->getAliasTypeId('MINECRAFT_UUID');

        $this->assertEquals(1, $test);
    }

    /**
     * Tests that when given a non-existing alias type name, throws an exception
     *
     * @return void
     */
    public function testGetAliasTypeId_whenInvalidType_throwsException() {
        $this->expectException(InvalidAliasTypeException::class);

        $lookupService = new GameUserLookupService($this->gameUserRepositoryMock, $this->userAliasRepositoryMock);
        $test = $lookupService->getAliasTypeId('invalid_type_name');
    }

}
