<?php

namespace App\Actions\Organization;

use App\Contracts\Actions\Organization\RemovesMemberAction as RemovesMemberContract;
use App\Models\Organization;
use App\Models\User;
use App\Support\AuthorizesActions;

class RemoveMemberAction implements RemovesMemberContract
{
    use AuthorizesActions;

    public function remove(User $actor, Organization $organization, int $userId)
    {
        $member = $organization->members()
            ->where('users.id', $userId)
            ->firstOrFail();

        $this->authorizeForUser($actor, 'remove', $member);

        $organization->members()->detach($userId);
    }
}
