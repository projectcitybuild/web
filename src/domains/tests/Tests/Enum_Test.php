<?php
namespace Tests;

use Tests\TestCase;
use Infrastructure\Enum;

class TestEnum extends Enum {
    const Key1 = 'value1';
    const Key2 = 'value2';
}

class Enum_Test extends TestCase
{
    public function testConstructFromRawValue()
    {
        $enum = new TestEnum(TestEnum::Key1);

        $this->assertNotNull($enum);
        $this->assertInstanceOf(TestEnum::class, $enum);
    }

    public function testCanConstructStatically()
    {
        $enum = TestEnum::Key1();

        $this->assertNotNull($enum);
        $this->assertInstanceOf(TestEnum::class, $enum);

        $this->assertEquals(TestEnum::Key1, $enum->valueOf());
    }

    public function testFailsWhenBadKey()
    {
        $this->expectException(\InvalidArgumentException::class);
        $enum = new TestEnum('bad_key');
    }

    public function testStaticFailsWhenBadKey()
    {
        $this->expectException(\InvalidArgumentException::class);
        $enum = TestEnum::bad_key();
    }

    public function testCanGetValue()
    {
        $enum = new TestEnum(TestEnum::Key1);
        $this->assertEquals(TestEnum::Key1, $enum->valueOf());
    }
    
    public function testCanGetKeys()
    {
        $keys = TestEnum::keys();
        $this->assertEquals(['Key1', 'Key2'], $keys);
    }

    public function testCanGetValues()
    {
        $values = TestEnum::values();
        $this->assertEquals(['value1', 'value2'], $values);
    }

    public function testCanGetConstants()
    {
        $constants = TestEnum::constants();
        $this->assertEquals($constants, [
            'Key1' => 'value1',
            'Key2' => 'value2',
        ]);
    }
}
