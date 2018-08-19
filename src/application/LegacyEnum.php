<?php
namespace Application;

/**
 * TODO: delete this
 * @deprecated Use Enum instead
 */
abstract class LegacyEnum
{
    /**
     * Returns all constants as a KvP array
     *
     * @deprecated Use Enum instead
     * @return array
     */
    public static function getConstants()
    {
        $class = new \ReflectionClass(get_called_class());
        return $class->getConstants() ?: [];
    }

    /**
     * Returns all constant keys
     *
     * @deprecated Use Enum instead
     * @return array
     */
    public static function getKeys()
    {
        return array_keys(self::getConstants()) ?: [];
    }

    /**
     * Returns all constant values
     *
     * @deprecated Use Enum instead
     * @return void
     */
    public static function getValues()
    {
        return array_values(self::getConstants()) ?: [];
    }

    /**
     * Converts the given key to its value
     *
     * @deprecated Use Enum instead
     * @param string $key
     *
     * @throws \Exception
     * @return string
     */
    public static function toValue(string $key) : string
    {
        $constants = self::getConstants() ?: [];
        if (array_key_exists($key, $constants) === false) {
            throw new \Exception(get_called_class() . ' does not contain key ['.$key.']');
        }
        return $constants[$key];
    }
}
