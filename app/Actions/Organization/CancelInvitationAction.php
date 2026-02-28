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

        if (! is_null($invitation->accepted_at)) {
            throw new \Exception('Accepted invitations cannot be revoked.');
        }

        $invitation->delete();
    }
}
