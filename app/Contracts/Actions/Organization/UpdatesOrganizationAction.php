<?php

namespace App\Contracts\Actions\Organization;

use App\Data\OrganizationData;
use App\Models\Organization;
use App\Models\User;

interface UpdatesOrganizationAction
{
    public function update(User $actor, Organization $organization, OrganizationData $data): Organization;
}
