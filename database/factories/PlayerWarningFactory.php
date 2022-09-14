<?php

namespace Database\Factories;

use Carbon\Carbon;
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

        $isAcknowledged = rand(0, 1) == 0;

        return [
            'id' => $this->faker->randomNumber(),
            'reason' => $this->faker->sentence,
            'weight' => $this->faker->randomNumber(nbDigits: 1),
            'is_acknowledged' => $isAcknowledged,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'acknowledged_at' => ($isAcknowledged) ? $this->faker->dateTimeBetween('-5 years', 'now') : null,
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

    public function withPlayers(): PlayerWarningFactory
    {
        return $this
            ->warnedPlayer(MinecraftPlayer::factory())
            ->warnedBy(MinecraftPlayer::factory());
    }

    public function acknowledged(): PlayerWarningFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_acknowledged' => true,
                'acknowledged_at' => now()->subWeek(),
            ];
        });
    }

    public function createdAt(Carbon $date)
    {
        return $this->state(function (array $attributes) use ($date) {
            return [
                'created_at' => $date,
                'updated_at' => $date,
            ];
        });
    }
}
