<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Modules\Users\UserAliasTypeEnum;
use App\Modules\Users\Exceptions\InvalidAliasTypeException;

class UserAliasTypeEnum_Test extends TestCase {

    /**
     * Asserts that hasValue() will return true when given an existing id
     *
     * @return void
     */
    public function testHasValue_whenValidConstant_returnsTrue() {
        $result = UserAliasTypeEnum::hasValue(1);
        $this->assertTrue($result);
    }

    /**
     * Asserts that hasValue() will return false when given an invalid id
     *
     * @return void
     */
    public function testHasValue_whenInvalidConstant_returnsFalse() {
        $result = UserAliasTypeEnum::hasValue(999);
        $this->assertFalse($result);
    }

    /**
     * Asserts that when toValue() is given a valid constant name, returns that constant's id
     *
     * @return void
     */
    public function testToValue_whenValidConstant_returnsId() {
        $result = UserAliasTypeEnum::toValue('MINECRAFT_UUID');
        $this->assertEquals(UserAliasTypeEnum::MINECRAFT_UUID, $result);
    }

    /**
     * Asserts that when toValue() is given a non-existant constant name, throws an exception
     *
     * @return void
     */
    public function testToValue_whenInvalidConstant_throwsException() {
        $this->expectException(InvalidAliasTypeException::class);
        $result = UserAliasTypeEnum::toValue('bad_constant');
    }

}
