<?php

namespace App\Providers;

use App\Enums\SubAdmin\SubAdminRoleEnum;
use Illuminate\Support\ServiceProvider;
use Ladder\Ladder;

class LadderServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Ladder::role(SubAdminRoleEnum::ADMIN->value, SubAdminRoleEnum::ADMIN->name, [
            'subadmin', 'productGroup', 'product', 'purchase', 'inventory', 'warehouse',

            'customer:viewAny', 'customer:view', 'customer:create', 'customer:update', 'customer:delete', 'customer:viewAssignedToColumn', 'customer:viewAssignedToFilter', 'customer:approve', 'customer:blacklist', 'customer:assign', 'customer:viewAssignedToField',

            'sale:viewAny', 'sale:view', 'sale:create', 'sale:update', 'sale:delete', 'sale:viewAssignedToColumn', 'sale:viewAssignedToFilter', 'sale:approve', 'sale:reject',

            'stockRequest:viewAny', 'stockRequest:view', 'stockRequest:update', 'stockRequest:delete', 'stockRequest:viewRequestedByColumn', 'stockRequest:viewRequestedByFilter', 'stockRequest:approve', 'stockRequest:reject',
        ])->description('Administrator users can perform any action.');

        Ladder::role(SubAdminRoleEnum::SALESAGENT->value, SubAdminRoleEnum::SALESAGENT->name, [
            'customer:viewAny', 'customer:create',
            'sale:viewAny', 'sale:create',
            'stockRequest:viewAny', 'stockRequest:create',
            'warehouse:viewAny',
        ])->description('Sales Agent users have the ability to create customers, create sales, request stock.');
    }
}
