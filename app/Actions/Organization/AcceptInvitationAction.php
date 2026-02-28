<?php

namespace App\Actions\Organization;

use App\Contracts\Actions\Organization\AcceptsInvitationAction as AcceptsInvitationContract;
use App\Models\OrganizationInvite;
use App\Models\User;
use App\Support\AuthorizesActions;
use Illuminate\Support\Facades\DB;

class AcceptInvitationAction implements AcceptsInvitationContract
{
    use AuthorizesActions;

    public function accept(User $actor, OrganizationInvite $invite)
    {
        $this->authorizeForUser($actor, 'accept', $invite);

        $this->ensureInvitationValidity($invite);
        $this->ensureNotYetMember($actor, $invite);

        $organization = $invite->organization;

        DB::transaction(function () use ($actor, $organization, $invite) {
            $organization->members()->attach($actor->id, [
                'role' => $invite->role,
            ]);

            $invite->update(['accepted_at' => now()]);
        });
    }

    protected function ensureNotYetMember($user, $invite)
    {
        $member = $invite->organization->members()
            ->where('users.id', $user->id)
            ->first();

        if ($member) {
            if (is_null($invite->accepted_at)) {
                $invite->update(['accepted_at' => $member->created_at]);
            }

            throw new \Exception('User is already a member of this organization.');
        }
    }

    protected function ensureInvitationValidity($invite)
    {
        $expired = ! is_null($invite->expires_at) && $invite->expires_at->isPast();

        if (! is_null($invite->accepted_at) || $expired) {
            throw new \Exception('The invitation has already been accepted or expired.');
        }
    }
}
