<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\Auth\AuthServerTokenValidator;
use App\Services\Auth\TechysavvyOAuthProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerPolicies();
        $this->configureTechysavvyAuthentication();

        Gate::define('viewApiDocs', function (?User $user = null): bool {
            return true;
        });
    }

    protected function registerPolicies(): void
    {
        Gate::policy(Organization::class, \App\Policies\OrganizationPolicy::class);
        Gate::policy(OrganizationInvite::class, \App\Policies\InvitationPolicy::class);
        Gate::policy(OrganizationMember::class, \App\Policies\MemberPolicy::class);
        Gate::policy(Project::class, \App\Policies\ProjectPolicy::class);
        Gate::policy(Task::class, \App\Policies\TaskPolicy::class);
        Gate::policy(Comment::class, \App\Policies\CommentPolicy::class);
    }

    protected function configureTechysavvyAuthentication(): void
    {
        Socialite::extend('techysavvy', function ($app) {
            $config = $app['config']['services.techysavvy'];

            return Socialite::buildProvider(TechysavvyOAuthProvider::class, $config);
        });

        Auth::viaRequest('techysavvy', function ($request) {
            $token = $request->bearerToken();

            try {
                $authId = $this->app->make(AuthServerTokenValidator::class)->validate($token);

                return User::firstWhere('auth_id', $authId);
            } catch (\Exception $e) {
                Log::error('Error validating token: ' . $e->getMessage());
                return null;
            }
        });
    }


}
