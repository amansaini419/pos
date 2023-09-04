<?php

namespace App\Filament\Resources\SubAdminResource\Pages;

use App\Filament\Resources\SubAdminResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSubAdmin extends CreateRecord
{
    protected static string $resource = SubAdminResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('New subadmin added')
            ->body('The new subadmin has been added successfully.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
