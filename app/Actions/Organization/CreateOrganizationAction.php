<?php

namespace App\Actions\Organization;

use App\Contracts\Actions\Organization\CreatesOrganizationAction as CreatesOrganizationContract;
use App\Data\OrganizationData;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use App\Support\AuthorizesActions;
use Illuminate\Support\Str;

class CreateOrganizationAction implements CreatesOrganizationContract
{
    use AuthorizesActions;

    public function create(User $actor, OrganizationData $data): Organization
    {
        $this->authorizeForUser($actor, 'create', Organization::class);

        $organization = Organization::create([
            'name' => $data->name,
            'slug' => Str::slug($data->name),
            'owner_id' => $actor->id,
        ]);

        $organization->members()->attach($actor, [
            'role' => Role::Owner
        ]);

        return $organization;
    }
}
