<?php

namespace Database\Factories;

use App\Domains\PlayerMail\Data\MailCategory;
use App\Models\MinecraftPlayer;
use App\Models\PlayerMail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlayerMail>
 */
class PlayerMailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_player_id' => MinecraftPlayer::factory(),
            'sender_name' => $this->faker->userName(),
            'created_by_account_id' => null,
            'category' => MailCategory::DIRECT,
            'subject' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
        ];
    }
}
