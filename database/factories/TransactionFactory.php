<?php

namespace Database\Factories;

use App\Models\AccountTrade;
use App\Models\Hit;
use App\Models\LotSize;
use App\Models\Market;
use App\Models\Position;
use App\Models\RiskToReward;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'account_trade_id' => AccountTrade::factory(),
            'position_id' => Position::factory(),
            'market_id' => Market::factory(),
            'risk_to_reward_id' => RiskToReward::factory(),
            'lot_size_id' => LotSize::factory(),
            'hit_id' => Hit::factory(),
            'harga_entry' => fake()->randomFloat(5, 0, 9999999999.99999),
            'harga_sl' => fake()->randomFloat(5, 0, 9999999999.99999),
            'harga_tp' => fake()->randomFloat(5, 0, 9999999999.99999),
            'account_balance' => fake()->randomFloat(2, 0, 9999999999999.99),
            'profit_or_loss' => fake()->randomFloat(2, 0, 9999999999999.99),
            'equity' => fake()->randomFloat(2, 0, 9999999999999.99),
            'account_change' => fake()->randomFloat(2, 0, 9999999999999.99),
            'cummulative_account_change' => fake()->randomFloat(2, 0, 9999999999999.99),
            'screenshot_before' => fake()->word(),
            'screenshot_after' => fake()->word(),
            'reason' => fake()->text(),
        ];
    }
}
