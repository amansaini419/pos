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
            'subadmin', 'productGroup', 'product',
            'customer:viewAny', 'customer:view', 'customer:create', 'customer:update', 'customer:delete', 'customer:viewAssignedToColumn', 'customer:viewAssignedToFilter', 'customer:approve', 'customer:blacklist', 'customer:assign', 'customer:viewAssignedToField',
            'product:create', 'product:read', 'product:update', 'product:delete',
        ])->description('Administrator users can perform any action.');

        Ladder::role(SubAdminRoleEnum::SALESAGENT->value, SubAdminRoleEnum::SALESAGENT->name, [
            'customer:viewAny', 'customer:create',
            'sale:read', 'sale:create',
        ])->description('Sales Agent users have the ability to read and create the customer, .');
    }
}
