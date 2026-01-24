<?php

namespace App\Filament\Resources\AccountTradeResource\Pages;

use App\Filament\Resources\AccountTradeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAccountTrades extends ManageRecords
{
    protected static string $resource = AccountTradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
