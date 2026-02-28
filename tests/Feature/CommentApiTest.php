<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Comment;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_create_update_and_delete_comments_for_task(): void
    {
        [$organization, $user] = $this->createOrganizationAndOwner();
        Sanctum::actingAs($user);

        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => Role::Owner->value,
        ]);

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

    private function createOrganizationAndOwner(): array
    {
        $organization = Organization::factory()->create();
        $owner = User::query()->findOrFail($organization->owner_id);

        $organization->members()->attach($owner->id, ['role' => Role::Owner->value]);

        return [$organization, $owner];
    }
}
