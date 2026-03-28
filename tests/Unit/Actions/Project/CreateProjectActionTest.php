<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\CreateProjectAction;
use App\Data\ProjectData;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateProjectActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_allows_owner_or_admin(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'role' => Role::Owner->value,
        ]);

        $action = app(CreateProjectAction::class);

        $project = $action->create(
            actor: $owner,
            organization: $organization,
            data: new ProjectData(
                name: 'New Project',
                description: 'Some description',
            ),
        );

        $this->assertInstanceOf(Project::class, $project);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'organization_id' => $organization->id,
            'name' => 'New Project',
            'archived_at' => null,
        ]);
    }

    public function test_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $nonMember = User::factory()->create();

        $action = app(CreateProjectAction::class);

        $this->expectException(AuthorizationException::class);

        $action->create(
            actor: $nonMember,
            organization: $organization,
            data: new ProjectData(
                name: 'New Project',
                description: null,
            ),
        );
    }
}
