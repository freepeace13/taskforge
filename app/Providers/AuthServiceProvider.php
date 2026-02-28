<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Organization::class, \App\Policies\OrganizationPolicy::class);
        Gate::policy(OrganizationInvite::class, \App\Policies\InvitationPolicy::class);
        Gate::policy(OrganizationMember::class, \App\Policies\MemberPolicy::class);
        Gate::policy(Project::class, \App\Policies\ProjectPolicy::class);
        Gate::policy(Task::class, \App\Policies\TaskPolicy::class);
        Gate::policy(Comment::class, \App\Policies\CommentPolicy::class);
    }
}
