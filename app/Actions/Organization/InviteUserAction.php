<?php

namespace App\Actions\Organization;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class InviteUserAction
{
    /**
     * @return array{invite: OrganizationInvite, accept_url: string}
     */
    public function invite(Organization $organization, User $inviter, Role $actorRole, string $email, Role $role): array
    {
        abort_unless(in_array($actorRole, [Role::Owner, Role::Admin], true), Response::HTTP_FORBIDDEN);

        $isExistingMember = $organization->members()
            ->where('users.email', $email)
            ->exists();

        abort_if($isExistingMember, Response::HTTP_UNPROCESSABLE_ENTITY, 'This user is already a member.');

        $hasActiveInvite = $organization->invites()
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();

        abort_if($hasActiveInvite, Response::HTTP_UNPROCESSABLE_ENTITY, 'An active invitation already exists for this email.');

        $invite = $organization->invites()->create([
            'invited_by_user_id' => $inviter->id,
            'email' => $email,
            'role' => $role->value,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        $acceptUrl = URL::temporarySignedRoute(
            name: 'invitations.accept',
            expiration: $invite->expires_at ?? now()->addDays(7),
            parameters: [
                'token' => $invite->token,
                'email' => $invite->email,
            ],
        );

        return [
            'invite' => $invite,
            'accept_url' => $acceptUrl,
        ];
    }
}
