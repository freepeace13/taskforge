<?php

namespace Tests\Unit\Actions\Organization;

use App\Actions\Organization\UpdateOrganizationAction;
use App\Data\OrganizationData;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateOrganizationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'role' => Role::Owner->value,
        ]);

        $action = app(UpdateOrganizationAction::class);

        $updated = $action->update(
            actor: $owner,
            organization: $organization,
            data: new OrganizationData(
                name: 'Updated Name',
            ),
        );

        $this->assertSame('Updated Name', $updated->name);
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

        $action = app(UpdateOrganizationAction::class);

        $this->expectException(AuthorizationException::class);

        $action->update(
            actor: $other,
            organization: $organization,
            data: new OrganizationData(
                name: 'Should Fail',
            ),
        );
    }
}
