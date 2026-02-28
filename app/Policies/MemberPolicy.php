<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MemberPolicy
{
    public function update(User $user, OrganizationMember $member)
    {
        $organization = $member->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        if ($member->user->isNot($user)) {
            return $user->organizationRole($organization) === Role::Owner;
        }

        return $user->hasAnyOrganizationRole($organization, [Role::Admin, Role::Owner]);
    }

    public function remove(User $user, OrganizationMember $member)
    {
        $organization = $member->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        if ($member->role === Role::Owner) {
            return Response::deny('Cannot remove owners from the organization.');
        }

        return $user->hasAnyOrganizationRole($organization, [Role::Admin, Role::Owner]);
    }
}
