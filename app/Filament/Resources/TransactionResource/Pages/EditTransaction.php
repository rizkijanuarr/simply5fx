<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /** Lock the form when HIT and Screenshot After are already filled */
    protected function isLocked(): bool
    {
        $record = $this->getRecord();
        return !empty($record->hit_id) && !empty($record->screenshot_after);
    }

    protected function getFormActions(): array
    {
        if ($this->isLocked()) {
            return []; // Hide Save Changes button
        }

        return parent::getFormActions();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Disable all fields visually by passing a flag via the record
        return $data;
    }
}
