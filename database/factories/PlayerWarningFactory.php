<?php

namespace Database\Factories;

use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\PlayerWarning;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerWarningFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlayerWarning::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $createdAt = $this->faker->dateTimeBetween('-5 years', 'now');

        return [
            'reason' => $this->faker->sentence,
            'weight' => $this->faker->randomNumber(nbDigits: 1),
            'is_acknowledged' => false,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }

    public function warnedBy(MinecraftPlayer|Factory|null $minecraftPlayer): PlayerWarningFactory
    {
        return $this->for($minecraftPlayer, 'warnerPlayer');
    }

    public function warnedPlayer(MinecraftPlayer|Factory $player): PlayerWarningFactory
    {
        return $this->for($player, 'warnedPlayer');
    }
}
