<?php

namespace Modules\Organization;

use Illuminate\Support\ServiceProvider;
use Modules\Organization\Contracts\OrganizationContextResolver;

class OrganizationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(OrganizationContextResolver::class, EloquentOrganizationContextResolver::class);
    }

    public function boot()
    {
        //
    }
}
