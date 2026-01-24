<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RiskToRewardFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ratio' => fake()->word(),
            'slug' => fake()->slug(),
        ];
    }
}
