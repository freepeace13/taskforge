<?php

namespace App\Actions\Comment;

use App\Contracts\Actions\Comment\UpdatesCommentAction as UpdatesCommentContract;
use App\Data\CommentData;
use App\Models\Comment;
use App\Models\User;

class UpdateCommentAction implements UpdatesCommentContract
{
    public function update(User $actor, Comment $comment, CommentData $data): Comment
    {
        $project = $comment->task->project;

        if (!$actor->belongsToOrganization($project->organization)) {
            throw new \Exception('You are not a member of this organization.');
        }

        if ($actor->isNot($comment->user)) {
            throw new \Exception('You cannot update someone else comment!');
        }

        $comment->update(['body' => $data->body]);

        return $comment->refresh();
    }
}
