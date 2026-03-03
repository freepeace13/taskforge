<?php

namespace App\Actions\Organization;

use App\Contracts\Actions\Organization\CancelsInvitationAction as CancelsInvitationContract;
use App\Models\OrganizationInvite;
use App\Models\User;
use App\Support\AuthorizesActions;

class CancelInvitationAction implements CancelsInvitationContract
{
    use AuthorizesActions;

    public function cancel(User $actor, OrganizationInvite $invitation)
    {
        $this->authorizeForUser($actor, 'cancel', $invitation);

        $invitation->delete();
    }
}
