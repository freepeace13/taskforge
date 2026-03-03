<?php

namespace Tests\Unit\Project;

use App\Actions\Project\ArchiveProjectAction;
use App\Actions\Project\CreateProjectAction;
use App\Actions\Project\DeleteProjectAction;
use App\Actions\Project\RestoreProjectAction;
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

class ProjectActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_project_action_allows_owner_or_admin(): void
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

    public function test_create_project_action_denies_non_member(): void
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

    public function test_update_project_action_updates_fields_without_changing_organization_for_admin(): void
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

    public function test_update_project_action_denies_non_member(): void
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

    public function test_archive_and_restore_project_actions_toggle_archived_at_for_admin(): void
    {
        $organization = Organization::factory()->create();
        $admin = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $admin->id,
            'role' => Role::Admin->value,
        ]);

        $project = Project::factory()->for($organization)->create([
            'archived_at' => null,
        ]);

        $archiveAction = app(ArchiveProjectAction::class);
        $restoreAction = app(RestoreProjectAction::class);

        $archived = $archiveAction->archive(
            actor: $admin,
            project: $project,
        );

        $this->assertNotNull($archived->archived_at);

        $restored = $restoreAction->restore(
            actor: $admin,
            project: $archived,
        );

        $this->assertNull($restored->archived_at);
    }

    public function test_archive_project_action_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $nonMember = User::factory()->create();
        $project = Project::factory()->for($organization)->create([
            'archived_at' => null,
        ]);

        $action = app(ArchiveProjectAction::class);

        $this->expectException(AuthorizationException::class);

        $action->archive(
            actor: $nonMember,
            project: $project,
        );
    }

    public function test_restore_project_action_denies_non_member(): void
    {
        $organization = Organization::factory()->create();
        $nonMember = User::factory()->create();
        $project = Project::factory()->for($organization)->create([
            'archived_at' => now(),
        ]);

        $action = app(RestoreProjectAction::class);

        $this->expectException(AuthorizationException::class);

        $action->restore(
            actor: $nonMember,
            project: $project,
        );
    }

    public function test_delete_project_action_soft_deletes_for_owner(): void
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

    public function test_delete_project_action_denies_non_member(): void
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
