<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\UpdateProjectAction;
use App\Data\ProjectData;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateProjectActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_fields_without_changing_organization_for_admin(): void
    {
        $organization = Organization::factory()->create();
        $admin = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $admin->id,
            'role' => Role::Admin->value,
        ]);

        $project = Project::factory()->for($organization)->create([
            'name' => 'Old Name',
            'description' => 'Old description',
        ]);

        $action = app(UpdateProjectAction::class);

        $updated = $action->update(
            actor: $admin,
            project: $project,
            data: new ProjectData(
                name: 'Updated Name',
                description: 'Updated description',
            ),
        );

        $this->assertSame('Updated Name', $updated->name);
        $this->assertSame('Updated description', $updated->description);
        $this->assertSame($organization->id, $updated->organization_id);
    }

    public function test_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $nonMember = User::factory()->create();
        $project = Project::factory()->for($organization)->create();

        $action = app(UpdateProjectAction::class);

        $this->expectException(AuthorizationException::class);

        $action->update(
            actor: $nonMember,
            project: $project,
            data: new ProjectData(
                name: 'Updated Name',
                description: null,
            ),
        );
    }
}
