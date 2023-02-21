<?php

namespace Database\Factories;

abstract class Factory extends \Illuminate\Database\Eloquent\Factories\Factory
{
    /**
     * Set created_at and updated_at to now.
     * Useful when using make() in cases where timestamps are still needed.
     */
    public function withTimestamps()
    {
        return $this->state(function (array $attributes) {
            return [
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });
    }
}
