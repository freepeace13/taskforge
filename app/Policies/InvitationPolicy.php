<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvitationPolicy
{
    public function viewAny(User $user, Organization $organization)
    {
        if (! $user->belongsToOrganization($organization)) {
            return Response::deny('You are not a member of this organization.');
        }

        return $user->hasAnyOrganizationRole($organization, [Role::Admin, Role::Owner]);
    }

    public function invite(User $user, Organization $organization)
    {
        if (! $user->belongsToOrganization($organization)) {
            return Response::deny('You are not a member of this organization.');
        }

        return $user->hasAnyOrganizationRole($organization, [Role::Admin, Role::Owner]);
    }

    public function accept(User $user, OrganizationInvite $invitation)
    {
        if ($invitation->email !== $user->email) {
            return Response::denyAsNotFound();
        }

        if (! is_null($invitation->expires_at) && $invitation->expires_at->isPast()) {
            return Response::deny('This invitation has expired.');
        } elseif (! is_null($invitation->accepted_at)) {
            return Response::deny('This invitation has already been accepted.');
        }

        return true;
    }

    public function cancel(User $user, OrganizationInvite $invitation)
    {
        $organization = $invitation->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::deny('You are not a member of this organization.');
        }

        if (! is_null($invitation->accepted_at)) {
            return Response::deny('This invitation has already been accepted.');
        }

        return $user->hasAnyOrganizationRole($organization, [Role::Admin, Role::Owner]);
    }
}
