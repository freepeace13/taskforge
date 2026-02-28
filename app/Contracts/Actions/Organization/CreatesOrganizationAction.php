<?php

namespace App\Contracts\Actions\Organization;

use App\Data\OrganizationData;
use App\Models\Organization;
use App\Models\User;

interface CreatesOrganizationAction
{
    public function create(User $actor, OrganizationData $data): Organization;
}
