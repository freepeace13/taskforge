<?php

namespace Tests\Unit\Actions\Organization;

use App\Actions\Organization\DeleteOrganizationAction;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteOrganizationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_delete(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'role' => Role::Owner->value,
        ]);

        $action = app(DeleteOrganizationAction::class);

        $action->delete(
            actor: $owner,
            organization: $organization,
        );

        $this->assertDatabaseMissing('organizations', [
            'id' => $organization->id,
        ]);
    }

    public function test_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $other = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $organization->owner_id,
            'role' => Role::Owner->value,
        ]);

        $action = app(DeleteOrganizationAction::class);

        $this->expectException(AuthorizationException::class);

        $action->delete(
            actor: $other,
            organization: $organization,
        );
    }
}
