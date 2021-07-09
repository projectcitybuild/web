<?php


namespace App\Library\Auditing;


use Altek\Accountant\Contracts\Cipher;
use Altek\Accountant\Exceptions\DecipherException;

class FullRedact implements Cipher
{

    public static function isOneWay(): bool
    {
        return true;
    }

    public static function cipher($value)
    {
        $length = mb_strlen($value);
        return str_pad('', $length, '-');
    }

    public static function decipher($value)
    {
        throw new DecipherException('Value deciphering is not supported by this implementation', $value);
    }
}
