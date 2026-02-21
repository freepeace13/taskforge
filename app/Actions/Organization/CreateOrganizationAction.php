<?php

namespace App\Actions\Organization;

use App\Models\Organization;

class CreateOrganizationAction
{
    public function execute(int $ownerId, string $name): Organization
    {
        $org = Organization::create([
            'name' => $name,
            'owner_id' => $ownerId
        ]);

        $org->members()->attach($ownerId, ['role' => 'owner']);

        return $org->refresh();
    }
}
