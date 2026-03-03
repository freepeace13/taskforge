<?php

namespace App\Actions\Organization;

use App\Contracts\Actions\Organization\RemovesMemberAction as RemovesMemberContract;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use App\Support\AuthorizesActions;
use Illuminate\Validation\ValidationException;

class RemoveMemberAction implements RemovesMemberContract
{
    use AuthorizesActions;

    public function remove(User $actor, Organization $organization, int $userId)
    {
        $user = $organization->members()
            ->where('users.id', $userId)
            ->firstOrFail();

        $member = $user->pivot;

        $this->authorizeForUser($actor, 'remove', $member);

        if ($member->role === Role::Owner) {
            throw ValidationException::withMessages([
                'user_id' => ['The owner cannot be removed from the organization.'],
            ]);
        }

        $organization->members()->detach($userId);
    }
}
