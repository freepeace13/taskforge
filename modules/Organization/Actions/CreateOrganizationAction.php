<?php

namespace Modules\Organization\Actions;

use Modules\Organization\Organization;
use Modules\Organization\OrganizationMember;

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
