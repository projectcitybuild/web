<?php

namespace Tests\Unit\App\Rules;

use App\Core\Rules\DiscourseUsernameRule;
use Tests\TestCase;

class DiscourseUsernameRuleTest extends TestCase
{
    private $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new DiscourseUsernameRule;
    }

    public function test_blank_username_is_invalid()
    {
        $this->assertFalse($this->rule->passes('username', ''));
    }

    public function test_username_too_short()
    {
        $this->assertFalse($this->rule->passes('username', 'abc'));
    }

    public function test_username_with_min_length()
    {
        $this->assertTrue($this->rule->passes('username', 'abcd'));
    }

    public function test_username_too_long()
    {
        $this->assertFalse($this->rule->passes('username', 'abcdefgjijklmnopqrstu'));
    }

    public function test_username_with_max_length()
    {
        $this->assertTrue($this->rule->passes('username', 'abcdefgjijklmnopqrst'));
    }

    public function test_valid_characters()
    {
        $this->assertTrue($this->rule->passes('username', 'ab-cd.123_ABC-xYz'));
    }

    public function test_invalid_characters()
    {
        $this->assertFalse($this->rule->passes('username', 'abc|'));
        $this->assertFalse($this->rule->passes('username', 'a#bc'));
        $this->assertFalse($this->rule->passes('username', 'abc xyz'));
    }

    public function test_starting_with_alphanumeric_or_underscore()
    {
        $this->assertTrue($this->rule->passes('username', 'abcd'));
        $this->assertTrue($this->rule->passes('username', '1abc'));
        $this->assertTrue($this->rule->passes('username', '_abc'));
    }

    public function test_starting_with_dot_or_dash()
    {
        $this->assertFalse($this->rule->passes('username', '.abc'));
        $this->assertFalse($this->rule->passes('username', '-abc'));
    }

    public function test_ending_with_alphanumeric()
    {
        $this->assertTrue($this->rule->passes('username', 'abcd'));
        $this->assertTrue($this->rule->passes('username', 'abc9'));
    }

    public function test_ending_with_invalid_character()
    {
        $this->assertFalse($this->rule->passes('username', 'abc_'));
        $this->assertFalse($this->rule->passes('username', 'abc.'));
        $this->assertFalse($this->rule->passes('username', 'abc-'));
    }

    public function test_consecutive_special_characters()
    {
        $this->assertFalse($this->rule->passes('username', 'a__bc'));
        $this->assertFalse($this->rule->passes('username', 'a..bc'));
        $this->assertFalse($this->rule->passes('username', 'a--bc'));
    }

    public function test_file_extension_ending()
    {
        $this->assertFalse($this->rule->passes('username', 'abc.json'));
        $this->assertFalse($this->rule->passes('username', 'abc.png'));
    }

    public function test_unicode_characters()
    {
        $this->assertFalse($this->rule->passes('username', 'abcö'));
        $this->assertFalse($this->rule->passes('username', 'abc象'));
    }
}
