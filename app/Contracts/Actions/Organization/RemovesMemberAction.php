<?php

namespace App\Contracts\Actions\Organization;

use App\Models\Organization;
use App\Models\User;

interface RemovesMemberAction
{
    public function remove(User $actor, Organization $organization, int $userId);
}
