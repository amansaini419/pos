<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use App\Filament\Resources\InventoryResource;
use App\Filament\Resources\PurchaseResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;

class ManageInventories extends ManageRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('add')
                ->button()
                ->label('Add Inventory')
                ->url(fn (): string => PurchaseResource::getUrl('create')),
        ];
    }
}
