<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\User;
use App\Policies\ProductGroupPolicy;
use App\Policies\ProductPolicy;
use App\Policies\SubAdminPolicy;
use App\Policies\CustomerPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ProductGroup::class => ProductGroupPolicy::class,
        Product::class => ProductPolicy::class,
        User::class => SubAdminPolicy::class,
        Customer::class => CustomerPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
