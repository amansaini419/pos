<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Enums\Customer\CustomerStatusEnum;
use App\Enums\SubAdmin\SubAdminRoleEnum;
use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
//use Ladder\HasRoles;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        //auth()->user()->roles()->updateOrCreate(['role' => 'admin']);
        //dd(User::all()->load('roles')->where('role', 'like', 'sales_agent')->pluck('name', 'id'));
        //dd(auth()->user()->hasRole('admin'));
        //dd(auth()->user()->roles()->updateOrCreate(['role' => 'admin']));
        return [
            'all' => Tab::make('All Customers')
            /* ->badge(Customer::query()->count()) */,
            'pending' => Tab::make('Pending Customers')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('customer_status', CustomerStatusEnum::Pending))
            /* ->badge(Customer::query()->where('customer_status', CustomerStatusEnum::Pending)->count()) */,
            'approved' => Tab::make('Approved Customers')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('customer_status', CustomerStatusEnum::Approved))
            /* ->badge(Customer::query()->where('customer_status', CustomerStatusEnum::Approved)->count()) */,
            'blacklisted' => Tab::make('Blacklisted Customers')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('customer_status', CustomerStatusEnum::Blacklisted))
            /* ->badge(Customer::query()->where('customer_status', CustomerStatusEnum::Blacklisted)->count()) */,
        ];
    }
}
