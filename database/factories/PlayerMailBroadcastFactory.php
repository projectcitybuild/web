<?php

namespace Database\Factories;

use App\Domains\PlayerMail\Data\PlayerMailBroadcastStatus;
use App\Models\PlayerMail;
use App\Models\PlayerMailBroadcast;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlayerMailBroadcast>
 */
class PlayerMailBroadcastFactory extends Factory
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
            'status' => PlayerMailBroadcastStatus::QUEUED,
            'max_player_id' => 0,
            'last_player_id' => 0,
            'recipients_count' => 0,
            'started_at' => null,
            'completed_at' => null,
        ];
    }
}
