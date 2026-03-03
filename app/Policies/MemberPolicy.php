<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MemberPolicy
{
    public function viewAny(User $user, Organization $organization)
    {
        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return true;
    }

    public function update(User $user, OrganizationMember $member)
    {
        $organization = $member->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return $user->hasAnyOrganizationRole($organization, [Role::Admin, Role::Owner]);
    }

    public function remove(User $user, OrganizationMember $member)
    {
        $organization = $member->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return $user->hasAnyOrganizationRole($organization, [Role::Admin, Role::Owner]);
    }
}
