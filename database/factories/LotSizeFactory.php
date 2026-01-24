<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LotSizeFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'size' => fake()->randomFloat(2, 0, 99999999.99),
            'slug' => fake()->slug(),
        ];
    }
}
