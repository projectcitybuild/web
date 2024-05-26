<?php

namespace Tests\Unit\Models;

use App\Core\MinecraftUUID\MinecraftUUID;
use Tests\TestCase;

class MinecraftUUIDTest extends TestCase
{
    private const UUID_TRIMMED = 'bee2c0bb2f5b47ce93f9734b3d7fef5f';
    private const UUID_FULL = 'bee2c0bb-2f5b-47ce-93f9-734b3d7fef5f';

    public function test_constructor_invalid_uuid_throws(): void
    {
        $uuidRaw = 'invalid_uuid';
        $this->assertThrows(
            test: fn () => new MinecraftUUID($uuidRaw),
            expectedClass: \Exception::class,
        );
    }

    public function test_constructor_valid_uuid_does_not_throw(): void
    {
        $this->expectNotToPerformAssertions();

        new MinecraftUUID(self::UUID_FULL);
        new MinecraftUUID(self::UUID_TRIMMED);
    }

    public function test_tryParse_returns_null_if_invalid_uuid(): void
    {
        $this->assertNull(
            MinecraftUUID::tryParse('invalid_uuid'),
        );
    }

    public function test_tryParse_returns_valid_uuid(): void
    {
        $uuid = MinecraftUUID::tryParse(self::UUID_FULL);
        $this->assertEquals($uuid, new MinecraftUUID(self::UUID_FULL));

        $uuid = MinecraftUUID::tryParse(self::UUID_TRIMMED);
        $this->assertEquals($uuid, new MinecraftUUID(self::UUID_TRIMMED));
    }

    public function test_trimmed_conversions(): void
    {
        $uuid = new MinecraftUUID(self::UUID_TRIMMED);

        $this->assertEquals(self::UUID_FULL, actual: $uuid->full());
        $this->assertEquals(self::UUID_TRIMMED, actual: $uuid->trimmed());
    }

    public function test_full_conversions(): void
    {
        $uuid = new MinecraftUUID(self::UUID_FULL);

        $this->assertEquals(self::UUID_FULL, actual: $uuid->full());
        $this->assertEquals(self::UUID_TRIMMED, actual: $uuid->trimmed());
    }
}
