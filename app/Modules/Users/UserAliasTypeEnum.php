<?php
namespace App\Modules\Users;

use App\Modules\Users\Exceptions\InvalidAliasTypeException;

abstract class UserAliasTypeEnum {
    const MINECRAFT_UUID = 1;
    const MINECRAFT_NAME = 2;
    const STEAM_ID = 3;


    private static $cache = null;

    /**
     * Uses reflection to return (and cache) all the enum keys and values
     *
     * @return array
     */
    private static function getConstants() {
        if(is_null(self::$cache)) {
            $class = new \ReflectionClass(__CLASS__);
            self::$cache = $class->getConstants();

            return self::$cache;
        }

        return self::$cache;
    }

    /**
     * Returns whether the given int is a valid enum id
     *
     * @param int $value
     * @return boolean
     */
    public static function hasValue(int $value) : bool {
        $constantValues = array_values(self::getConstants());
        return in_array($value, $constantValues);
    }

    /**
     * Converts a string into an enum id. Throws an exception if invalid
     *
     * @param string $constant
     * @throws InvalidAliasTypeException
     * @return void
     */
    public static function toValue(string $constant) : int {
        $constants = self::getConstants();
        $constantKeys = array_keys($constants);

        if(!in_array($constant, $constantKeys)) {
            throw new InvalidAliasTypeException($constant . ' is not a valid AliasTypeEnum [' . implode(',', $constantKeys) . ']');
        }

        return $constants[$constant];
    }

}