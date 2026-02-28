<?php

namespace App\Actions\Organization;

use App\Contracts\Actions\Organization\DeletesOrganizationAction as DeletesOrganizationContract;
use App\Models\User;
use App\Models\Organization;
use App\Support\AuthorizesActions;

class DeleteOrganizationAction implements DeletesOrganizationContract
{
    use AuthorizesActions;

    public function delete(User $actor, Organization $organization)
    {
        $this->authorizeForUser($actor, 'delete', $organization);

        $organization->delete();
    }
}
