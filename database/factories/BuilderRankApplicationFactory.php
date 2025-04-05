<?php

namespace Database\Factories;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Models\BuilderRankApplication;

class BuilderRankApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BuilderRankApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'minecraft_alias' => $this->faker->name,
            'current_builder_rank' => $this->faker->word,
            'build_location' => $this->faker->sentence,
            'build_description' => $this->faker->paragraph,
            'additional_notes' => $this->faker->paragraph,
            'status' => 1,
        ];
    }

    public function status(ApplicationStatus $status)
    {
        return $this->state(function (array $attributes) use ($status) {
            return [
                'status' => $status->value,
            ];
        });
    }

    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ApplicationStatus::APPROVED,
                'closed_at' => now()->addDays(rand(0, 30)),
            ];
        });
    }

    public function denied()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ApplicationStatus::DENIED,
                'denied_reason' => $this->faker->paragraph,
                'closed_at' => now()->addDays(rand(0, 30)),
            ];
        });
    }
}
