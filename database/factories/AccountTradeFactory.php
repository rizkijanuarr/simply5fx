<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AccountTradeFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'balance' => fake()->randomFloat(2, 0, 9999999999999.99),
            'slug' => fake()->slug(),
        ];
    }
}
