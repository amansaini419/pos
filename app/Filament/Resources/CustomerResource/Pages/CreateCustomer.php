<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['customer_number'] = date('ym') . str_pad(Customer::count() + 1, 6, '0', STR_PAD_LEFT);
        $data['added_by'] = auth()->id();
        if (!isset($data['assigned_to'])) {
            $data['assigned_to'] = auth()->user()->hasRole('sales_agent') ? auth()->id() : 0;
        }
        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('New customer added')
            ->body('The new customer has been added successfully.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
