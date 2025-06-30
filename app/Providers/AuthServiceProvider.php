<?php

namespace App\Providers;

use App\Models\Customer; 
use App\Models\CustomerLog;
use App\Policies\CustomerPolicy; 
use App\Policies\CustomerLogPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        CustomerLog::class => CustomerLogPolicy::class,
        Customer::class => CustomerPolicy::class, // NEW
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
