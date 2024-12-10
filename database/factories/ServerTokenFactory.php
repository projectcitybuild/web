<?php

namespace Database\Factories;

use App\Models\Server;
use App\Models\ServerToken;
use Illuminate\Support\Str;

class ServerTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ServerToken::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'token' => Str::random(),
            'server_id' => Server::factory()->create()->getKey(),
        ];
    }
}
