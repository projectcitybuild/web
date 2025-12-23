<?php

namespace App\Core\Domains\MinecraftCoordinate;

class MinecraftCoordinate
{
    public function __construct(
        public readonly string $world,
        public readonly int $x,
        public readonly int $y,
        public readonly int $z,
        public readonly float $pitch,
        public readonly float $yaw,
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
