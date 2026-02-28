<?php

namespace App\Actions\Organization;

use App\Contracts\Actions\Organization\UpdatesMemberRoleAction as UpdatesMemberRoleContract;
use App\Data\MemberData;
use App\Models\OrganizationMember;
use App\Models\User;
use App\Support\AuthorizesActions;

class UpdateMemberRoleAction implements UpdatesMemberRoleContract
{
    use AuthorizesActions;

    public function update(User $actor, MemberData $data): OrganizationMember
    {
        $member = OrganizationMember::query()
            ->where('organization_id', $data->organizationId)
            ->where('user_id', $data->userId)
            ->firstOrFail();

        $this->authorizeForUser($actor, 'update', $member);

        $member->update([
            'role' => $data->role,
        ]);

        return $member;
    }
}
