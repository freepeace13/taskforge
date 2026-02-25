<?php

namespace App\Actions\Organization;

use App\Enums\Role;
use App\Models\OrganizationMember;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class UpdateMemberRoleAction
{
    public function update(int $organizationId, Role $actorRole, User $member, Role $role): OrganizationMember
    {
        abort_unless(in_array($actorRole, [Role::Owner, Role::Admin], true), Response::HTTP_FORBIDDEN);

        $organizationMember = OrganizationMember::query()
            ->where('organization_id', $organizationId)
            ->where('user_id', $member->id)
            ->firstOrFail();

        abort_if($organizationMember->role === Role::Owner, Response::HTTP_UNPROCESSABLE_ENTITY, 'Owner role cannot be changed.');

        $organizationMember->update([
            'role' => $role->value,
        ]);

        return $organizationMember->refresh();
    }
}
