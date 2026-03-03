<?php

namespace App\Actions\Organization;

use App\Contracts\Actions\Organization\InvitesUserAction as InvitesUserContract;
use App\Data\InviteUserData;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use App\Support\AuthorizesActions;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InviteUserAction implements InvitesUserContract
{
    use AuthorizesActions;

    public function invite(User $actor, Organization $organization, InviteUserData $data): OrganizationInvite
    {
        $this->authorizeForUser($actor, 'invite', [OrganizationInvite::class, $organization]);

        if ($organization->members()
            ->where('users.email', $data->email)
            ->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This user is already a member.',
            ]);
        }

        if ($organization->invites()
            ->pending()
            ->where('email', $data->email)
            ->exists()) {
            throw ValidationException::withMessages([
                'email' => 'An active invitation already exists for this email.',
            ]);
        }

        $invitation = $organization->invites()->create([
            'invited_by_user_id' => $actor->id,
            'email' => $data->email,
            'role' => $data->role,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        return $invitation;
    }
}
