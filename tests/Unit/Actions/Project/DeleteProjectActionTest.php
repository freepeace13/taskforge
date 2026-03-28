<?php

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\DeleteProjectAction;
use App\Enums\Role;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteProjectActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_soft_deletes_for_owner(): void
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'role' => Role::Owner->value,
        ]);

        $project = Project::factory()->for($organization)->create();

        $action = app(DeleteProjectAction::class);

        $action->delete(
            actor: $owner,
            project: $project,
        );

        $this->assertSoftDeleted('projects', [
            'id' => $project->id,
        ]);
    }

    public function test_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $nonMember = User::factory()->create();
        $project = Project::factory()->for($organization)->create();

        $action = app(DeleteProjectAction::class);

        $this->expectException(AuthorizationException::class);

        $action->delete(
            actor: $nonMember,
            project: $project,
        );
    }
}
