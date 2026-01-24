<?php

namespace App\Filament\Widgets;

use App\Models\AccountTrade;
use App\Models\Transaction;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class TransactionStats extends BaseWidget
{
    use InteractsWithPageFilters;
    
    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 1; // Tampil paling atas

    protected function getStats(): array
    {
        $startDate = $this->filters['start_date'] ? new Carbon($this->filters['start_date']) : now()->startOfMonth();
        $endDate = $this->filters['end_date'] ? new Carbon($this->filters['end_date']) : now();
        $accountId = $this->filters['account_trade_id'] ?? null;

        // Query transactions with filters
        $transactionsQuery = Transaction::query()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->when($accountId, fn ($query) => $query->where('account_trade_id', $accountId));

        $transactions = $transactionsQuery->with('hit')->get();

        // Count total transactions
        $totalTransactions = $transactions->count();

        // Calculate total profit (only WIN/TP)
        $totalProfit = $transactions->filter(function ($transaction) {
            if (!$transaction->hit) return false;
            $hitName = strtoupper(trim($transaction->hit->name));
            return in_array($hitName, ['WIN', 'TP']);
        })->sum(function ($transaction) {
            return floatval(str_replace(['+', '$', ' '], '', $transaction->profit ?? '0'));
        });

        // Count unique accounts
        $totalAccounts = $accountId 
            ? 1 
            : AccountTrade::query()
                ->whereHas('transactions', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('transaction_date', [$startDate, $endDate]);
                })
                ->count();

        return [
            Stat::make('Total Transactions', number_format($totalTransactions))
                ->icon('heroicon-o-document-text')
                ->description('Transactions in period')
                ->color('primary'),

            Stat::make('Total Profit', '$' . number_format($totalProfit, 2, '.', ','))
                ->icon('heroicon-o-banknotes')
                ->description('From winning trades')
                ->color('success'),

            Stat::make('Total Accounts', number_format($totalAccounts))
                ->icon('heroicon-o-building-storefront')
                ->description('Active accounts')
                ->color('info'),
        ];
    }
}
