<?php

namespace Tests\Unit\Actions\Comment;

use App\Actions\Comment\DeleteCommentAction;
use App\Enums\Role;
use App\Models\Comment;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCommentActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_soft_deletes_for_author(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create();
        $author = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $author->id,
            'role' => Role::Member->value,
        ]);

        $comment = Comment::factory()->for($task)->create([
            'user_id' => $author->id,
        ]);

        $action = app(DeleteCommentAction::class);

        $action->delete(
            comment: $comment,
            actor: $author,
        );

        $this->assertSoftDeleted('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_denies_non_author(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create();
        $author = User::factory()->create();
        $other = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $author->id,
            'role' => Role::Member->value,
        ]);
        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $other->id,
            'role' => Role::Member->value,
        ]);

        $comment = Comment::factory()->for($task)->create([
            'user_id' => $author->id,
        ]);

        $action = app(DeleteCommentAction::class);

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('You are not allowed to delete this comment.');

        $action->delete(
            comment: $comment,
            actor: $other,
        );
    }
}
