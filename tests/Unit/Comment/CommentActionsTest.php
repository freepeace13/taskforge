<?php

namespace Tests\Unit\Comment;

use App\Actions\Comment\CreateCommentAction;
use App\Actions\Comment\DeleteCommentAction;
use App\Actions\Comment\UpdateCommentAction;
use App\Enums\Role;
use App\Models\Comment;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
            task: $task,
            user: $user,
            body: 'A comment',
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

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(Response::HTTP_FORBIDDEN);
        $this->expectExceptionMessage('You are not a member of this organization.');

        $action->create(
            task: $task,
            user: $user,
            body: 'A comment',
        );
    }

    public function test_update_comment_action_allows_author_and_blocks_others(): void
    {
        $author = User::factory()->create();
        $other = User::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $author->id,
            'body' => 'Old',
        ]);

        $action = app(UpdateCommentAction::class);

        $updated = $action->update(
            comment: $comment,
            actor: $author,
            body: 'New',
        );

        $this->assertSame('New', $updated->body);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(Response::HTTP_FORBIDDEN);
        $this->expectExceptionMessage('You are not allowed to update this comment.');

        $action->update(
            comment: $comment,
            actor: $other,
            body: 'Other',
        );
    }

    public function test_delete_comment_action_soft_deletes_for_author_only(): void
    {
        $author = User::factory()->create();
        $other = User::factory()->create();
        $comment = Comment::factory()->create([
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

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(Response::HTTP_FORBIDDEN);
        $this->expectExceptionMessage('You are not allowed to delete this comment.');

        $anotherComment = Comment::factory()->create([
            'user_id' => $author->id,
        ]);

        $action->delete(
            comment: $anotherComment,
            actor: $other,
        );
    }
}
