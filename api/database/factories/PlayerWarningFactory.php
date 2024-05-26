<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\PlayerWarning;
use Carbon\Carbon;

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
            'additional_info' => (rand(0, 1) == 0) ? $this->faker->text : null,
            'weight' => $this->faker->randomNumber(nbDigits: 1),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'acknowledged_at' => (rand(0, 1) == 0)
                ? $this->faker->dateTimeBetween('-5 years', 'now')
                : null,
        ];
    }

    public function warnedBy(Player|Factory|null $minecraftPlayer): PlayerWarningFactory
    {
        return $this->for($minecraftPlayer, 'warnerPlayer');
    }

    public function warnedPlayer(Player|Factory $player): PlayerWarningFactory
    {
        return $this->for($player, 'warnedPlayer');
    }

    public function withPlayers(): PlayerWarningFactory
    {
        return $this
            ->warnedPlayer(Player::factory())
            ->warnedBy(Player::factory());
    }

    public function acknowledged(bool $isAcknowledged = true): PlayerWarningFactory
    {
        return $this->state(function (array $attributes) use ($isAcknowledged) {
            if ($isAcknowledged) {
                return ['acknowledged_at' => now()->subWeek()];
            } else {
                return ['acknowledged_at' => null];
            }
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
