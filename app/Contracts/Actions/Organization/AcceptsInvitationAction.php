<?php

namespace App\Contracts\Actions\Organization;

use App\Models\OrganizationInvite;
use App\Models\User;

interface AcceptsInvitationAction
{
    public function accept(User $user, OrganizationInvite $invitation);
}
