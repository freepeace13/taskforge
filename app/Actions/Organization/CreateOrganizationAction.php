<?php

namespace App\Actions\Organization;

use App\Enums\Role;
use App\Models\Organization;
use Illuminate\Support\Str;

class CreateOrganizationAction
{
    public function create(int $ownerId, string $name): Organization
    {
        $org = Organization::create([
            'name' => $name,
            'slug' => Str::slug($name),
            'owner_id' => $ownerId,
        ]);

        $org->members()->attach($ownerId, ['role' => Role::Owner]);

        return $org->refresh();
    }
}
