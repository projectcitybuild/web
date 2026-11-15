<?php

namespace Database\Factories;

use App\Models\MinecraftPlayer;
use App\Models\PlayerMail;
use App\Models\PlayerMailRecipient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlayerMailRecipient>
 */
class PlayerMailRecipientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mail_id' => PlayerMail::factory(),
            'player_id' => MinecraftPlayer::factory(),
            'read_at' => null,
            'received_at' => now(),
        ];
    }
}
