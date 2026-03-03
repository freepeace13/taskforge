<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_create_update_and_delete_comments_for_task(): void
    {
        [$organization, $user] = $this->createOrganizationWithMember(Role::Owner);
        Sanctum::actingAs($user);

        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create();

        $comment = Comment::factory()->for($task)->for($user)->create();

        $this->getJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/tasks/'.$task->id.'/comments')
            ->assertOk()
            ->assertJsonFragment(['id' => $comment->id]);

        $createResponse = $this->postJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/tasks/'.$task->id.'/comments', [
            'body' => 'New comment',
        ])->assertCreated();

        $createdId = $createResponse->json('id');

        $this->patchJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/tasks/'.$task->id.'/comments/'.$createdId, [
            'body' => 'Updated comment',
        ])->assertOk()
            ->assertJsonFragment(['body' => 'Updated comment']);

        $this->deleteJson('/api/v1/orgs/'.$organization->slug.'/projects/'.$project->id.'/tasks/'.$task->id.'/comments/'.$createdId)
            ->assertNoContent();

        $this->assertSoftDeleted('comments', [
            'id' => $createdId,
        ]);
    }
}
