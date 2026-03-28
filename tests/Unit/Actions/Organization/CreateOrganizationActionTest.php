<?php

namespace Tests\Unit\Actions\Organization;

use App\Actions\Organization\CreateOrganizationAction;
use App\Data\OrganizationData;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrganizationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_org_and_attaches_owner(): void
    {
        $user = User::factory()->create();

        $action = app(CreateOrganizationAction::class);

        $organization = $action->create(
            actor: $user,
            data: new OrganizationData(
                name: 'Acme Org',
            ),
        );

        $this->assertInstanceOf(Organization::class, $organization);

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'owner_id' => $user->id,
        ]);

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => Role::Owner->value,
        ]);
    }
}
