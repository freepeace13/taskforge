<?php

namespace App\Actions\Organization;

use App\Contracts\Actions\Organization\UpdatesMemberRoleAction as UpdatesMemberRoleContract;
use App\Data\MemberData;
use App\Enums\Role;
use App\Models\OrganizationMember;
use App\Models\User;
use App\Support\AuthorizesActions;
use Illuminate\Validation\ValidationException;

class UpdateMemberRoleAction implements UpdatesMemberRoleContract
{
    use AuthorizesActions;

    public function update(User $actor, MemberData $data): OrganizationMember
    {
        $member = OrganizationMember::query()
            ->where('organization_id', $data->organization->id)
            ->where('user_id', $data->user->id)
            ->firstOrFail();

        $this->authorizeForUser($actor, 'update', $member);

        if ($member->role === Role::Owner) {
            throw ValidationException::withMessages([
                'role' => ['The owner\'s role cannot be changed.'],
            ]);
        }

        $member->update([
            'role' => $data->role,
        ]);

        return $member;
    }
}
