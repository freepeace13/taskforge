<?php

namespace App\Contracts\Actions\Organization;

use App\Models\OrganizationInvite;
use App\Models\User;

interface CancelsInvitationAction
{
    public function cancel(User $actor, OrganizationInvite $invitation);
}
