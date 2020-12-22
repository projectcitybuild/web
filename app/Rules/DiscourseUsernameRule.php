<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * PHP implementation of
 * https://github.com/discourse/discourse/blob/888e68a1637ca784a7bf51a6bbb524dcf7413b13/app/models/username_validator.rb
 *
 * @package App\Rules
 */
class DiscourseUsernameRule implements Rule
{

    // usernames must consist of a-z A-Z 0-9 _ - .
    private const ASCII_INVALID_CHARACTERS = "/[^\w.-]/";
    // usernames can start with a-z A-Z 0-0 _
    private const VALID_LEADING_CHARACTERS = '/^[a-zA-Z0-9_]/';
    // usernames must end with a-z A-Z 0-9
    private const VALID_TRAILING_CHARACTERS = '/[a-zA-Z0-9]$/';
    // underscores, dashes and dots can't be repeated consecutively
    private const REPEATING_CONFUSING_CHARACTERS = '/[-_.]{2,}/';

    private const CONFUSING_EXTENSIONS = "/\.(js|json|css|htm|html|xml|jpg|jpeg|png|gif|bmp|ico|tif|tiff|woff)$/i";
    private $value;
    private $errors = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->value = $value;

        return $this->minimumLength() &&
            $this->maximumLength() &&
            $this->charactersValid() &&
            $this->firstCharacterValid() &&
            $this->lastCharacterValid() &&
            $this->noDoubleSpecial() &&
            $this->noConfusingExtension();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return implode('. ', $this->errors);
    }

    private function rule($passes, $message)
    {
        if (! $passes) {
            $this->errors[] = $message;
        }

        return $passes;
    }

    private function minimumLength()
    {
        return $this->rule(strlen($this->value) > 3, 'Your username is too short');
    }

    private function maximumLength()
    {
        return $this->rule(strlen($this->value) <= 20, 'Your username is too long');
    }

    private function charactersValid()
    {
        return $this->rule(! preg_match(self::ASCII_INVALID_CHARACTERS, $this->value), 'Username must only include numbers, letters, dashes, and underscores');
    }

    private function firstCharacterValid()
    {
        return $this->rule(preg_match(self::VALID_LEADING_CHARACTERS, $this->value), 'Usernames must begin with a letter, a number or an underscore');
    }

    private function lastCharacterValid()
    {
        return $this->rule(preg_match(self::VALID_TRAILING_CHARACTERS, $this->value), 'Usernames must end with a letter or a number');
    }

    private function noDoubleSpecial()
    {
        return $this->rule(! preg_match(self::REPEATING_CONFUSING_CHARACTERS, $this->value), 'Usernames must not contain a sequence of 2 or more special chars (.-_)');
    }

    private function noConfusingExtension()
    {
        return $this->rule(! preg_match(self::CONFUSING_EXTENSIONS, $this->value), 'Usernames must not end with a confusing suffix like .json or .png etc.');
    }
}
