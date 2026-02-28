<?php

namespace App\Contracts\Actions\Organization;

use App\Data\MemberData;
use App\Models\OrganizationMember;
use App\Models\User;

interface UpdatesMemberRoleAction
{
    public function update(User $actor, MemberData $data): OrganizationMember;
}
