<?php
namespace App\Shared;

abstract class Enum {

    /**
     * Returns all constants as a KvP array
     *
     * @return array
     */
    public static function getConstants() {
        $class = new \ReflectionClass(get_called_class());
        return $class->getConstants() ?: [];
    }

    /**
     * Returns all constant keys
     *
     * @return array
     */
    public static function getKeys() {
        return array_keys(self::getConstants()) ?: [];
    }

    /**
     * Returns all constant values
     *
     * @return void
     */
    public static function getValues() {
        return array_values(self::getConstants()) ?: [];
    }

}