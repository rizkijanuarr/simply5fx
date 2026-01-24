<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Filament\Resources\TransactionResource\Pages\ListTransactions;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class TransactionStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListTransactions::class;
    }

    protected function getStats(): array
    {
        // Get filtered transactions
        $query = $this->getPageTableQuery();
        $transactions = $query->with(['hit', 'accountTrade'])->get();
        
        // Calculate metrics
        $totalWin = $transactions->filter(function ($transaction) {
            if (!$transaction->hit) return false;
            $hitName = strtoupper(trim($transaction->hit->name));
            return in_array($hitName, ['WIN', 'TP']);
        })->count();

        $totalLose = $transactions->filter(function ($transaction) {
            if (!$transaction->hit) return false;
            $hitName = strtoupper(trim($transaction->hit->name));
            return in_array($hitName, ['LOSE', 'SL']);
        })->count();

        $totalProfit = $transactions->filter(function ($transaction) {
            if (!$transaction->hit) return false;
            $hitName = strtoupper(trim($transaction->hit->name));
            return in_array($hitName, ['WIN', 'TP']);
        })->sum(function ($transaction) {
            return floatval(str_replace(['+', '$', ' '], '', $transaction->profit ?? '0'));
        });

        $totalLoss = $transactions->filter(function ($transaction) {
            if (!$transaction->hit) return false;
            $hitName = strtoupper(trim($transaction->hit->name));
            return in_array($hitName, ['LOSE', 'SL']);
        })->sum(function ($transaction) {
            return floatval(str_replace(['-', '$', ' '], '', $transaction->rugi ?? '0'));
        });

        $netProfit = $totalProfit - $totalLoss;

        // Get current balance from unique accounts
        $accountIds = $transactions->pluck('account_trade_id')->unique()->filter();
        $currentBalance = 0;
        foreach ($accountIds as $accountId) {
            $account = \App\Models\AccountTrade::find($accountId);
            if ($account) {
                $balance = floatval(str_replace(['$', ' ', ','], '', $account->balance ?? '0'));
                $currentBalance += $balance;
            }
        }

        // Calculate drawdown (largest loss as percentage of balance)
        $maxDrawdownValue = $transactions->filter(function ($transaction) {
            if (!$transaction->hit) return false;
            $hitName = strtoupper(trim($transaction->hit->name));
            return in_array($hitName, ['LOSE', 'SL']);
        })->max(function ($transaction) {
            return floatval(str_replace(['-', '$', ' '], '', $transaction->rugi ?? '0'));
        }) ?? 0;

        // Calculate drawdown percentage (max loss / current balance * 100)
        $drawdownPercentage = $currentBalance > 0 ? ($maxDrawdownValue / $currentBalance) * 100 : 0;

        // Trend data - Last 7 days
        $netProfitTrend = Trend::query(
            Transaction::query()
                ->whereNotNull('hit_id')
                ->whereHas('hit')
        )
            ->between(
                start: now()->subDays(6),
                end: now(),
            )
            ->perDay()
            ->count();

        $winTrend = Trend::query(
            Transaction::query()
                ->whereNotNull('hit_id')
                ->whereHas('hit', function ($q) {
                    $q->whereRaw('UPPER(name) IN (?, ?)', ['WIN', 'TP']);
                })
        )
            ->between(
                start: now()->subDays(6),
                end: now(),
            )
            ->perDay()
            ->count();

        $loseTrend = Trend::query(
            Transaction::query()
                ->whereNotNull('hit_id')
                ->whereHas('hit', function ($q) {
                    $q->whereRaw('UPPER(name) IN (?, ?)', ['LOSE', 'SL']);
                })
        )
            ->between(
                start: now()->subDays(6),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            Stat::make('Net Profit/Loss', '$' . number_format($netProfit, 2, '.', ','))
                ->description($netProfit >= 0 ? 'Positive return' : 'Negative return')
                ->descriptionIcon($netProfit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($netProfitTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color($netProfit >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Current Balance', '$' . number_format($currentBalance, 2, '.', ','))
                ->description('Total account balance')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info')
                ->icon('heroicon-o-wallet'),

            Stat::make('Win/Lose Ratio', $totalWin . 'W / ' . $totalLose . 'L')
                ->description($totalWin + $totalLose > 0 ? number_format(($totalWin / ($totalWin + $totalLose)) * 100, 1) . '% win rate' : 'No trades')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->chart($winTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color($totalWin > $totalLose ? 'success' : ($totalWin < $totalLose ? 'danger' : 'warning'))
                ->icon('heroicon-o-trophy'),

            Stat::make('Total Profit', '$' . number_format($totalProfit, 2, '.', ','))
                ->description('From ' . $totalWin . ' winning trades')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($winTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color('success')
                ->icon('heroicon-o-arrow-up-circle'),

            Stat::make('Total Loss', '$' . number_format($totalLoss, 2, '.', ','))
                ->description('From ' . $totalLose . ' losing trades')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart($loseTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color('danger')
                ->icon('heroicon-o-arrow-down-circle'),

            Stat::make('Max Drawdown', number_format($drawdownPercentage, 2) . '%')
                ->description('$' . number_format($maxDrawdownValue, 2, '.', ',') . ' largest loss')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->chart($loseTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color('warning')
                ->icon('heroicon-o-exclamation-circle'),
        ];
    }
}
