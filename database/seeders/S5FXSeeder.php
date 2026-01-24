<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountTrade;
use App\Models\Position;
use App\Models\Market;
use App\Models\RiskToReward;
use App\Models\LotSize;
use App\Models\Hit;

class S5FXSeeder extends Seeder
{
    public function run(): void
    {
        // AccountTrade
        AccountTrade::create([
            'name' => 'Akun Utama',
            'balance' => 1000.00,
            'slug' => 'akun-utama-' . now()->timestamp
        ]);

        // Position
        Position::create([
            'name' => 'BUY',
            'slug' => 'buy-' . now()->timestamp
        ]);

        Position::create([
            'name' => 'SELL',
            'slug' => 'sell-' . now()->timestamp
        ]);

        // Market
        $markets = ['XAU/USD', 'GBP/USD', 'GBP/JPY', 'GBP/AUD', 'EUR/USD', 'EUR/AUD', 'USD/JPY', 'USD/AUD', 'BTC/USD'];

        foreach ($markets as $market) {
            Market::create([
                'name' => $market,
                'slug' => strtolower(str_replace('/', '-', $market)) . '-' . now()->timestamp
            ]);
        }

        // RiskToReward
        for ($i = 1; $i <= 5; $i++) {
            RiskToReward::create([
                'ratio' => "1:$i",
                'slug' => "1-$i-" . now()->timestamp
            ]);
        }

        // LotSize
        for ($i = 1; $i <= 10; $i++) {
            $size = number_format($i * 0.01, 2);
            LotSize::create([
                'size' => $size,
                'slug' => str_replace('.', '-', $size) . '-' . now()->timestamp
            ]);
        }

        // Hit
        Hit::create([
            'name' => 'TP',
            'slug' => 'tp-' . now()->timestamp
        ]);

        Hit::create([
            'name' => 'SL',
            'slug' => 'sl-' . now()->timestamp
        ]);
    }
}
