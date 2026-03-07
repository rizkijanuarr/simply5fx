<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class ProfitChart extends ChartWidget
{
    use InteractsWithPageFilters;
    
    protected static ?string $pollingInterval = null;
    protected static ?string $heading = 'Profit Trend';
    protected static ?string $maxHeight = '300px';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 3; // Tampil paling bawah

    protected function getData(): array
    {
        $startDate = $this->filters['start_date'] ? new Carbon($this->filters['start_date']) : now()->startOfMonth();
        $endDate = $this->filters['end_date'] ? new Carbon($this->filters['end_date']) : now();
        $accountId = $this->filters['account_trade_id'] ?? null;

        // Query for WIN transactions
        $profitQuery = Transaction::query()
            ->whereNotNull('hit_id')
            ->whereHas('hit', function ($q) {
                $q->whereRaw('UPPER(name) IN (?, ?)', ['WIN', 'TP']);
            })
            ->when($accountId, fn ($query) => $query->where('account_trade_id', $accountId));

        // Get trend data
        $data = Trend::query($profitQuery)
            ->between(start: $startDate, end: $endDate)
            ->perDay()
            ->count();

        // Calculate actual profit values per day
        $profitValues = [];
        foreach ($data as $value) {
            $dayTransactions = Transaction::query()
                ->whereDate('created_at', $value->date)
                ->whereNotNull('hit_id')
                ->whereHas('hit', function ($q) {
                    $q->whereRaw('UPPER(name) IN (?, ?)', ['WIN', 'TP']);
                })
                ->when($accountId, fn ($query) => $query->where('account_trade_id', $accountId))
                ->get();

            $dayProfit = $dayTransactions->sum(function ($transaction) {
                return floatval(str_replace(['+', '$', ' '], '', $transaction->profit ?? '0'));
            });

            $profitValues[] = $dayProfit;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Profit ($)',
                    'data' => $profitValues,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, .1)',
                    'fill' => 'start',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => (new Carbon($value->date))->format('d M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
