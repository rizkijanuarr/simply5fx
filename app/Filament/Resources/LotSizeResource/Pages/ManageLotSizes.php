<?php

namespace App\Filament\Resources\LotSizeResource\Pages;

use App\Filament\Resources\LotSizeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLotSizes extends ManageRecords
{
    protected static string $resource = LotSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
