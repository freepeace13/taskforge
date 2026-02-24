<?php

namespace App\Models\Concerns;

use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasOrganizations
{
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->using(OrganizationMember::class)
            ->withPivot('role');
    }

    public function ownsOrganization($org): bool
    {
        return $this->organizationRole($org) === Role::Owner;
    }

    public function belongsToOrganization($org): bool
    {
        if (is_null($org)) {
            return false;
        }

        return $this->organizations()
            ->whereKey($org->id)
            ->exists();
    }

    public function organizationRole($org): ?Role
    {
        if (!$this->belongsToOrganization($org)) {
            return null;
        }

        $org = $this->organizations()->whereKey($org->id)->first();

        return $org->pivot->role;
    }
}
