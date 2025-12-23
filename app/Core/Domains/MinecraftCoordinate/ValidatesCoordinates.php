<?php

namespace App\Core\Domains\MinecraftCoordinate;

/**
 * A set of validation rules for a Minecraft coordinate
 */
trait ValidatesCoordinates
{
    protected array $coordinateRules = [
        'world' => ['required', 'string'],
        'x' => ['required', 'numeric'],
        'y' => ['required', 'numeric'],
        'z' => ['required', 'numeric'],
        'pitch' => ['required', 'numeric'],
        'yaw' => ['required', 'numeric'],
    ];
    protected array $optionalCoordinateRules = [
        'world' => ['string'],
        'x' => ['numeric'],
        'y' => ['numeric'],
        'z' => ['numeric'],
        'pitch' => ['numeric'],
        'yaw' => ['numeric'],
    ];
}
