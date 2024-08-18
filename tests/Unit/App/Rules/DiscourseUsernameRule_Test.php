<?php

namespace Tests\Unit\App\Rules;

use App\Core\Rules\DiscourseUsernameRule;
use Tests\TestCase;

class DiscourseUsernameRule_Test extends TestCase
{
    private $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new DiscourseUsernameRule();
    }

    public function testBlankUsernameIsInvalid()
    {
        $this->assertFalse($this->rule->passes('username', ''));
    }

    public function testUsernameTooShort()
    {
        $this->assertFalse($this->rule->passes('username', 'abc'));
    }

    public function testUsernameWithMinLength()
    {
        $this->assertTrue($this->rule->passes('username', 'abcd'));
    }

    public function testUsernameTooLong()
    {
        $this->assertFalse($this->rule->passes('username', 'abcdefgjijklmnopqrstu'));
    }

    public function testUsernameWithMaxLength()
    {
        $this->assertTrue($this->rule->passes('username', 'abcdefgjijklmnopqrst'));
    }

    public function testValidCharacters()
    {
        $this->assertTrue($this->rule->passes('username', 'ab-cd.123_ABC-xYz'));
    }

    public function testInvalidCharacters()
    {
        $this->assertFalse($this->rule->passes('username', 'abc|'));
        $this->assertFalse($this->rule->passes('username', 'a#bc'));
        $this->assertFalse($this->rule->passes('username', 'abc xyz'));
    }

    public function testStartingWithAlphanumericOrUnderscore()
    {
        $this->assertTrue($this->rule->passes('username', 'abcd'));
        $this->assertTrue($this->rule->passes('username', '1abc'));
        $this->assertTrue($this->rule->passes('username', '_abc'));
    }

    public function testStartingWithDotOrDash()
    {
        $this->assertFalse($this->rule->passes('username', '.abc'));
        $this->assertFalse($this->rule->passes('username', '-abc'));
    }

    public function testEndingWithAlphanumeric()
    {
        $this->assertTrue($this->rule->passes('username', 'abcd'));
        $this->assertTrue($this->rule->passes('username', 'abc9'));
    }

    public function testEndingWithInvalidCharacter()
    {
        $this->assertFalse($this->rule->passes('username', 'abc_'));
        $this->assertFalse($this->rule->passes('username', 'abc.'));
        $this->assertFalse($this->rule->passes('username', 'abc-'));
    }

    public function testConsecutiveSpecialCharacters()
    {
        $this->assertFalse($this->rule->passes('username', 'a__bc'));
        $this->assertFalse($this->rule->passes('username', 'a..bc'));
        $this->assertFalse($this->rule->passes('username', 'a--bc'));
    }

    public function testFileExtensionEnding()
    {
        $this->assertFalse($this->rule->passes('username', 'abc.json'));
        $this->assertFalse($this->rule->passes('username', 'abc.png'));
    }

    public function testUnicodeCharacters()
    {
        $this->assertFalse($this->rule->passes('username', 'abcö'));
        $this->assertFalse($this->rule->passes('username', 'abc象'));
    }
}
