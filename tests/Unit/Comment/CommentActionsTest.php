<?php

namespace Tests\Unit\Comment;

use App\Actions\Comment\CreateCommentAction;
use App\Actions\Comment\DeleteCommentAction;
use App\Actions\Comment\UpdateCommentAction;
use App\Data\CommentData;
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

class CommentActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_comment_action_creates_comment_for_member(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create();
        $user = User::factory()->create();

        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => Role::Member->value,
        ]);

        $action = app(CreateCommentAction::class);

        $comment = $action->create(
            actor: $user,
            task: $task,
            data: new CommentData(body: 'A comment'),
        );

        $this->assertInstanceOf(Comment::class, $comment);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'task_id' => $task->id,
            'user_id' => $user->id,
            'body' => 'A comment',
        ]);
    }

    public function test_create_comment_action_blocks_non_member(): void
    {
        $organization = Organization::factory()->create();
        $project = Project::factory()->for($organization)->create();
        $task = Task::factory()->for($project)->create();
        $user = User::factory()->create();

        $action = app(CreateCommentAction::class);

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('You are not a member of this organization.');

        $action->create(
            actor: $user,
            task: $task,
            data: new CommentData(body: 'A comment'),
        );
    }

    public function test_update_comment_action_allows_author_and_blocks_others(): void
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
            'body' => 'Old',
        ]);

        $action = app(UpdateCommentAction::class);

        $updated = $action->update(
            comment: $comment,
            actor: $author,
            data: new CommentData(body: 'New'),
        );

        $this->assertSame('New', $updated->body);

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('You are not allowed to update this comment.');

        $action->update(
            comment: $comment,
            actor: $other,
            data: new CommentData(body: 'Other'),
        );
    }

    public function test_delete_comment_action_soft_deletes_for_author_only(): void
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

        $action->delete(
            comment: $comment,
            actor: $author,
        );

        $this->assertSoftDeleted('comments', [
            'id' => $comment->id,
        ]);

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('You are not allowed to delete this comment.');

        $anotherComment = Comment::factory()->for($task)->create([
            'user_id' => $author->id,
        ]);

        $action->delete(
            comment: $anotherComment,
            actor: $other,
        );
    }
}
