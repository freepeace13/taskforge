<?php

namespace App\Contracts\Actions\Organization;

use App\Data\InviteUserData;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;

interface InvitesUserAction
{
    public function invite(User $actor, Organization $organization, InviteUserData $data): OrganizationInvite;
}
