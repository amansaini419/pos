<?php

namespace App\Filament\Resources\WarehouseResource\Pages;

use App\Filament\Resources\StockRequestResource;
use App\Filament\Resources\WarehouseResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;

class ManageWarehouses extends ManageRecords
{
    protected static string $resource = WarehouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('add')
                ->button()
                ->label('Add Inventory')
                ->url(fn (): string => StockRequestResource::getUrl('create'))
                ->visible(auth()->user()->hasPermission('stockRequest:create')),
        ];
    }
}
