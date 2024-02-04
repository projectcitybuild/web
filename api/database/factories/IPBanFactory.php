<?php

namespace Database\Factories;

use App\Models\Eloquent\IPBan;
use App\Models\Eloquent\Player;
use Domain\Bans\UnbanType;
use Illuminate\Database\Eloquent\Factories\Factory;

class IPBanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = IPBan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-5 years', 'now');

        return [
            'ip_address' => $this->faker->unique()->ipv4,
            'reason' => $this->faker->sentence,
            'created_at' => $date,
            'updated_at' => $date,
            'unbanned_at' => null,
            'unbanner_player_id' => null,
            'unban_type' => null,
        ];
    }

    public function bannedBy(Player|Factory|null $minecraftPlayer): IPBanFactory
    {
        return $this->for($minecraftPlayer, 'bannerPlayer');
    }

    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'unbanned_at' => now()->subDay(),
                'unban_type' => UnbanType::MANUAL,
            ];
        });
    }
}
