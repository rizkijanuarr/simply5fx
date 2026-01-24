<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $title = 'Dashboard';

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('account_trade_id')
                            ->label('Account')
                            ->options(fn () => \App\Models\AccountTrade::pluck('name', 'id')->toArray())
                            ->placeholder('All accounts')
                            ->searchable()
                            ->preload(),
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->native(false)
                            ->maxDate(fn (Get $get) => $get('end_date') ?: now())
                            ->default(now()->startOfMonth()),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->minDate(fn (Get $get) => $get('start_date') ?: now())
                            ->native(false)
                            ->maxDate(now())
                            ->default(now()),
                    ])
                    ->columns(3),
            ]);
    }
}
