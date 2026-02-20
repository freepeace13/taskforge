<?php

namespace Domains\Organization\Actions;

use Domains\Organization\Models\Organization;
use Domains\Organization\Models\OrganizationMember;

class CreateOrganizationAction
{
    public function execute(int $ownerId, string $name): Organization
    {
        $org = Organization::create([
            'name' => $name,
            'owner_id' => $ownerId
        ]);

        OrganizationMember::create([
            'organization_id' => $org->id,
            'user_id' => $ownerId,
            'role' => 'owner'
        ]);

        return $org->refresh();
    }
}
