<?php

namespace Database\Factories;

use App\Domains\PlayerReports\Data\PlayerReportStatus;
use App\Domains\PlayerReports\Data\PlayerReportVisibility;
use App\Models\PlayerReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlayerReport>
 */
class PlayerReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reporter_name' => fake()->userName(),
            'reporter_contact' => fake()->email(),
            'reporter_email' => fake()->safeEmail(),
            'reported_player_name' => fake()->userName(),
            'subject' => fake()->sentence(4),
            'details' => fake()->paragraph(),
            'visibility' => PlayerReportVisibility::STANDARD,
            'status' => PlayerReportStatus::OPEN,
        ];
    }
}
