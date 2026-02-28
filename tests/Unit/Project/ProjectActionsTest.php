<?php

namespace Tests\Unit\Project;

use App\Actions\Project\ArchiveProjectAction;
use App\Actions\Project\CreateProjectAction;
use App\Actions\Project\DeleteProjectAction;
use App\Actions\Project\RestoreProjectAction;
use App\Actions\Project\UpdateProjectAction;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ProjectActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_project_action_creates_project_for_organization(): void
    {
        $organization = Organization::factory()->create();

        $action = app(CreateProjectAction::class);

        $project = $action->create(
            organizationId: $organization->id,
            name: 'New Project',
            description: 'Some description',
        );

        $this->assertInstanceOf(Project::class, $project);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'organization_id' => $organization->id,
            'name' => 'New Project',
            'archived_at' => null,
        ]);
    }

    public function test_update_project_action_updates_fields_without_changing_organization(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create([
            'name' => 'Old Name',
            'description' => 'Old description',
        ]);

        $action = app(UpdateProjectAction::class);

        $updated = $action->update($project, [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ]);

        $this->assertSame('Updated Name', $updated->name);
        $this->assertSame('Updated description', $updated->description);
        $this->assertSame($organization->id, $updated->organization_id);
    }

    public function test_archive_and_restore_project_actions_toggle_archived_at(): void
    {
        $project = Project::factory()->create([
            'archived_at' => null,
        ]);

        $archiveAction = app(ArchiveProjectAction::class);
        $restoreAction = app(RestoreProjectAction::class);

        $archived = $archiveAction->archive($project);

        $this->assertNotNull($archived->archived_at);

        $restored = $restoreAction->restore($archived);

        $this->assertNull($restored->archived_at);
    }

    public function test_archive_project_action_fails_when_already_archived(): void
    {
        $project = Project::factory()->create([
            'archived_at' => now(),
        ]);

        $action = app(ArchiveProjectAction::class);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage('Project is already archived.');

        $action->archive($project);
    }

    public function test_restore_project_action_fails_when_not_archived(): void
    {
        $project = Project::factory()->create([
            'archived_at' => null,
        ]);

        $action = app(RestoreProjectAction::class);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage('Project is not archived.');

        $action->restore($project);
    }

    public function test_delete_project_action_soft_deletes_project(): void
    {
        $project = Project::factory()->create();

        $action = app(DeleteProjectAction::class);

        $action->delete($project);

        $this->assertSoftDeleted('projects', [
            'id' => $project->id,
        ]);
    }
}
