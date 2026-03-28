<?php

namespace Tests\Unit\Actions\Comment;

use App\Actions\Comment\CreateCommentAction;
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

class CreateCommentActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_comment_for_member(): void
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

    public function test_blocks_non_member(): void
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
}
