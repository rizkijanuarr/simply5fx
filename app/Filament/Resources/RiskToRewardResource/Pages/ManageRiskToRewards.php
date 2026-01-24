<?php

namespace App\Filament\Resources\RiskToRewardResource\Pages;

use App\Filament\Resources\RiskToRewardResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRiskToRewards extends ManageRecords
{
    protected static string $resource = RiskToRewardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
