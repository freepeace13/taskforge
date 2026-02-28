<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy
{
    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Organization $organization)
    {
        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return $user->hasAnyOrganizationRole($organization, [Role::Owner, Role::Admin]);
    }

    public function delete(User $user, Organization $organization)
    {
        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return $user->organizationRole($organization) === Role::Owner;
    }
}
