<?php

namespace App\Filament\Resources\SubAdminResource\Pages;

use App\Filament\Resources\SubAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubAdmin extends EditRecord
{
    protected static string $resource = SubAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
