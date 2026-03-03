<?php

namespace App\Actions\Organization;

use App\Contracts\Actions\Organization\UpdatesOrganizationAction as UpdatesOrganizationContract;
use App\Data\OrganizationData;
use App\Models\Organization;
use App\Models\User;
use App\Support\AuthorizesActions;

class UpdateOrganizationAction implements UpdatesOrganizationContract
{
    use AuthorizesActions;

    public function update(User $actor, Organization $organization, OrganizationData $data): Organization
    {
        $this->authorizeForUser($actor, 'update', $organization);

        $organization->update([
            'name' => $data->name,
        ]);

        return $organization;
    }
}
