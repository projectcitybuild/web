<?php

namespace Database\Factories;

use App\Entities\Models\Eloquent\MinecraftLootBox;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MinecraftLootBoxFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MinecraftLootBox::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'loot_box_name' => Str::random(10),
            'quantity' => $this->faker->randomNumber(2, false),
            'is_active' => true,
        ];
    }

    /**
     * Indicates that the box cannot be redeemed anymore.
     */
    public function inactive(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}
