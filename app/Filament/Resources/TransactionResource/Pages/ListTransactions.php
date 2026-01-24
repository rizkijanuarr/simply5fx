<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Pages\Concerns\ExposesTableToWidgets;


class ListTransactions extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(Transaction::count())
                ->badgeColor('primary'),

            'win' => Tab::make('WIN')
                ->badge(Transaction::whereHas('hit', function (Builder $query) {
                    $query->whereRaw('UPPER(name) IN (?, ?)', ['WIN', 'TP']);
                })->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereHas('hit', function (Builder $q) {
                        $q->whereRaw('UPPER(name) IN (?, ?)', ['WIN', 'TP']);
                    })
                ),

            'lose' => Tab::make('LOSE')
                ->badge(Transaction::whereHas('hit', function (Builder $query) {
                    $query->whereRaw('UPPER(name) IN (?, ?)', ['LOSE', 'SL']);
                })->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereHas('hit', function (Builder $q) {
                        $q->whereRaw('UPPER(name) IN (?, ?)', ['LOSE', 'SL']);
                    })
                ),

            'no_hit' => Tab::make('No HIT')
                ->badge(Transaction::whereNull('hit_id')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('hit_id')),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\TransactionResource\Widgets\TransactionStats::class,
        ];
    }
}
