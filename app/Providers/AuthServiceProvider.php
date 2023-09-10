<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models;
use App\Policies;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Models\ProductGroup::class => Policies\ProductGroupPolicy::class,
        Models\Product::class => Policies\ProductPolicy::class,
        Models\User::class => Policies\SubAdminPolicy::class,
        Models\Customer::class => Policies\CustomerPolicy::class,
        Models\Sale::class => Policies\SalePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
