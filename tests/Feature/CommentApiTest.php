<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithTenant;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    use InteractsWithTenant, RefreshDatabase;

    public function test_can_list_create_update_and_delete_comments_for_task(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);

        $this->actingAsInOrganization($user, $organization, Role::Owner);

        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create();

        $comment = Comment::factory()->for($task)->for($user)->create();

        $this->getJson(route('api.v1.orgs.projects.tasks.comments.index', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $task->id,
        ]))
            ->assertOk()
            ->assertJsonFragment(['id' => $comment->id]);

        $createResponse = $this->postJson(route('api.v1.orgs.projects.tasks.comments.store', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $task->id,
        ]), [
            'body' => 'New comment',
        ])->assertCreated();

        $createdId = $createResponse->json('id');

        $this->patchJson(route('api.v1.orgs.projects.tasks.comments.update', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $task->id,
            'comment' => $createdId,
        ]), [
            'body' => 'Updated comment',
        ])->assertOk()
            ->assertJsonFragment(['body' => 'Updated comment']);

        $this->deleteJson(route('api.v1.orgs.projects.tasks.comments.destroy', [
            'org' => $organization->slug,
            'project' => $project->id,
            'task' => $task->id,
            'comment' => $createdId,
        ]))
            ->assertNoContent();

        $this->assertSoftDeleted('comments', [
            'id' => $createdId,
        ]);
    }
}
