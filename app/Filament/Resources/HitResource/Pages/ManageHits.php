<?php

namespace App\Filament\Resources\HitResource\Pages;

use App\Filament\Resources\HitResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHits extends ManageRecords
{
    protected static string $resource = HitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
