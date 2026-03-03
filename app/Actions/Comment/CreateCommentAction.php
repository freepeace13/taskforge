<?php

namespace App\Actions\Comment;

use App\Contracts\Actions\Comment\CreatesCommentAction as CreatesCommentContract;
use App\Data\CommentData;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Support\AuthorizesActions;

class CreateCommentAction implements CreatesCommentContract
{
    use AuthorizesActions;

    public function create(User $actor, Task $task, CommentData $data): Comment
    {
        $this->authorizeForUser($actor, 'create', [Comment::class, $task]);

        return $task->comments()->create([
            'task_id' => $task->id,
            'user_id' => $actor->id,
            'body' => $data->body,
        ]);
    }
}
