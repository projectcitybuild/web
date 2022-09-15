<?php

namespace Database\Factories;

use Entities\Models\Eloquent\GameIPBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameIPBanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GameIPBan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-5 years', 'now');

        return [
            'ip_address' => $this->faker->ipv4,
            'reason' => $this->faker->sentence,
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }

    public function bannedBy(MinecraftPlayer|Factory|null $minecraftPlayer): GameIPBanFactory
    {
        return $this->for($minecraftPlayer, 'bannerPlayer');
    }
}
