<?php

namespace App\Actions\Organization;

use App\Enums\Role;
use App\Models\OrganizationInvite;
use Symfony\Component\HttpFoundation\Response;

class RevokeInvitationAction
{
    public function revoke(int $organizationId, Role $actorRole, OrganizationInvite $invite): void
    {
        abort_unless(in_array($actorRole, [Role::Owner, Role::Admin], true), Response::HTTP_FORBIDDEN);
        abort_if($invite->organization_id !== $organizationId, Response::HTTP_NOT_FOUND);
        abort_if(! is_null($invite->accepted_at), Response::HTTP_UNPROCESSABLE_ENTITY, 'Accepted invitations cannot be revoked.');

        $invite->delete();
    }
}
