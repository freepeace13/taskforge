<?php

namespace App\Actions\Organization;

use App\Models\OrganizationInvite;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AcceptInvitationAction
{
    public function accept(User $user, OrganizationInvite $invite): OrganizationInvite
    {
        $this->ensureIsPending($invite);
        $this->ensureNotExpire($invite);
        $this->ensureUserEmailMatched($user, $invite);
        $this->ensureNotYetMember($user, $invite);

        $invite->organization->members()->attach($user->id, [
            'role' => $invite->role
        ]);

        $invite->update(['accepted_at' => now()]);

        return $invite->refresh();
    }

    protected function ensureNotYetMember($user, $invite)
    {
        $isMember = $invite->organization->members()
            ->where('users.id', $user->id)
            ->exists();

        abort_if($isMember, Response::HTTP_UNPROCESSABLE_ENTITY, 'User is already a member of this organization.');
    }

    protected function ensureUserEmailMatched($user, $invite)
    {
        $mismatch = $invite->email !== $user->email;
        abort_if($mismatch, Response::HTTP_UNPROCESSABLE_ENTITY, 'Invitation email mismatch.');
    }

    protected function ensureIsPending($invite)
    {
        $accepted = !is_null($invite->accepted_at);
        abort_if($accepted, Response::HTTP_UNPROCESSABLE_ENTITY, __('This invitation has already been accepted.'));
    }

    protected function ensureNotExpire($invite)
    {
        $expired = !is_null($invite->expires_at) && $invite->expires_at->isPast();
        abort_if($expired, Response::HTTP_UNPROCESSABLE_ENTITY, 'This invitation has expired.');
    }
}
