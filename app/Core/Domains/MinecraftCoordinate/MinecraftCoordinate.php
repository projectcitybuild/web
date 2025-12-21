<?php

namespace App\Core\Domains\MinecraftCoordinate;

class MinecraftCoordinate
{
    public function __construct(
        readonly String $world,
        readonly int $x,
        readonly int $y,
        readonly int $z,
        readonly float $pitch,
        readonly float $yaw,
    ) {}

    public static function fromValidatedRequest(array $validated): MinecraftCoordinate
    {
        return new MinecraftCoordinate(
            world: $validated['world'],
            x: $validated['x'],
            y: $validated['y'],
            z: $validated['z'],
            pitch: $validated['pitch'],
            yaw: $validated['yaw'],
        );
    }

    public function toArray(): array
    {
        return [
            'world' => $this->world,
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
            'pitch' => $this->pitch,
            'yaw' => $this->yaw,
        ];
    }
}
