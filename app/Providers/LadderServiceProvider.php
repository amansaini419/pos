<?php

namespace App\Providers;

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
        Ladder::role('admin', 'Administrator', [
            'customer:create', 'customer:read', 'customer:update', 'customer:delete',
            'product:create', 'product:read', 'product:update', 'product:delete',
        ])->description('Administrator users can perform any action.');

        Ladder::role('sales_agent', 'Sales Agent', [
            'customer:read', 'customer:create',
        ])->description('Sales Agent users have the ability to read and create the customer, .');
    }
}
