<?php

namespace App\Providers;

use App\Actions\Organization\AcceptInvitationAction;
use App\Actions\Organization\InviteUserAction;
use App\Contracts\Actions\Organization\AcceptsInvitationAction;
use App\Contracts\Actions\Organization\InvitesUserAction;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InvitesUserAction::class, InviteUserAction::class);
        $this->app->bind(AcceptsInvitationAction::class, AcceptInvitationAction::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
