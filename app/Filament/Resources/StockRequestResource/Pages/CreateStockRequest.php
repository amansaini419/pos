<?php

namespace App\Filament\Resources\StockRequestResource\Pages;

use App\Filament\Resources\StockRequestResource;
use App\Models\StockRequest;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateStockRequest extends CreateRecord
{
    protected static string $resource = StockRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['request_number'] = 'REQ-' . date('ym') . str_pad((StockRequest::latest('id')->first()->id ?? 0) + 1, 6, '0', STR_PAD_LEFT);
        $data['requested_by'] = auth()->id();
        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('New stock request added')
            ->body('The new stock request has been added successfully.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
