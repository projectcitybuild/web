<?php
namespace Domains\Helpers;

use Illuminate\Database\Eloquent\Relations\Relation;

class MorphMapHelpers
{
    /**
     * Returns the key that belongs to the given class resolution path
     *
     * @see AppServiceProvider.php
     * @param string $classResolvePath
     *
     * @return string
     */
    public static function getMorphKeyOf(string $classResolvePath) : string
    {
        $map = Relation::morphMap();
        $key = array_search($classResolvePath, $map);

        if ($key === false) {
            throw new \Exception('Morph Map does not contain value: '.$classResolvePath);
        }
        return $key;
    }
}
