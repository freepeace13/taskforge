<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    public function create(User $user, Organization $organization)
    {
        if (! $user->belongsToOrganization($organization)) {
            return Response::deny('You are not a member of this organization.');
        }

        return $user->hasAnyOrganizationRole($organization, [Role::Admin, Role::Owner]);
    }

    public function view(User $user, Project $project)
    {
        $organization = $project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return true;
    }

    public function update(User $user, Project $project)
    {
        $organization = $project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return $user->hasAnyOrganizationRole($organization, [Role::Admin, Role::Owner]);
    }

    public function delete(User $user, Project $project)
    {
        $organization = $project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return $user->hasAnyOrganizationRole($organization, [Role::Admin, Role::Owner]);
    }

    public function archive(User $user, Project $project)
    {
        $organization = $project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return $user->hasAnyOrganizationRole($organization, [Role::Admin, Role::Owner]);
    }

    public function restore(User $user, Project $project)
    {
        $organization = $project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return $user->hasAnyOrganizationRole($organization, [Role::Admin, Role::Owner]);
    }
}
