<?php

namespace App\Filament\Resources\StockRequestResource\Pages;

use App\Filament\Resources\StockRequestResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditStockRequest extends EditRecord
{
    protected static string $resource = StockRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Stock request updated')
            ->body('The stock request has been updated successfully.');
    }
}
