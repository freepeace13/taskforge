<?php

namespace App\Providers;

use Domains\Organization\Contracts\OrganizationContextResolver;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Organization\EloquentOrganizationContextResolver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrganizationContextResolver::class, EloquentOrganizationContextResolver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
