<?php

namespace Database\Factories;

use Domain\ShowcaseApplications\Entities\ApplicationStatus;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\ShowcaseApplication;

/**
 * @extends Factory<ShowcaseApplication>
 */
class ShowcaseApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ShowcaseApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->name,
            'name' => $this->faker->slug,
            'creators' => $this->faker->name,
            'description' => $this->faker->sentence,
            'location_x' => $this->faker->randomNumber(),
            'location_y' => $this->faker->randomNumber(),
            'location_z' => $this->faker->randomNumber(),
            'location_pitch' => $this->faker->randomFloat(nbMaxDecimals: 1, max: 500),
            'location_yaw' => $this->faker->randomFloat(nbMaxDecimals: 1, max: 500),
            'location_world' => $this->faker->randomElement(['creative', 'survival', 'monarch']),
            'built_at' => $this->faker->dateTime(),
            'status' => ApplicationStatus::PENDING,
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }

    public function approved(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ApplicationStatus::APPROVED,
                'decided_at' => now(),
                'decider_account_id' => Account::factory(),
            ];
        });
    }

    public function denied(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ApplicationStatus::DENIED,
                'decision_note' => $this->faker->paragraph,
                'decided_at' => now(),
                'decider_account_id' => Account::factory(),
            ];
        });
    }
}
