<?php

namespace App\Actions\Comment;

use App\Contracts\Actions\Comment\CreatesCommentAction as CreatesCommentContract;
use App\Data\CommentData;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;

class CreateCommentAction implements CreatesCommentContract
{
    public function create(User $actor, Task $task, CommentData $data): Comment
    {
        $project = $task->project;

        if (!$actor->belongsToOrganization($project->organization)) {
            throw new \Exception('You are not a member of this organization.');
        }

        return $task->comments()->create([
            'task_id' => $task->id,
            'user_id' => $actor->id,
            'body' => $data->body,
        ]);
    }
}
